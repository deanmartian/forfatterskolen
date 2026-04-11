<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\AdOs\AdStrategyService;
use App\Services\AdOs\AdCampaignService;
use App\Services\AdOs\AdCreativeService;
use App\Services\AdOs\AdDecisionService;
use App\Services\AdOs\AdApprovalService;
use App\Services\AdOs\AdRuleService;
use App\Services\AdOs\AdBudgetService;
use App\Services\AdOs\AdOptimizationService;
use App\Services\AdOs\AdActionLogService;
use App\Services\AdOs\AdReportService;
use App\Services\AdOs\AdStrategistService;
use App\Models\AdOs\AdExperiment;
use App\Jobs\AdOs\GenerateAdCreativesJob;
use Illuminate\Http\Request;

class AdOsController extends Controller
{
    public function __construct(
        private readonly AdStrategyService $strategyService,
        private readonly AdCampaignService $campaignService,
        private readonly AdCreativeService $creativeService,
        private readonly AdDecisionService $decisionService,
        private readonly AdApprovalService $approvalService,
        private readonly AdRuleService $ruleService,
        private readonly AdBudgetService $budgetService,
        private readonly AdActionLogService $logService,
        private readonly AdReportService $reportService,
        private readonly AdStrategistService $strategistService,
    ) {}

    public function dashboard()
    {
        try {
            $strategy = $this->strategyService->getActiveProfile();
        } catch (\Exception $e) {
            $strategy = null;
        }

        try {
            $campaignStats = $this->campaignService->getDashboardStats();
        } catch (\Exception $e) {
            $campaignStats = ['active' => 0];
        }

        try {
            $pendingApprovals = $this->approvalService->getPendingCount();
        } catch (\Exception $e) {
            $pendingApprovals = 0;
        }

        try {
            $pendingRecommendations = $this->decisionService->getRecommendations(5);
        } catch (\Exception $e) {
            $pendingRecommendations = collect();
        }

        try {
            $recentActions = $this->logService->getRecentActions(10);
        } catch (\Exception $e) {
            $recentActions = collect();
        }

        try {
            $dailySummary = $this->reportService->getDailySummary();
        } catch (\Exception $e) {
            $dailySummary = ['metrics' => null];
        }

        try {
            $budgetInfo = [
                'remaining_daily' => $this->budgetService->getRemainingDailyBudget(),
                'remaining_monthly' => $this->budgetService->getRemainingMonthlyBudget(),
                'spent_today' => $this->budgetService->getCurrentSpendToday(),
                'spent_month' => $this->budgetService->getCurrentSpendThisMonth(),
            ];
        } catch (\Exception $e) {
            $budgetInfo = ['remaining_daily' => 0, 'remaining_monthly' => 0, 'spent_today' => 0, 'spent_month' => 0];
        }

        // Aktive kampanjer fra FreeWebinar (Facebook + Google)
        try {
            $activeWebinarCampaigns = \App\FreeWebinar::where(function ($q) {
                    $q->whereNotNull('facebook_campaign_id')
                      ->orWhereNotNull('google_search_campaign_id');
                })
                ->orderByDesc('start_date')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            $activeWebinarCampaigns = collect();
        }

        // Siste Facebook-leads
        try {
            $recentLeads = \DB::table('webinar_registrants')
                ->where('source', 'facebook')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            $recentLeads = collect();
        }

        return view('backend.ads.dashboard', compact(
            'strategy', 'campaignStats', 'pendingApprovals',
            'pendingRecommendations', 'recentActions', 'dailySummary', 'budgetInfo',
            'activeWebinarCampaigns', 'recentLeads'
        ));
    }

