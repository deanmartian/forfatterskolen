<?php

namespace App\Services\AiTools;

use App\Enums\AiToolActionStatus;
use App\Models\AiToolAction;
use App\Services\AiTools\Exceptions\ToolExecutionException;
use App\Services\AiTools\Exceptions\ToolValidationException;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Utfører et AI-verktøy med validering, idempotens, audit-logging og
 * error handling. Dette er det eneste stedet i koden der tools faktisk
 * kjøres — alt annet (controllers, AI-tjeneste) går gjennom denne.
 */
class AiToolExecutor
{
    public function __construct(protected AiToolRegistry $registry)
    {
    }

    /**
     * Foreslår en handling — lagrer den i ai_tool_actions med status='suggested'.
     * Brukes av AI-tjenesten når Anthropic returnerer en tool_use-block.
     */
    public function suggest(
        string $toolName,
        array $parameters,
        int $conversationId,
        ?int $inboxMessageId = null,
    ): AiToolAction {
        $tool = $this->registry->get($toolName);

        if (!$tool) {
            throw new ToolExecutionException("Ukjent verktøy: {$toolName}");
        }

        // Valider parametre allerede ved suggestion (catcher åpenbare feil tidlig)
        try {
            $tool->validate($parameters);
        } catch (ToolValidationException $e) {
            Log::warning('AiTool: foreslått handling med ugyldige parametre', [
                'tool' => $toolName,
                'params' => $parameters,
                'error' => $e->getMessage(),
            ]);
        }

        return AiToolAction::create([
            'conversation_id' => $conversationId,
            'inbox_message_id' => $inboxMessageId,
            'tool_name' => $toolName,
            'parameters' => $parameters,
            'ui_label' => $tool->describeForUi($parameters),
            'status' => AiToolActionStatus::Suggested->value,
            'suggested_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);
    }

    /**
     * Utfører en foreslått handling. Kalles fra controlleren når admin
     * klikker en knapp i UI-en.
     *
     * Bruker pessimistisk lock for å forhindre dobbeltklikk-rase.
     */
    public function execute(int $actionId, User $executor): AiToolResult
    {
        return DB::transaction(function () use ($actionId, $executor) {
            /** @var AiToolAction|null $action */
            $action = AiToolAction::where('id', $actionId)->lockForUpdate()->first();

            if (!$action) {
                return AiToolResult::failure('Handling finnes ikke', 'NOT_FOUND');
            }

            // Idempotens: hvis allerede utført, returner cached resultat
            if ($action->status === AiToolActionStatus::Executed) {
                return AiToolResult::success(
                    'Allerede utført',
                    $action->result ?? []
                );
            }

            // Sjekk at status fortsatt er klikkbar
            if (!$action->isClickable()) {
                return AiToolResult::failure(
                    'Handlingen kan ikke utføres ' . ($action->status->label()),
                    'NOT_CLICKABLE'
                );
            }

            $tool = $this->registry->get($action->tool_name);
            if (!$tool) {
                $action->update([
                    'status' => AiToolActionStatus::Failed->value,
                    'error_message' => "Verktøy '{$action->tool_name}' finnes ikke lenger",
                ]);
                return AiToolResult::failure("Verktøy ikke funnet", 'TOOL_NOT_FOUND');
            }

            // Re-valider parametere rett før eksekvering
            try {
                $tool->validate($action->parameters);
            } catch (ToolValidationException $e) {
                $action->update([
                    'status' => AiToolActionStatus::Failed->value,
                    'error_message' => 'Validering feilet: ' . $e->getMessage(),
                ]);
                return AiToolResult::failure($e->getMessage(), 'VALIDATION_FAILED');
            }

            // Utfør handlingen
            try {
                $result = $tool->execute($action->parameters, $executor);

                $action->update([
                    'status' => $result->success
                        ? AiToolActionStatus::Executed->value
                        : AiToolActionStatus::Failed->value,
                    'executed_at' => now(),
                    'executed_by_user_id' => $executor->id,
                    'result' => $result->data,
                    'error_message' => $result->success ? null : $result->message,
                ]);

                Log::info('AiTool: handling utført', [
                    'action_id' => $action->id,
                    'tool' => $action->tool_name,
                    'success' => $result->success,
                    'executor' => $executor->email,
                ]);

                return $result;
            } catch (\Throwable $e) {
                $action->update([
                    'status' => AiToolActionStatus::Failed->value,
                    'executed_at' => now(),
                    'executed_by_user_id' => $executor->id,
                    'error_message' => $e->getMessage(),
                ]);

                Log::error('AiTool: handling kastet exception', [
                    'action_id' => $action->id,
                    'tool' => $action->tool_name,
                    'error' => $e->getMessage(),
                ]);

                return AiToolResult::failure(
                    'Uventet feil: ' . $e->getMessage(),
                    'EXCEPTION'
                );
            }
        });
    }
}
