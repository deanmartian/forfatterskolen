<?php

namespace App\Services\AiTools\Tools\Action;

use App\Services\AiTools\AiToolResult;
use App\Services\AiTools\Contracts\AiToolInterface;
use App\Services\AiTools\Exceptions\ToolValidationException;
use App\User;

/**
 * Action-tool: forlenge fristen på en spesifikk innlevering.
 *
 * Tar en AssignmentManuscript-ID og et antall dager, og oppdaterer
 * AssignmentLearnerSubmissionDate med en ny frist. Maks 60 dager
 * hardkodet som business-grense.
 */
class ExtendAssignmentDeadlineTool implements AiToolInterface
{
    public const MAX_DAYS = 60;

    public function name(): string
    {
        return 'extend_assignment_deadline';
    }

    public function definition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Forlenger fristen for en spesifikk innlevering med X dager. Krever assignment_manuscript_id (fra get_assignment_status) og antall dager (maks 60). Bruk dette når eleven ber om utsettelse og det er rimelig å gi det.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'assignment_manuscript_id' => [
                        'type' => 'integer',
                        'description' => 'ID-en til innleveringen som skal få forlenget frist',
                    ],
                    'days' => [
                        'type' => 'integer',
                        'description' => 'Antall dager fristen skal forlenges med. Maks 60.',
                    ],
                ],
                'required' => ['assignment_manuscript_id', 'days'],
            ],
        ];
    }

    public function isAction(): bool
    {
        return true;
    }

    public function validate(array $params): void
    {
        if (empty($params['assignment_manuscript_id']) || !is_numeric($params['assignment_manuscript_id'])) {
            throw new ToolValidationException('assignment_manuscript_id må være et heltall');
        }
        if (!isset($params['days']) || !is_numeric($params['days'])) {
            throw new ToolValidationException('days må være et heltall');
        }
        $days = (int) $params['days'];
        if ($days < 1 || $days > self::MAX_DAYS) {
            throw new ToolValidationException('days må være mellom 1 og ' . self::MAX_DAYS);
        }
    }

    public function describeForUi(array $params): string
    {
        return "Forlenge frist {$params['days']} dager (innlevering #{$params['assignment_manuscript_id']})";
    }

    public function execute(array $params, User $executor): AiToolResult
    {
        try {
            $manuscript = \App\AssignmentManuscript::with('assignment')->find((int) $params['assignment_manuscript_id']);
        } catch (\Throwable $e) {
            return AiToolResult::failure('Kunne ikke lese innlevering: ' . $e->getMessage(), 'DB_ERROR');
        }

        if (!$manuscript) {
            return AiToolResult::failure('Fant ikke innlevering', 'NOT_FOUND');
        }

        $days = (int) $params['days'];
        $currentDeadline = $manuscript->editor_expected_finish
            ? \Carbon\Carbon::parse($manuscript->editor_expected_finish)
            : now();

        $newDeadline = $currentDeadline->copy()->addDays($days);

        try {
            \App\AssignmentLearnerSubmissionDate::updateOrCreate(
                [
                    'assignment_id' => $manuscript->assignment_id,
                    'user_id' => $manuscript->user_id,
                ],
                [
                    'submission_date' => $newDeadline->format('M d, Y h:i A'),
                ]
            );
        } catch (\Throwable $e) {
            return AiToolResult::failure('Kunne ikke oppdatere frist: ' . $e->getMessage(), 'DB_ERROR');
        }

        return AiToolResult::success(
            "Frist forlenget {$days} dager til " . $newDeadline->format('d.m.Y'),
            [
                'assignment_manuscript_id' => $manuscript->id,
                'assignment_title' => $manuscript->assignment?->title,
                'user_id' => $manuscript->user_id,
                'days_added' => $days,
                'old_deadline' => $currentDeadline->format('d.m.Y'),
                'new_deadline' => $newDeadline->format('d.m.Y'),
            ]
        );
    }
}
