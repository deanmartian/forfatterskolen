<?php

namespace App\Services\AiTools\Tools\Action;

use App\PasswordReset;
use App\Jobs\AddMailToQueueJob;
use App\Services\AiTools\AiToolResult;
use App\Services\AiTools\Contracts\AiToolInterface;
use App\Services\AiTools\Exceptions\ToolValidationException;
use App\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Action-tool: sender passord-tilbakestillingslenke.
 *
 * Bruker samme flow som /login → "Glemt passord", men triggeret fra
 * admin-siden. Idempotent via 5-min cache.
 */
class SendPasswordResetTool implements AiToolInterface
{
    public function name(): string
    {
        return 'send_password_reset';
    }

    public function definition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Sender en passord-tilbakestillingslenke til brukeren. Bruk dette når eleven eksplisitt ber om å få bytte passord — for generelle innloggingsproblemer er send_login_link som regel bedre fordi det ikke krever at eleven setter nytt passord.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'user_id' => [
                        'type' => 'integer',
                        'description' => 'ID-en til brukeren som skal få passord-tilbakestillingslenken',
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
        return "Send passord-tilbakestilling til {$name}";
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

        $cacheKey = "password_reset_sent:user:{$user->id}";
        if (Cache::has($cacheKey)) {
            return AiToolResult::success(
                'Passord-tilbakestilling er allerede sendt siste 5 minutter',
                ['user_id' => $user->id, 'already_sent' => true]
            );
        }

        try {
            // Generer token, lagre i password_resets-tabellen
            $token = Str::random(64);
            PasswordReset::updateOrCreate(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()]
            );

            $resetUrl = route('frontend.passwordreset.form', $token);

            $subject = 'Tilbakestill passordet ditt - Forfatterskolen';
            $body = '<p>Hei ' . e($user->first_name) . ',</p>'
                . '<p>Du (eller noen på vegne av deg) har bedt om å tilbakestille passordet ditt.</p>'
                . '<p><a href="' . $resetUrl . '">Klikk her for å velge nytt passord</a></p>'
                . '<p>Hvis du ikke har bedt om dette, kan du ignorere e-posten.</p>'
                . '<p>Ha en fin dag!<br>Mvh Forfatterskolen</p>';

            dispatch(new AddMailToQueueJob(
                $user->email,
                $subject,
                $body,
                'post@forfatterskolen.no',
                'Forfatterskolen',
                null,
                'password_reset',
                $user->id
            ));

            Cache::put($cacheKey, true, now()->addMinutes(5));

            return AiToolResult::success(
                "Passord-tilbakestilling sendt til {$user->email}",
                ['user_id' => $user->id, 'email' => $user->email]
            );
        } catch (\Throwable $e) {
            return AiToolResult::failure('Kunne ikke sende e-post: ' . $e->getMessage(), 'EMAIL_FAILED');
        }
    }
}
