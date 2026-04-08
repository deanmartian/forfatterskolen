<?php

namespace App\Services\AiTools\Tools\Lookup;

use App\Services\AiTools\AiToolResult;
use App\Services\AiTools\Contracts\AiToolInterface;
use App\Services\AiTools\Exceptions\ToolValidationException;
use App\User;

/**
 * Lookup-tool: hent status på brukerens innleveringer/oppgaver.
 *
 * Returnerer liste med oppgavetittel, frist, om innlevering er gjort,
 * og hvilken redaktør som er tildelt (hvis noen).
 */
class GetAssignmentStatusTool implements AiToolInterface
{
    public function name(): string
    {
        return 'get_assignment_status';
    }

    public function definition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Henter status på alle brukerens innleveringer — tittel, frist, om fila er levert, status (ventende/godkjent/avvist), og hvem som er redaktør. Bruk dette når eleven spør om frister, leveringstatus, eller når tilbakemelding kan forventes.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'user_id' => [
                        'type' => 'integer',
                        'description' => 'ID-en til brukeren oppgavene skal hentes for',
                    ],
                    'include_completed' => [
                        'type' => 'boolean',
                        'description' => 'Inkluder også fullførte/godkjente oppgaver. Default er false (kun pågående).',
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
    }

    public function describeForUi(array $params): string
    {
        return "Slå opp oppgavestatus for bruker #{$params['user_id']}";
    }

    public function execute(array $params, User $executor): AiToolResult
    {
        $user = User::find((int) $params['user_id']);
        if (!$user) {
            return AiToolResult::failure('Fant ikke bruker', 'USER_NOT_FOUND');
        }

        $includeCompleted = (bool) ($params['include_completed'] ?? false);

        try {
            $query = \App\AssignmentManuscript::where('user_id', $user->id)
                ->with('assignment');

            if (!$includeCompleted) {
                $query->where('status', 0);
            }

            $assignments = $query->get();
        } catch (\Throwable $e) {
            return AiToolResult::failure('Kunne ikke lese oppgaver: ' . $e->getMessage(), 'DB_ERROR');
        }

        if ($assignments->isEmpty()) {
            return AiToolResult::success(
                $includeCompleted ? 'Brukeren har ingen oppgaver' : 'Brukeren har ingen pågående oppgaver',
                ['user_id' => $user->id, 'assignments' => []]
            );
        }

        $list = $assignments->map(function ($m) {
            return [
                'assignment_id' => $m->assignment_id ?? $m->assignment?->id,
                'title' => $m->assignment?->title ?? 'Ukjent',
                'deadline' => $m->editor_expected_finish ?: ($m->assignment?->editor_expected_finish ?? null),
                'delivered' => !empty($m->filename),
                'filename' => $m->filename ?: null,
                'status' => (int) ($m->status ?? 0),
                'editor_id' => $m->editor_id ?? null,
            ];
        })->values()->all();

        return AiToolResult::success(
            "Fant {$assignments->count()} oppgaver",
            [
                'user_id' => $user->id,
                'user_name' => trim($user->first_name . ' ' . $user->last_name),
                'assignments' => $list,
            ]
        );
    }
}
