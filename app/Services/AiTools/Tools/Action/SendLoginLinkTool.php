<?php

namespace App\Services\AiTools\Tools\Action;

use App\Jobs\AddMailToQueueJob;
use App\Services\AiTools\AiToolResult;
use App\Services\AiTools\Contracts\AiToolInterface;
use App\Services\AiTools\Exceptions\ToolValidationException;
use App\User;
use Illuminate\Support\Facades\Cache;

/**
 * Action-tool: sender en magisk innloggingslenke til eleven.
 *
 * Støtter både elever (role 2) → forfatterskolen.no og
 * redaktører (role 3) → editor.forfatterskolen.no.
 *
 * Idempotent via en 5-min cache-nøkkel slik at dobbeltklikk ikke
 * sender to e-poster.
 */
class SendLoginLinkTool implements AiToolInterface
{
    public function name(): string
    {
        return 'send_login_link';
    }

    public function definition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Sender en magisk innloggingslenke til brukeren via e-post. Lenken logger dem inn uten passord. Bruker riktig subdomene basert på rollen til brukeren (elever → forfatterskolen.no, redaktører → editor.forfatterskolen.no). Bruk dette når eleven har innloggingsproblemer.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'user_id' => [
                        'type' => 'integer',
                        'description' => 'ID-en til brukeren som skal få innloggingslenken',
                    ],
                ],
                'required' => ['user_id'],
            ],
        ];
    }

    public function isAction(): bool
    {
        return true;
    }

    public function validate(array $params): void
    {
        if (empty($params['user_id']) || !is_numeric($params['user_id'])) {
            throw new ToolValidationException('user_id må være et heltall');
        }
    }

    public function describeForUi(array $params): string
    {
        $user = User::find((int) $params['user_id']);
        $name = $user ? trim($user->first_name . ' ' . $user->last_name) : "bruker #{$params['user_id']}";
        return "Send innloggingslenke til {$name}";
    }

    public function execute(array $params, User $executor): AiToolResult
    {
        $user = User::find((int) $params['user_id']);
        if (!$user) {
            return AiToolResult::failure('Fant ikke bruker', 'USER_NOT_FOUND');
        }

        if (!$user->is_active) {
            return AiToolResult::failure('Brukeren er inaktiv', 'USER_INACTIVE');
        }

        // Idempotens: ikke send ny e-post hvis vi allerede har sendt én siste 5 min
        $cacheKey = "login_link_sent:user:{$user->id}";
        if (Cache::has($cacheKey)) {
            return AiToolResult::success(
                'Innloggingslenke er allerede sendt siste 5 minutter — venter før vi sender en til',
                ['user_id' => $user->id, 'already_sent' => true]
            );
        }

        try {
            $encryptedEmail = encrypt($user->email);

            // Velg riktig rute basert på rolle
            if ($user->role == 3 || $user->admin_with_editor_access) {
                $loginUrl = route('editor.login.email', $encryptedEmail);
                $portalName = 'redaktørportalen';
            } else {
                $loginUrl = route('auth.login.email', $encryptedEmail);
                $portalName = 'Forfatterskolen';
            }

            $subject = 'Innloggingslenke til ' . $portalName;
            $body = '<p>Hei ' . e($user->first_name) . ',</p>'
                . '<p>Her er en direkte innloggingslenke til ' . $portalName . ':</p>'
                . '<p><a href="' . $loginUrl . '">Logg inn her</a></p>'
                . '<p>Hvis knappen ikke fungerer, åpne lenken i et privat/inkognito-vindu (Cmd+Shift+N på Mac, Ctrl+Shift+N på Windows) — da er vi fri fra gammel nettleser-cache.</p>'
                . '<p>Ha en fin dag!<br>Mvh Forfatterskolen</p>';

            dispatch(new AddMailToQueueJob(
                $user->email,
                $subject,
                $body,
                'post@forfatterskolen.no',
                'Forfatterskolen',
                null,
                'login_link',
                $user->id
            ));

            Cache::put($cacheKey, true, now()->addMinutes(5));

            return AiToolResult::success(
                "Innloggingslenke sendt til {$user->email}",
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'portal' => $portalName,
                    // Lenken lagres IKKE i result — den er hemmelig
                ]
            );
        } catch (\Throwable $e) {
            return AiToolResult::failure('Kunne ikke sende e-post: ' . $e->getMessage(), 'EMAIL_FAILED');
        }
    }
}
