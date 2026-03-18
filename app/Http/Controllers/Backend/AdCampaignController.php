<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use App\Models\AdCampaignStat;
use App\Services\FacebookAdsService;
use App\Services\GoogleAdsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdCampaignController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'overview');

        $campaigns = AdCampaign::with('latestStats')
            ->when($request->input('platform'), fn ($q, $p) => $q->where('platform', $p))
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate(20);

        // Statistikk-data
        $stats = [
            'active_count' => AdCampaign::active()->count(),
            'total_spend' => AdCampaignStat::sum('spend'),
            'total_leads' => AdCampaignStat::sum('leads'),
            'total_clicks' => AdCampaignStat::sum('clicks'),
            'fb_active' => AdCampaign::active()->facebook()->count(),
            'google_active' => AdCampaign::active()->google()->count(),
        ];

        $webinars = \App\FreeWebinar::orderByDesc('start_date')->limit(20)->get();

        return view('backend.ads.index', compact('campaigns', 'tab', 'stats', 'webinars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'platform' => 'required|in:facebook,google',
            'type' => 'required|in:lead,retargeting,search,display',
            'name' => 'required|string|max:255',
            'daily_budget' => 'required|numeric|min:50',
        ]);

        $campaign = AdCampaign::create([
            'platform' => $request->platform,
            'type' => $request->type,
            'name' => $request->name,
            'free_webinar_id' => $request->free_webinar_id,
            'daily_budget' => $request->daily_budget,
            'status' => 'draft',
            'config' => [
                'audience' => $request->input('audience', 'all'),
                'headline' => $request->input('headline'),
                'text' => $request->input('ad_text'),
                'keywords' => $request->input('keywords'),
                'age_min' => $request->input('age_min', 25),
                'age_max' => $request->input('age_max', 65),
            ],
        ]);

        return redirect()->route('admin.ads.index', ['tab' => 'overview'])
            ->with('success', "Kampanje \"{$campaign->name}\" opprettet som utkast.");
    }

    public function activate(AdCampaign $campaign)
    {
        try {
            if ($campaign->platform === 'facebook' && $campaign->external_campaign_id) {
                $fb = app(FacebookAdsService::class);
                $fb->activateCampaign($campaign->external_campaign_id);
            }

            $campaign->update([
                'status' => 'active',
                'started_at' => $campaign->started_at ?? now(),
            ]);

            return back()->with('success', "Kampanje \"{$campaign->name}\" aktivert.");
        } catch (\Exception $e) {
            Log::error('Kunne ikke aktivere kampanje', ['id' => $campaign->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Feil ved aktivering: ' . $e->getMessage());
        }
    }

    public function pause(AdCampaign $campaign)
    {
        try {
            if ($campaign->platform === 'facebook' && $campaign->external_campaign_id) {
                $fb = app(FacebookAdsService::class);
                $fb->pauseCampaign($campaign->external_campaign_id);
            }

            $campaign->update(['status' => 'paused']);

            return back()->with('success', "Kampanje \"{$campaign->name}\" pauset.");
        } catch (\Exception $e) {
            Log::error('Kunne ikke pause kampanje', ['id' => $campaign->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Feil ved pausing: ' . $e->getMessage());
        }
    }

    public function destroy(AdCampaign $campaign)
    {
        if ($campaign->status === 'active') {
            return back()->with('error', 'Kan ikke slette en aktiv kampanje. Paus den først.');
        }

        $campaign->delete();
        return back()->with('success', 'Kampanje slettet.');
    }

    public function syncStats(AdCampaign $campaign)
    {
        try {
            if ($campaign->platform === 'facebook' && $campaign->external_campaign_id) {
                $fb = app(FacebookAdsService::class);
                $insights = $fb->getCampaignStats($campaign->external_campaign_id);

                if ($insights) {
                    AdCampaignStat::updateOrCreate(
                        ['ad_campaign_id' => $campaign->id, 'date' => today()],
                        [
                            'impressions' => $insights['impressions'] ?? 0,
                            'clicks' => $insights['clicks'] ?? 0,
                            'leads' => $insights['actions']['lead'] ?? 0,
                            'spend' => $insights['spend'] ?? 0,
                            'cpl' => $insights['cost_per_lead'] ?? null,
                        ]
                    );
                }
            }

            return back()->with('success', 'Statistikk oppdatert.');
        } catch (\Exception $e) {
            Log::error('Kunne ikke synke stats', ['id' => $campaign->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Feil ved synkronisering: ' . $e->getMessage());
        }
    }

    public function launchFacebookLead(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'free_webinar_id' => 'required|exists:free_webinars,id',
            'daily_budget' => 'required|numeric|min:50',
            'headline' => 'required|string|max:255',
            'ad_text' => 'required|string',
        ]);

        $webinar = \App\FreeWebinar::findOrFail($request->free_webinar_id);

        try {
            $fb = app(FacebookAdsService::class);
            $result = $fb->createWebinarLeadCampaign([
                'name' => $request->name,
                'webinar_title' => $webinar->title,
                'webinar_date' => $webinar->start_date?->format('d.m.Y'),
                'webinar_time' => $webinar->start_date?->format('H:i'),
                'daily_budget' => $request->daily_budget * 100, // FB bruker cents
                'headline' => $request->headline,
                'text' => $request->ad_text,
                'image_url' => $webinar->image ? asset('storage/' . $webinar->image) : null,
                'landing_url' => url('/gratis-webinar/' . $webinar->id),
            ]);

            $campaign = AdCampaign::create([
                'platform' => 'facebook',
                'type' => 'lead',
                'name' => $request->name,
                'free_webinar_id' => $webinar->id,
                'daily_budget' => $request->daily_budget,
                'status' => 'active',
                'started_at' => now(),
                'external_campaign_id' => $result['campaign_id'] ?? null,
                'external_adset_id' => $result['adset_id'] ?? null,
                'external_ad_id' => $result['ad_id'] ?? null,
                'external_form_id' => $result['form_id'] ?? null,
                'config' => [
                    'headline' => $request->headline,
                    'text' => $request->ad_text,
                ],
            ]);

            return redirect()->route('admin.ads.index')
                ->with('success', "Facebook Lead Ad \"{$campaign->name}\" opprettet og aktivert!");

        } catch (\Exception $e) {
            Log::error('Facebook Lead Ad feilet', ['error' => $e->getMessage()]);
            return back()->with('error', 'Feil: ' . $e->getMessage())->withInput();
        }
    }

    public function generateAdText(Request $request)
    {
        $webinar = null;
        if ($request->free_webinar_id) {
            $webinar = \App\FreeWebinar::find($request->free_webinar_id);
        }

        $googleService = app(GoogleAdsService::class);
        $texts = $googleService->generateAdTexts([
            'title' => $webinar?->title ?? $request->input('title', 'Gratiswebinar'),
            'description' => $webinar?->description ?? '',
        ]);

        return response()->json($texts);
    }
}
