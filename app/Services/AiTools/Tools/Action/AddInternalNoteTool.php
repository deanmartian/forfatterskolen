<?php

namespace App\Services\AiTools\Tools\Action;

use App\Services\AiTools\AiToolResult;
use App\Services\AiTools\Contracts\AiToolInterface;
use App\Services\AiTools\Exceptions\ToolValidationException;
use App\Services\InboxService;
use App\User;
use Illuminate\Support\Str;

/**
 * Legger til en intern notat på samtalen — synlig kun for teamet, ikke for kunden.
 *
 * Eksempel-bruk: AI ser at samtalen mangler kontekst, og legger til en notat
 * som "Eleven har spurt om forlengelse to ganger tidligere — vurder å si nei".
 */
class AddInternalNoteTool implements AiToolInterface
{
    public function __construct(protected InboxService $inboxService)
    {
    }

    public function name(): string
    {
        return 'add_internal_note';
    }

    public function definition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Legger til en intern notat/kommentar på samtalen. Notaten er kun synlig for teamet i admin-panelet — kunden ser den ALDRI. Bruk dette for å markere kontekst, rødt flagg, eller huskelapp til kollegene.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'conversation_id' => [
                        'type' => 'integer',
                        'description' => 'ID-en til samtalen notaten skal legges til',
                    ],
                    'note' => [
                        'type' => 'string',
                        'description' => 'Selve notatet (norsk, kort og presist, 1-3 setninger)',
                    ],
                ],
                'required' => ['conversation_id', 'note'],
            ],
        ];
    }

    public function isAction(): bool
    {
        return true;
    }

    public function validate(array $params): void
    {
        $errors = [];

        if (empty($params['conversation_id']) || !is_numeric($params['conversation_id'])) {
            $errors['conversation_id'] = 'conversation_id må være et heltall';
        }

        if (empty($params['note']) || !is_string($params['note'])) {
            $errors['note'] = 'note må være en ikke-tom tekst';
        } elseif (strlen($params['note']) > 2000) {
            $errors['note'] = 'note kan være maks 2000 tegn';
        }

        if (!empty($errors)) {
            throw new ToolValidationException(
                'Validering feilet: ' . implode(', ', array_values($errors)),
                $errors
            );
        }
    }

    public function describeForUi(array $params): string
    {
        $preview = Str::limit($params['note'] ?? '', 60);
        return "Legg til intern notat: \"{$preview}\"";
    }

    public function execute(array $params, User $executor): AiToolResult
    {
        $comment = $this->inboxService->addComment(
            conversationId: (int) $params['conversation_id'],
            userId: $executor->id,
            body: $params['note'],
        );

        return AiToolResult::success(
            'Intern notat lagt til',
            ['comment_id' => $comment->id]
        );
    }
}
