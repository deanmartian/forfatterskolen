<?php

namespace App\Services\AiTools\Tools\Action;

use App\Services\AiTools\AiToolResult;
use App\Services\AiTools\Contracts\AiToolInterface;
use App\Services\AiTools\Exceptions\ToolValidationException;
use App\User;

/**
 * Action-tool: tildeler en redaktør til en manustjeneste (ShopManuscriptsTaken).
 *
 * Verifiserer at editor-id-en tilhører en aktiv redaktør før tildelingen.
 */
class AssignEditorToManuscriptTool implements AiToolInterface
{
    public function name(): string
    {
        return 'assign_editor_to_manuscript';
    }

    public function definition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Tildeler en redaktør til en bestemt manustjeneste. Krever shop_manuscript_taken_id og editor_id. Editoren må være en aktiv bruker med rolle 3 (Redaktør). Bruk dette når manustjenesten trenger en ny redaktør, eller når en bytte er ønsket.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'shop_manuscript_taken_id' => [
                        'type' => 'integer',
                        'description' => 'ID-en til manustjenesten (ShopManuscriptsTaken)',
                    ],
                    'editor_id' => [
                        'type' => 'integer',
                        'description' => 'ID-en til redaktøren som skal tildeles (må ha rolle 3)',
                    ],
                ],
                'required' => ['shop_manuscript_taken_id', 'editor_id'],
            ],
        ];
    }

    public function isAction(): bool
    {
        return true;
    }

    public function validate(array $params): void
    {
        if (empty($params['shop_manuscript_taken_id']) || !is_numeric($params['shop_manuscript_taken_id'])) {
            throw new ToolValidationException('shop_manuscript_taken_id må være et heltall');
        }
        if (empty($params['editor_id']) || !is_numeric($params['editor_id'])) {
            throw new ToolValidationException('editor_id må være et heltall');
        }
    }

    public function describeForUi(array $params): string
    {
        $editor = User::find((int) $params['editor_id']);
        $editorName = $editor ? trim($editor->first_name . ' ' . $editor->last_name) : "redaktør #{$params['editor_id']}";
        return "Tildele {$editorName} til manustjeneste #{$params['shop_manuscript_taken_id']}";
    }

    public function execute(array $params, User $executor): AiToolResult
    {
        // Verifiser at redaktøren eksisterer og er aktiv
        $editor = User::find((int) $params['editor_id']);
        if (!$editor) {
            return AiToolResult::failure('Fant ikke redaktør', 'EDITOR_NOT_FOUND');
        }
        if ($editor->role != 3 && !$editor->admin_with_editor_access) {
            return AiToolResult::failure('Brukeren er ikke en redaktør', 'NOT_EDITOR');
        }
        if (!$editor->is_active) {
            return AiToolResult::failure('Redaktøren er inaktiv', 'EDITOR_INACTIVE');
        }

        // Finn manustjenesten
        try {
            $manuscript = \App\ShopManuscriptsTaken::find((int) $params['shop_manuscript_taken_id']);
        } catch (\Throwable $e) {
            return AiToolResult::failure('Kunne ikke lese manustjeneste: ' . $e->getMessage(), 'DB_ERROR');
        }

        if (!$manuscript) {
            return AiToolResult::failure('Fant ikke manustjeneste', 'MANUSCRIPT_NOT_FOUND');
        }

        $oldEditorId = $manuscript->editor_id;

        try {
            $manuscript->update(['editor_id' => $editor->id]);
        } catch (\Throwable $e) {
            return AiToolResult::failure('Kunne ikke oppdatere tildeling: ' . $e->getMessage(), 'DB_ERROR');
        }

        return AiToolResult::success(
            "Redaktør tildelt: " . trim($editor->first_name . ' ' . $editor->last_name),
            [
                'shop_manuscript_taken_id' => $manuscript->id,
                'editor_id' => $editor->id,
                'editor_name' => trim($editor->first_name . ' ' . $editor->last_name),
                'previous_editor_id' => $oldEditorId,
            ]
        );
    }
}
