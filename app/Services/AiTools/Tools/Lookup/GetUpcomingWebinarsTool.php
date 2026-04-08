<?php

namespace App\Services\AiTools\Tools\Lookup;

use App\Services\AiTools\AiToolResult;
use App\Services\AiTools\Contracts\AiToolInterface;
use App\Services\AiTools\Exceptions\ToolValidationException;
use App\User;

/**
 * Lookup-tool: hent brukerens kommende webinarer (de neste X dagene).
 *
 * Returnerer tittel, dato/tid, og join-URL for hvert webinar eleven
 * er registrert på. Brukes når eleven spør etter webinar-lenker eller
 * neste samling.
 */
class GetUpcomingWebinarsTool implements AiToolInterface
{
    public function name(): string
    {
        return 'get_upcoming_webinars';
    }

    public function definition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Henter alle webinarer brukeren er registrert på som starter i løpet av de neste X dagene (default 14). Returnerer tittel, dato/tid og en direkte join-lenke. Bruk dette når eleven spør "hva er lenken til webinaret?" eller "når er neste samling?".',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'user_id' => [
                        'type' => 'integer',
                        'description' => 'ID-en til brukeren webinarene skal hentes for',
                    ],
                    'days_ahead' => [
                        'type' => 'integer',
                        'description' => 'Hvor mange dager frem i tid vi skal lete. Default 14.',
                    ],
                ],
                'required' => ['user_id'],
            ],
        ];
    }

    public function isAction(): bool
    {
        return false;
    }

    public function validate(array $params): void
    {
        if (empty($params['user_id']) || !is_numeric($params['user_id'])) {
            throw new ToolValidationException('user_id må være et heltall');
        }
        if (isset($params['days_ahead']) && (!is_numeric($params['days_ahead']) || $params['days_ahead'] < 1 || $params['days_ahead'] > 365)) {
            throw new ToolValidationException('days_ahead må være mellom 1 og 365');
        }
    }

    public function describeForUi(array $params): string
    {
        $days = (int) ($params['days_ahead'] ?? 14);
        return "Slå opp webinarer de neste {$days} dager for bruker #{$params['user_id']}";
    }

    public function execute(array $params, User $executor): AiToolResult
    {
        $user = User::find((int) $params['user_id']);
        if (!$user) {
            return AiToolResult::failure('Fant ikke bruker', 'USER_NOT_FOUND');
        }

        $daysAhead = (int) ($params['days_ahead'] ?? 14);

        try {
            $registrations = \App\WebinarRegistrant::where('user_id', $user->id)
                ->whereHas('webinar', function ($q) use ($daysAhead) {
                    $q->where('start_date', '>=', now())
                        ->where('start_date', '<=', now()->addDays($daysAhead));
                })
                ->with('webinar')
                ->get();
        } catch (\Throwable $e) {
            return AiToolResult::failure('Kunne ikke lese webinarer: ' . $e->getMessage(), 'DB_ERROR');
        }

        if ($registrations->isEmpty()) {
            return AiToolResult::success(
                "Brukeren har ingen kommende webinarer de neste {$daysAhead} dagene",
                ['user_id' => $user->id, 'webinars' => []]
            );
        }

        $list = $registrations->map(function ($reg) {
            $w = $reg->webinar;
            return [
                'webinar_id' => $w?->id,
                'title' => $w?->title ?? 'Ukjent webinar',
                'start_date' => $w?->start_date ? \Carbon\Carbon::parse($w->start_date)->format('d.m.Y H:i') : null,
                'join_url' => $reg->join_url ?: null,
            ];
        })->values()->all();

        return AiToolResult::success(
            "Fant {$registrations->count()} webinarer de neste {$daysAhead} dagene",
            [
                'user_id' => $user->id,
                'user_name' => trim($user->first_name . ' ' . $user->last_name),
                'webinars' => $list,
            ]
        );
    }
}
