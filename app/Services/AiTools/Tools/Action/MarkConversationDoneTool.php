<?php

namespace App\Services\AiTools\Tools\Action;

use App\Services\AiTools\AiToolResult;
use App\Services\AiTools\Contracts\AiToolInterface;
use App\Services\AiTools\Exceptions\ToolValidationException;
use App\Services\InboxService;
use App\User;

/**
 * Action-tool: markerer en samtale som fullført/lukket.
 *
 * Setter status til "closed" og timestamp på resolved_at. Brukes når
 * AI-en har identifisert at samtalen er ferdig løst (f.eks. etter at
 * en annen handling løste problemet).
 */
class MarkConversationDoneTool implements AiToolInterface
{
    public function __construct(protected InboxService $inboxService)
    {
    }

    public function name(): string
    {
        return 'mark_conversation_done';
    }

    public function definition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Markerer samtalen som lukket/fullført. Sett kun når du er sikker på at saken er løst — f.eks. etter en vellykket handling (send_login_link), eller når svaret tydelig avslutter saken. Sett IKKE hvis det er uavklarte spørsmål igjen.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'conversation_id' => [
                        'type' => 'integer',
                        'description' => 'ID-en til samtalen som skal lukkes',
                    ],
                ],
                'required' => ['conversation_id'],
            ],
        ];
    }

    public function isAction(): bool
    {
        return true;
    }

    public function validate(array $params): void
    {
        if (empty($params['conversation_id']) || !is_numeric($params['conversation_id'])) {
            throw new ToolValidationException('conversation_id må være et heltall');
        }
    }

    public function describeForUi(array $params): string
    {
        return "Lukke samtalen som fullført";
    }

    public function execute(array $params, User $executor): AiToolResult
    {
        try {
            $this->inboxService->updateStatus((int) $params['conversation_id'], 'closed');
        } catch (\Throwable $e) {
            return AiToolResult::failure('Kunne ikke lukke samtalen: ' . $e->getMessage(), 'DB_ERROR');
        }

        return AiToolResult::success(
            'Samtalen er markert som fullført',
            ['conversation_id' => (int) $params['conversation_id']]
        );
    }
}
