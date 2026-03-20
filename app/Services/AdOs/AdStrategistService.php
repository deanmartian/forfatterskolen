<?php

namespace App\Services\AdOs;

use App\Models\AdOs\AdCampaign;
use App\Models\AdOs\AdMetricSnapshot;
use App\Models\AdOs\AdAiDecision;
use App\Models\AdOs\AdActionLog;
use App\Models\AdOs\AdStrategistConversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdStrategistService
{
    public function __construct(
        private readonly AdCampaignService $campaignService,
        private readonly AdBudgetService $budgetService,
        private readonly AdStrategyService $strategyService,
    ) {}

    /**
     * Process a natural language instruction and return a structured action plan.
     */
    public function processInstruction(string $instruction): array
    {
        $context = $this->gatherCampaignContext();
        $prompt = $this->buildPrompt($instruction, $context);

        try {
            $aiResponse = $this->callClaude($prompt);
            $plan = $this->parseResponse($aiResponse);

            $conversation = $this->saveConversation($instruction, $context, $plan);

            return [
                'success' => true,
                'conversation_id' => $conversation->id,
                'plan' => $plan,
            ];
        } catch (\Exception $e) {
            Log::error('AdOS Strategist failed', [
                'instruction' => $instruction,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Kunne ikke generere handlingsplan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Execute approved actions from a strategist plan.
     */
    public function executeActions(int $conversationId, array $approvedActionIds): array
    {
        $conversation = AdStrategistConversation::findOrFail($conversationId);
        $plan = $conversation->ai_response;
        $actions = $plan['actions'] ?? [];
        $results = [];

        foreach ($actions as $index => $action) {
            if (!in_array($index, $approvedActionIds)) {
                continue;
            }

            try {
                $result = $this->executeSingleAction($action);
                $results[] = [
                    'index' => $index,
                    'action' => $action,
                    'success' => true,
                    'result' => $result,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'index' => $index,
                    'action' => $action,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Update conversation with execution results
        $conversation->update([
            'execution_results' => $results,
            'executed_at' => now(),
            'status' => 'executed',
        ]);

        AdActionLog::log('strategist_plan_executed', [
            'triggered_by' => 'human',
            'user_id' => auth()->id(),
            'payload' => [
                'conversation_id' => $conversationId,
                'approved_count' => count($approvedActionIds),
                'success_count' => collect($results)->where('success', true)->count(),
            ],
        ]);

        return $results;
    }

    /**
     * Get conversation history.
     */
    public function getHistory(int $limit = 20)
    {
        return AdStrategistConversation::orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Gather all relevant campaign data for the AI context.
     */
    private function gatherCampaignContext(): array
    {
        $campaigns = AdCampaign::with(['latestMetrics', 'account'])
            ->orderByDesc('updated_at')
            ->get();

        $campaignData = $campaigns->map(function (AdCampaign $campaign) {
            $metrics = $campaign->latestMetrics;
            return [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'status' => $campaign->status,
                'platform' => $campaign->platform ?? 'facebook',
                'objective' => $campaign->objective ?? null,
                'daily_budget' => (float) $campaign->daily_budget,
                'total_budget' => (float) $campaign->total_budget,
                'spent_total' => (float) $campaign->spent_total,
                'targeting' => $campaign->targeting,
                'created_at' => $campaign->created_at?->format('Y-m-d'),
                'metrics' => $metrics ? [
                    'date' => $metrics->date?->format('Y-m-d'),
                    'impressions' => $metrics->impressions ?? 0,
                    'clicks' => $metrics->clicks ?? 0,
                    'conversions' => $metrics->conversions ?? 0,
                    'spend' => (float) ($metrics->spend ?? 0),
                    'cpa' => (float) ($metrics->cpa ?? 0),
                    'ctr' => (float) ($metrics->ctr ?? 0),
                    'cpc' => (float) ($metrics->cpc ?? 0),
                    'cpm' => (float) ($metrics->cpm ?? 0),
                    'roas' => (float) ($metrics->roas ?? 0),
                    'revenue' => (float) ($metrics->revenue ?? 0),
                ] : null,
            ];
        })->toArray();

        $profile = $this->strategyService->getActiveProfile();

        return [
            'campaigns' => $campaignData,
            'budget' => [
                'spent_today' => $this->budgetService->getCurrentSpendToday(),
                'spent_month' => $this->budgetService->getCurrentSpendThisMonth(),
                'remaining_daily' => $this->budgetService->getRemainingDailyBudget(),
                'remaining_monthly' => $this->budgetService->getRemainingMonthlyBudget(),
            ],
            'strategy' => $profile ? [
                'name' => $profile->name,
                'automation_level' => $profile->automation_level,
                'primary_goal' => $profile->primary_goal,
                'target_cpa' => $profile->target_cpa,
                'target_roas' => $profile->target_roas,
                'risk_tolerance' => $profile->risk_tolerance,
            ] : null,
            'date' => now()->format('Y-m-d'),
            'day_of_week' => now()->locale('nb')->dayName,
        ];
    }

    /**
     * Build the prompt for Claude with campaign context and instruction.
     */
    private function buildPrompt(string $instruction, array $context): string
    {
        $campaignsJson = json_encode($context['campaigns'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $budgetJson = json_encode($context['budget'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $strategyJson = $context['strategy']
            ? json_encode($context['strategy'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : 'Ingen aktiv strategi konfigurert.';

        return <<<PROMPT
Du er en AI-annonsestrategist for Forfatterskolen, Norges ledende nettbaserte skriveskole.

Forfatterskolen tilbyr følgende produkter:
- Årskurs (12-måneders skrivekurs, flaggskipprodukt)
- Diktkurs (spesialisert poesikurs)
- Novellekurs (kurs i novelleskriving)
- Skriveverksted (gratis webinar / intro-arrangement for leads)
- Feelgood-kurs (skriv feelgood-roman)
- Sakprosa-kurs (skriv sakprosa)
- Manustjenester (profesjonell tilbakemelding på manuskripter)

DAGENS DATO: {$context['date']} ({$context['day_of_week']})

AKTIV STRATEGI:
{$strategyJson}

BUDSJETTINFORMASJON:
{$budgetJson}

KAMPANJEDATA (alle kampanjer med siste metrics):
{$campaignsJson}

---

BRUKERENS INSTRUKSJON:
"{$instruction}"

---

Analyser kampanjedataene og lag en strukturert handlingsplan basert på instruksjonen.

Du SKAL svare med gyldig JSON i følgende format:
{
  "summary": "Kort oppsummering av planen (norsk)",
  "reasoning": "Din analyse og begrunnelse for forslagene (norsk)",
  "actions": [
    {
      "type": "scale_budget|reduce_budget|pause_campaign|resume_campaign|create_campaign|reallocate_budget|create_creatives",
      "campaign_id": null eller kampanje-ID,
      "description": "Beskrivelse av handlingen (norsk)",
      "details": {
        // Spesifikke verdier avhengig av type:
        // scale_budget: {"new_daily_budget": 500, "increase_percent": 25}
        // reduce_budget: {"new_daily_budget": 200, "decrease_percent": 30}
        // pause_campaign: {}
        // resume_campaign: {}
        // create_campaign: {"name": "...", "objective": "...", "suggested_budget": 300, "targeting_notes": "..."}
        // reallocate_budget: {"from_campaign_id": 1, "to_campaign_id": 2, "amount": 100}
        // create_creatives: {"product": "...", "audience": "...", "angles": ["...", "..."]}
      },
      "risk_level": "low|medium|high",
      "reasoning": "Begrunnelse for denne handlingen (norsk)"
    }
  ],
  "warnings": ["Eventuelle advarsler eller ting å være obs på (norsk)"]
}

Viktige retningslinjer:
- Vær spesifikk med tall og verdier
- Vurder risiko realistisk
- Begrunn hver handling med data fra kampanjene
- Svar ALLTID på norsk
- Hvis instruksjonen er uklar, gjør rimelige antagelser og forklar dem
- Ikke foreslå handlinger som ikke er støttet av dataene
- Ta hensyn til budsjettgrenser
- Returner KUN gyldig JSON, ingen annen tekst
PROMPT;
    }

    /**
     * Call Claude API.
     */
    private function callClaude(string $prompt): string
    {
        $apiKey = config('services.anthropic.key');

        if (!$apiKey) {
            throw new \RuntimeException('Anthropic API-nøkkel er ikke konfigurert');
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01',
        ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-sonnet-4-20250514',
            'max_tokens' => 4096,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);

        if (!$response->successful()) {
            $error = $response->json('error.message', 'Ukjent feil fra API');
            throw new \RuntimeException("Claude API-feil: {$error}");
        }

        return $response->json('content.0.text', '{}');
    }

    /**
     * Parse the AI response into a structured plan.
     */
    private function parseResponse(string $response): array
    {
        // Strip markdown code block wrappers if present
        $cleaned = trim($response);
        if (str_starts_with($cleaned, '```')) {
            $cleaned = preg_replace('/^```(?:json)?\s*/', '', $cleaned);
            $cleaned = preg_replace('/\s*```$/', '', $cleaned);
        }

        $plan = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Kunne ikke tolke AI-responsen som JSON: ' . json_last_error_msg());
        }

        // Validate structure
        if (!isset($plan['summary']) || !isset($plan['actions'])) {
            throw new \RuntimeException('AI-responsen mangler påkrevde felt (summary, actions)');
        }

        // Validate each action
        $validTypes = ['scale_budget', 'reduce_budget', 'pause_campaign', 'resume_campaign', 'create_campaign', 'reallocate_budget', 'create_creatives'];
        $validRiskLevels = ['low', 'medium', 'high'];

        foreach ($plan['actions'] as $i => &$action) {
            if (!isset($action['type']) || !in_array($action['type'], $validTypes)) {
                throw new \RuntimeException("Handling #{$i} har ugyldig type: " . ($action['type'] ?? 'mangler'));
            }
            if (!isset($action['risk_level']) || !in_array($action['risk_level'], $validRiskLevels)) {
                $action['risk_level'] = 'medium';
            }
            if (!isset($action['description'])) {
                $action['description'] = ucfirst(str_replace('_', ' ', $action['type']));
            }
            if (!isset($action['details'])) {
                $action['details'] = [];
            }
            if (!isset($action['reasoning'])) {
                $action['reasoning'] = '';
            }
        }

        return $plan;
    }

    /**
     * Save the conversation to the database.
     */
    private function saveConversation(string $instruction, array $context, array $plan): AdStrategistConversation
    {
        return AdStrategistConversation::create([
            'user_id' => auth()->id(),
            'instruction' => $instruction,
            'campaign_context' => $context,
            'ai_response' => $plan,
            'status' => 'pending',
        ]);
    }

    /**
     * Execute a single action from the plan.
     */
    private function executeSingleAction(array $action): array
    {
        return match ($action['type']) {
            'scale_budget' => $this->executeScaleBudget($action),
            'reduce_budget' => $this->executeReduceBudget($action),
            'pause_campaign' => $this->executePauseCampaign($action),
            'resume_campaign' => $this->executeResumeCampaign($action),
            'create_campaign' => $this->executeCreateCampaign($action),
            'reallocate_budget' => $this->executeReallocateBudget($action),
            'create_creatives' => $this->executeCreateCreatives($action),
            default => throw new \RuntimeException("Ukjent handlingstype: {$action['type']}"),
        };
    }

    private function executeScaleBudget(array $action): array
    {
        $campaignId = $action['campaign_id'];
        $newBudget = $action['details']['new_daily_budget'] ?? null;

        if (!$campaignId || !$newBudget) {
            throw new \RuntimeException('Mangler campaign_id eller new_daily_budget for scale_budget');
        }

        $campaign = $this->campaignService->updateBudget($campaignId, (float) $newBudget, 'ai');

        return [
            'action' => 'budget_scaled',
            'campaign_id' => $campaignId,
            'new_budget' => (float) $campaign->daily_budget,
        ];
    }

    private function executeReduceBudget(array $action): array
    {
        $campaignId = $action['campaign_id'];
        $newBudget = $action['details']['new_daily_budget'] ?? null;

        if (!$campaignId || $newBudget === null) {
            throw new \RuntimeException('Mangler campaign_id eller new_daily_budget for reduce_budget');
        }

        $campaign = $this->campaignService->updateBudget($campaignId, (float) $newBudget, 'ai');

        return [
            'action' => 'budget_reduced',
            'campaign_id' => $campaignId,
            'new_budget' => (float) $campaign->daily_budget,
        ];
    }

    private function executePauseCampaign(array $action): array
    {
        $campaignId = $action['campaign_id'];
        if (!$campaignId) {
            throw new \RuntimeException('Mangler campaign_id for pause_campaign');
        }

        $campaign = $this->campaignService->updateStatus($campaignId, 'paused', 'ai');

        return [
            'action' => 'campaign_paused',
            'campaign_id' => $campaignId,
        ];
    }

    private function executeResumeCampaign(array $action): array
    {
        $campaignId = $action['campaign_id'];
        if (!$campaignId) {
            throw new \RuntimeException('Mangler campaign_id for resume_campaign');
        }

        $campaign = $this->campaignService->updateStatus($campaignId, 'active', 'ai');

        return [
            'action' => 'campaign_resumed',
            'campaign_id' => $campaignId,
        ];
    }

    private function executeCreateCampaign(array $action): array
    {
        $details = $action['details'];

        $campaign = $this->campaignService->createDraft([
            'name' => $details['name'] ?? 'AI-foreslått kampanje',
            'platform' => 'facebook',
            'objective' => $details['objective'] ?? 'conversions',
            'daily_budget' => $details['suggested_budget'] ?? 0,
            'targeting' => ['notes' => $details['targeting_notes'] ?? ''],
            'ai_brief' => true,
        ]);

        return [
            'action' => 'campaign_draft_created',
            'campaign_id' => $campaign->id,
            'name' => $campaign->name,
        ];
    }

    private function executeReallocateBudget(array $action): array
    {
        $details = $action['details'];
        $fromId = $details['from_campaign_id'] ?? null;
        $toId = $details['to_campaign_id'] ?? null;
        $amount = $details['amount'] ?? null;

        if (!$fromId || !$toId || !$amount) {
            throw new \RuntimeException('Mangler from_campaign_id, to_campaign_id eller amount for reallocate_budget');
        }

        $fromCampaign = AdCampaign::findOrFail($fromId);
        $toCampaign = AdCampaign::findOrFail($toId);

        $newFromBudget = max(0, (float) $fromCampaign->daily_budget - (float) $amount);
        $newToBudget = (float) $toCampaign->daily_budget + (float) $amount;

        $this->campaignService->updateBudget($fromId, $newFromBudget, 'ai');
        $this->campaignService->updateBudget($toId, $newToBudget, 'ai');

        return [
            'action' => 'budget_reallocated',
            'from_campaign_id' => $fromId,
            'to_campaign_id' => $toId,
            'amount' => $amount,
        ];
    }

    private function executeCreateCreatives(array $action): array
    {
        $details = $action['details'];

        // Queue creative generation via the existing service
        $creativeService = app(AdCreativeService::class);
        $creatives = $creativeService->generateCreatives([
            'product' => $details['product'] ?? 'skriveverksted',
            'audience' => $details['audience'] ?? 'voksne som vil skrive',
            'goal' => 'conversions',
            'platform' => 'facebook',
        ]);

        return [
            'action' => 'creatives_generated',
            'count' => count($creatives),
        ];
    }
}