    public function metricsApi(Request $request)
    {
        $days = (int) $request->input('days', 14);

        try {
            $metrics = \App\Models\AdOs\AdMetricSnapshot::where('level', 'campaign')
            ->where('date', '>=', now()->subDays($days)->toDateString())
            ->orderBy('date')
            ->get()
            ->groupBy('date')
            ->map(fn($day) => [
                'date' => \Carbon\Carbon::parse($day->first()->date)->format('d.m'),
                'spend' => round($day->sum('spend'), 2),
                'leads' => (int) $day->sum('leads'),
                'conversions' => (int) $day->sum('conversions'),
                'impressions' => (int) $day->sum('impressions'),
                'clicks' => (int) $day->sum('clicks'),
                'cpa' => $day->where('cpa', '>', 0)->avg('cpa') ? round($day->where('cpa', '>', 0)->avg('cpa'), 2) : null,
                'ctr' => $day->where('ctr', '>', 0)->avg('ctr') ? round($day->where('ctr', '>', 0)->avg('ctr') * 100, 2) : null,
            ]);

            return response()->json($metrics->values());
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function strategy()
    {
        $profiles = $this->strategyService->getAllProfiles();
        $activeProfile = $this->strategyService->getActiveProfile();
        $automationLevels = config('ad_os.automation_levels');
        $objectives = config('ad_os.objectives');

        return view('backend.ads.strategy', compact('profiles', 'activeProfile', 'automationLevels', 'objectives'));
    }

    public function updateStrategy(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'automation_level' => 'required|in:manual,assisted,supervised,full_operator',
            'primary_goal' => 'required',
            'budget.monthly_max' => 'required|numeric|min:0',
            'budget.daily_max' => 'required|numeric|min:0',
        ]);

        $this->strategyService->createOrUpdateProfile($request->all());

        return redirect()->route('admin.ads.strategy')
            ->with('alert_type', 'success')
            ->with('message', 'Strategiprofil oppdatert');
    }

    public function campaigns(Request $request)
    {
        $filters = $request->only(['status', 'platform', 'account_id']);
        $campaigns = $this->campaignService->getAllCampaigns($filters);

        return view('backend.ads.campaigns', compact('campaigns', 'filters'));
    }

    public function showCampaign(int $id)
    {
        $campaign = $this->campaignService->getCampaign($id);
        $optimizationService = app(AdOptimizationService::class);
        $analysis = $optimizationService->analyzeCampaign($campaign);

        return view('backend.ads.campaign-show', compact('campaign', 'analysis'));
    }

    public function creatives(Request $request)
    {
        $filters = $request->only(['status', 'platform']);
        $creatives = $this->creativeService->getAllCreatives($filters);

        return view('backend.ads.creatives', compact('creatives', 'filters'));
    }

    public function generateCreatives(Request $request)
    {
        $request->validate([
            'product' => 'required|string',
            'audience' => 'required|string',
            'goal' => 'required|string',
            'platform' => 'required|string',
        ]);

        GenerateAdCreativesJob::dispatch($request->all());

        return redirect()->route('admin.ads.creatives')
            ->with('alert_type', 'success')
            ->with('message', 'Kreativgenerering startet - resultater kommer snart');
    }

    public function recommendations()
    {
        $recommendations = $this->decisionService->getRecommendations(50);
        $riskLevels = config('ad_os.risk_levels');

        return view('backend.ads.recommendations', compact('recommendations', 'riskLevels'));
    }

    public function handleRecommendation(Request $request, int $id)
    {
        $action = $request->input('action');

        if ($action === 'approve') {
            $approval = $this->approvalService->createApprovalRequest($id);
            $this->approvalService->approve($approval->id, $request->input('notes'));
            $message = 'Anbefaling godkjent og utført';
        } else {
            $decision = \App\Models\AdOs\AdAiDecision::findOrFail($id);
            $decision->update(['status' => 'rejected']);
            $message = 'Anbefaling avvist';
        }

        return redirect()->route('admin.ads.recommendations')
            ->with('alert_type', 'success')
            ->with('message', $message);
    }

    public function approvals()
    {
        $approvals = $this->approvalService->getPendingApprovals();

        return view('backend.ads.approvals', compact('approvals'));
    }

