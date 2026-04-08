<?php

namespace App\Services\AiTools;

use App\Services\AiTools\Contracts\AiToolInterface;

/**
 * Sentral liste over alle AI-verktøy som er tilgjengelige.
 *
 * Tools registreres ved å legge dem til $defaultTools-listen. Når
 * Anthropic-API-en skal kalles, brukes getDefinitionsForAnthropic() for å
 * få en ferdig formatert tools-array som kan sendes med request-en.
 */
class AiToolRegistry
{
    /** @var array<string, AiToolInterface> */
    protected array $tools = [];

    /**
     * Registreres her — en flat liste over class-strings. Service container
     * resolver hver enkelt og kaller registerTool() ved boot.
     *
     * @var array<class-string<AiToolInterface>>
     */
    protected array $defaultTools = [
        // Lookup-tools (read-only, trygge, kan kjøres automatisk av AI)
        \App\Services\AiTools\Tools\Lookup\GetUserCoursesTool::class,
        \App\Services\AiTools\Tools\Lookup\GetInvoiceStatusTool::class,
        \App\Services\AiTools\Tools\Lookup\GetAssignmentStatusTool::class,
        \App\Services\AiTools\Tools\Lookup\GetUpcomingWebinarsTool::class,

        // Action-tools (krever klikk)
        \App\Services\AiTools\Tools\Action\AddInternalNoteTool::class,
        \App\Services\AiTools\Tools\Action\SendLoginLinkTool::class,
        \App\Services\AiTools\Tools\Action\SendPasswordResetTool::class,
        \App\Services\AiTools\Tools\Action\ExtendAssignmentDeadlineTool::class,
        \App\Services\AiTools\Tools\Action\ApproveExtensionRequestTool::class,
        \App\Services\AiTools\Tools\Action\RegisterForWebinarTool::class,
        \App\Services\AiTools\Tools\Action\AssignEditorToManuscriptTool::class,
        \App\Services\AiTools\Tools\Action\MarkConversationDoneTool::class,
    ];

    public function __construct()
    {
        foreach ($this->defaultTools as $toolClass) {
            $this->registerTool(app($toolClass));
        }
    }

    public function registerTool(AiToolInterface $tool): void
    {
        $this->tools[$tool->name()] = $tool;
    }

    /**
     * Hent et spesifikt verktøy ved navn. Returnerer null hvis ikke registrert
     * (f.eks. fordi det er deaktivert i config).
     */
    public function get(string $name): ?AiToolInterface
    {
        return $this->tools[$name] ?? null;
    }

    /**
     * Hent alle registrerte verktøy.
     *
     * @return array<string, AiToolInterface>
     */
    public function all(): array
    {
        return $this->tools;
    }

    /**
     * Hent kun lookup-verktøy (read-only, kjøres automatisk under draft).
     *
     * @return array<string, AiToolInterface>
     */
    public function lookups(): array
    {
        return array_filter($this->tools, fn(AiToolInterface $t) => !$t->isAction());
    }

    /**
     * Hent kun action-verktøy (krever klikk).
     *
     * @return array<string, AiToolInterface>
     */
    public function actions(): array
    {
        return array_filter($this->tools, fn(AiToolInterface $t) => $t->isAction());
    }

    /**
     * Returnerer tools-arrayen i Anthropic API-format, klar til å sendes
     * som "tools" i en messages-request.
     *
     * @return array<int, array>
     */
    public function getDefinitionsForAnthropic(): array
    {
        return array_values(array_map(
            fn(AiToolInterface $t) => $t->definition(),
            $this->tools
        ));
    }
}
