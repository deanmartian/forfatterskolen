<?php

namespace App\Services\AiTools\Contracts;

use App\Services\AiTools\AiToolResult;
use App\User;

/**
 * Kontrakt for et AI-verktøy.
 *
 * Hvert verktøy er en separat klasse som beskriver én konkret handling AI-en
 * kan foreslå (eller utføre automatisk hvis det er en lookup). Verktøyet er
 * ansvarlig for sin egen validering, beskrivelse til AI-en, beskrivelse til
 * brukeren, og selve utføringen.
 */
interface AiToolInterface
{
    /**
     * Maskin-navn på verktøyet (snake_case). Brukes som identifikator i
     * databasen og i Anthropic tool_use-blocks.
     * Eksempel: "send_login_link"
     */
    public function name(): string;

    /**
     * Returnerer hele tool-definisjonen i Anthropic-format
     * (https://docs.anthropic.com/claude/docs/tool-use).
     *
     * Format:
     * [
     *   'name' => 'send_login_link',
     *   'description' => 'Sender en magisk innloggingslenke...',
     *   'input_schema' => [
     *     'type' => 'object',
     *     'properties' => [...],
     *     'required' => [...],
     *   ],
     * ]
     */
    public function definition(): array;

    /**
     * True hvis verktøyet krever et brukerklikk for å utføres
     * (action-tool). False hvis det er en read-only lookup som kan
     * kjøres automatisk under draft-generering.
     */
    public function isAction(): bool;

    /**
     * Validerer parametrene som AI har foreslått. Kaster
     * ToolValidationException hvis noe er galt. Brukes BÅDE når
     * AI foreslår handlingen OG rett før den utføres (idempotent).
     *
     * @throws \App\Services\AiTools\Exceptions\ToolValidationException
     */
    public function validate(array $params): void;

    /**
     * Bygger en kort, brukervennlig beskrivelse av handlingen som
     * vises på knappen i UI-en. Eksempel: "Send innloggingslenke til Kari"
     */
    public function describeForUi(array $params): string;

    /**
     * Utfører selve handlingen. Returnerer AiToolResult som beskriver
     * om det gikk bra og hva som skjedde.
     */
    public function execute(array $params, User $executor): AiToolResult;
}