    public function approve(Request $request, int $id)
    {
        $result = $this->approvalService->approve($id, $request->input('notes'));

        return redirect()->route('admin.ads.approvals')
            ->with('alert_type', $result['success'] ? 'success' : 'error')
            ->with('message', $result['success'] ? 'Handling godkjent og utført' : 'Feil: ' . ($result['error'] ?? ''));
    }

    public function reject(Request $request, int $id)
    {
        $this->approvalService->reject($id, $request->input('reason'));

        return redirect()->route('admin.ads.approvals')
            ->with('alert_type', 'success')
            ->with('message', 'Handling avvist');
    }

    public function experiments()
    {
        $experiments = AdExperiment::with(['variants', 'campaign', 'winner'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('backend.ads.experiments', compact('experiments'));
    }

    public function logs(Request $request)
    {
        $filters = $request->only(['action_type', 'triggered_by', 'target_type', 'date_from', 'date_to']);
        $logs = $this->logService->getLogs($filters);
        $actionTypes = $this->logService->getActionTypes();

        return view('backend.ads.logs', compact('logs', 'filters', 'actionTypes'));
    }

    public function rules()
    {
        $rules = $this->ruleService->getAllRules();

        return view('backend.ads.rules', compact('rules'));
    }

    public function storeRule(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'metric' => 'required|string',
            'operator' => 'required|in:<,<=,>,>=,==,!=',
            'threshold' => 'required|numeric',
            'action' => 'required|string',
            'risk_level' => 'required|in:low,medium,high,critical',
        ]);

        $this->ruleService->createRule($request->all());

        return redirect()->route('admin.ads.rules')
            ->with('alert_type', 'success')
            ->with('message', 'Regel opprettet');
    }

    public function updateRule(Request $request, int $id)
    {
        $rule = \App\Models\AdOs\AdRule::findOrFail($id);
        $rule->update($request->all());

        return redirect()->route('admin.ads.rules')
            ->with('alert_type', 'success')
            ->with('message', 'Regel oppdatert');
    }

    public function deleteRule(int $id)
    {
        \App\Models\AdOs\AdRule::findOrFail($id)->delete();

        return redirect()->route('admin.ads.rules')
            ->with('alert_type', 'success')
            ->with('message', 'Regel slettet');
    }

    public function toggleKillSwitch()
    {
        $profile = $this->strategyService->getActiveProfile();
        if ($profile?->approvalPolicy) {
            $current = $profile->approvalPolicy->emergency_kill_switch;
            $profile->approvalPolicy->update(['emergency_kill_switch' => !$current]);

            \App\Models\AdOs\AdActionLog::log('kill_switch_toggled', [
                'triggered_by' => 'human',
                'user_id' => auth()->id(),
                'payload' => ['enabled' => !$current],
            ]);
        }

        return redirect()->route('admin.ads.dashboard')
            ->with('alert_type', 'success')
            ->with('message', 'Kill switch ' . (!$current ? 'aktivert' : 'deaktivert'));
    }

    // ─── AI Strategist ───────────────────────────────────────

    public function strategist()
    {
        $history = $this->strategistService->getHistory(20);

        return view('backend.ads.strategist', compact('history'));
    }

    public function strategistAsk(Request $request)
    {
        $request->validate([
            'instruction' => 'required|string|min:5|max:2000',
        ]);

        $result = $this->strategistService->processInstruction($request->input('instruction'));

        return response()->json($result);
    }

    public function strategistExecute(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|integer|exists:ad_strategist_conversations,id',
            'approved_actions' => 'required|array|min:1',
            'approved_actions.*' => 'integer|min:0',
        ]);

        $results = $this->strategistService->executeActions(
            $request->input('conversation_id'),
            $request->input('approved_actions')
        );

        return response()->json([
            'success' => true,
            'results' => $results,
        ]);
    }
}
