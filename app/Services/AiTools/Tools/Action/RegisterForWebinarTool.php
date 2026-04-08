<?php

namespace App\Services\AiTools\Tools\Action;

use App\Services\AiTools\AiToolResult;
use App\Services\AiTools\Contracts\AiToolInterface;
use App\Services\AiTools\Exceptions\ToolValidationException;
use App\User;

/**
 * Action-tool: registrerer en bruker for et spesifikt webinar.
 *
 * Oppretter en WebinarRegistrant-rad hvis eleven ikke allerede er
 * registrert. BigMarker/Whereby join-URL genereres normalt av de
 * eksisterende cron-jobbene.
 */
class RegisterForWebinarTool implements AiToolInterface
{
    public function name(): string
    {
        return 'register_for_webinar';
    }

    public function definition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Registrerer en bruker for et spesifikt webinar. Krever user_id og webinar_id. Oppretter en WebinarRegistrant-rad. Selve join-URL-en genereres automatisk av BigMarker-cron-jobben. Bruk dette når eleven ber om å bli lagt til på et webinar.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'user_id' => [
                        'type' => 'integer',
                        'description' => 'ID-en til brukeren som skal registreres',
                    ],
                    'webinar_id' => [
                        'type' => 'integer',
                        'description' => 'ID-en til webinaret brukeren skal registreres på',
                    ],
                ],
                'required' => ['user_id', 'webinar_id'],
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
        if (empty($params['webinar_id']) || !is_numeric($params['webinar_id'])) {
            throw new ToolValidationException('webinar_id må være et heltall');
        }
    }

    public function describeForUi(array $params): string
    {
        return "Registrer bruker #{$params['user_id']} til webinar #{$params['webinar_id']}";
    }

    public function execute(array $params, User $executor): AiToolResult
    {
        $user = User::find((int) $params['user_id']);
        if (!$user) {
            return AiToolResult::failure('Fant ikke bruker', 'USER_NOT_FOUND');
        }

        try {
            $webinar = \App\Webinar::find((int) $params['webinar_id']);
        } catch (\Throwable $e) {
            return AiToolResult::failure('Kunne ikke lese webinar: ' . $e->getMessage(), 'DB_ERROR');
        }

        if (!$webinar) {
            return AiToolResult::failure('Fant ikke webinar', 'WEBINAR_NOT_FOUND');
        }

        // Sjekk om webinaret er i fortiden
        if ($webinar->start_date && \Carbon\Carbon::parse($webinar->start_date)->isPast()) {
            return AiToolResult::failure('Webinaret er allerede ferdig', 'WEBINAR_PAST');
        }

        // Idempotens: hvis allerede registrert, returner cached
        $existing = \App\WebinarRegistrant::where('user_id', $user->id)
            ->where('webinar_id', $webinar->id)
            ->first();

        if ($existing) {
            return AiToolResult::success(
                "Brukeren er allerede registrert på webinaret",
                [
                    'user_id' => $user->id,
                    'webinar_id' => $webinar->id,
                    'webinar_title' => $webinar->title,
                    'already_registered' => true,
                    'join_url' => $existing->join_url,
                ]
            );
        }

        try {
            $registrant = \App\WebinarRegistrant::create([
                'user_id' => $user->id,
                'webinar_id' => $webinar->id,
            ]);
        } catch (\Throwable $e) {
            return AiToolResult::failure('Kunne ikke opprette registrering: ' . $e->getMessage(), 'DB_ERROR');
        }

        return AiToolResult::success(
            "Registrert for webinar: {$webinar->title}",
            [
                'user_id' => $user->id,
                'webinar_id' => $webinar->id,
                'webinar_title' => $webinar->title,
                'registrant_id' => $registrant->id,
                'note' => 'Join-URL genereres automatisk av BigMarker-cron og sendes via e-post dagen før webinaret.',
            ]
        );
    }
}
