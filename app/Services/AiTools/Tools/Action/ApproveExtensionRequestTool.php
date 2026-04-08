<?php

namespace App\Services\AiTools\Tools\Action;

use App\Services\AiTools\AiToolResult;
use App\Services\AiTools\Contracts\AiToolInterface;
use App\Services\AiTools\Exceptions\ToolValidationException;
use App\Services\AssignmentExtensionService;
use App\User;

/**
 * Action-tool: godkjenner en ventende fristforlengelses-forespørsel.
 *
 * Wrapper rundt AssignmentExtensionService — samme logikk brukes av
 * admin-controlleren for manuell godkjenning, slik at vi ikke
 * dupliserer forretningsregler.
 */
class ApproveExtensionRequestTool implements AiToolInterface
{
    public function __construct(protected AssignmentExtensionService $service)
    {
    }

    public function name(): string
    {
        return 'approve_extension_request';
    }

    public function definition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Godkjenner en eksisterende fristforlengelses-forespørsel fra eleven (AssignmentExtensionRequest med status=pending). Krever extension_request_id fra elevkonteksten. Setter status til approved, oppdaterer fristen, og sender automatisk bekreftelses-e-post til eleven.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'extension_request_id' => [
                        'type' => 'integer',
                        'description' => 'ID-en til fristforlengelses-forespørselen som skal godkjennes',
                    ],
                ],
                'required' => ['extension_request_id'],
            ],
        ];
    }

    public function isAction(): bool
    {
        return true;
    }

    public function validate(array $params): void
    {
        if (empty($params['extension_request_id']) || !is_numeric($params['extension_request_id'])) {
            throw new ToolValidationException('extension_request_id må være et heltall');
        }
    }

    public function describeForUi(array $params): string
    {
        return "Godkjenn fristforlengelse #{$params['extension_request_id']}";
    }

    public function execute(array $params, User $executor): AiToolResult
    {
        $req = $this->service->approve(
            (int) $params['extension_request_id'],
            $executor->id
        );

        if (!$req) {
            return AiToolResult::failure(
                'Fant ikke forespørselen, eller den er allerede behandlet',
                'NOT_PENDING'
            );
        }

        return AiToolResult::success(
            "Fristforlengelse godkjent — ny frist " . $req->requested_deadline->format('d.m.Y'),
            [
                'extension_request_id' => $req->id,
                'user_id' => $req->user_id,
                'new_deadline' => $req->requested_deadline->format('d.m.Y'),
                'assignment_title' => $req->assignment?->title,
            ]
        );
    }
}
