@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-cogs"></i> Ad OS - Strategi & Guardrails</h3>
    <a href="{{ route('admin.ads.dashboard') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake</a>
</div>

<div class="col-md-12">
    @if(session('message'))
        <div class="alert alert-{{ session('alert_type', 'info') }}">{{ session('message') }}</div>
    @endif

    <form action="{{ route('admin.ads.strategy.update') }}" method="POST">
        @csrf
        @if($activeProfile)
            <input type="hidden" name="id" value="{{ $activeProfile->id }}">
        @endif

        {{-- Profile Basics --}}
        <div class="panel panel-default">
            <div class="panel-heading"><strong>Strategiprofil</strong></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profilnavn</label>
                            <input type="text" name="name" class="form-control" value="{{ $activeProfile->name ?? 'Hovedstrategi' }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Automasjonsnivå</label>
                            <select name="automation_level" class="form-control">
                                @foreach($automationLevels as $key => $level)
                                    <option value="{{ $key }}" {{ ($activeProfile->automation_level ?? 'assisted') === $key ? 'selected' : '' }}>
                                        {{ $level['label'] }} - {{ $level['description'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Primærmål</label>
                            <select name="primary_goal" class="form-control">
                                @foreach($objectives as $key => $label)
                                    <option value="{{ $key }}" {{ ($activeProfile->primary_goal ?? 'leads') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Mål-CPA (kr)</label>
                            <input type="number" name="target_cpa" class="form-control" step="0.01" value="{{ $activeProfile->target_cpa ?? '' }}" placeholder="f.eks. 150">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Mål-ROAS</label>
                            <input type="number" name="target_roas" class="form-control" step="0.01" value="{{ $activeProfile->target_roas ?? '' }}" placeholder="f.eks. 3.0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Min. konverteringsvolum</label>
                            <input type="number" name="min_conversion_volume" class="form-control" value="{{ $activeProfile->min_conversion_volume ?? '' }}" placeholder="f.eks. 10">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Risikotoleranse</label>
                            <select name="risk_tolerance" class="form-control">
                                <option value="low" {{ ($activeProfile->risk_tolerance ?? 'medium') === 'low' ? 'selected' : '' }}>Lav</option>
                                <option value="medium" {{ ($activeProfile->risk_tolerance ?? 'medium') === 'medium' ? 'selected' : '' }}>Middels</option>
                                <option value="high" {{ ($activeProfile->risk_tolerance ?? 'medium') === 'high' ? 'selected' : '' }}>Høy</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Notater</label>
                    <textarea name="notes" class="form-control" rows="2">{{ $activeProfile->notes ?? '' }}</textarea>
                </div>
                <input type="hidden" name="is_active" value="1">
            </div>
        </div>

        {{-- Budget Policy --}}
        <div class="panel panel-default">
            <div class="panel-heading"><strong><i class="fa fa-money"></i> Budsjettpolicy</strong></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Maks månedlig budsjett (kr)</label>
                            <input type="number" name="budget[monthly_max]" class="form-control" step="0.01" value="{{ $activeProfile->budgetPolicy->monthly_max ?? '10000' }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Maks daglig budsjett (kr)</label>
                            <input type="number" name="budget[daily_max]" class="form-control" step="0.01" value="{{ $activeProfile->budgetPolicy->daily_max ?? '500' }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Maks økning per dag (%)</label>
                            <input type="number" name="budget[max_increase_per_day_percent]" class="form-control" step="0.01" value="{{ $activeProfile->budgetPolicy->max_increase_per_day_percent ?? '15' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Maks økning per uke (%)</label>
                            <input type="number" name="budget[max_increase_per_week_percent]" class="form-control" step="0.01" value="{{ $activeProfile->budgetPolicy->max_increase_per_week_percent ?? '30' }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Maks budsjett per kampanje (kr)</label>
                            <input type="number" name="budget[max_single_campaign_budget]" class="form-control" step="0.01" value="{{ $activeProfile->budgetPolicy->max_single_campaign_budget ?? '' }}" placeholder="Valgfritt">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Min budsjett per kampanje (kr)</label>
                            <input type="number" name="budget[min_campaign_budget]" class="form-control" step="0.01" value="{{ $activeProfile->budgetPolicy->min_campaign_budget ?? '50' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" style="padding-top: 25px;">
                            <label>
                                <input type="checkbox" name="budget[allow_auto_rebalance]" value="1" {{ ($activeProfile->budgetPolicy->allow_auto_rebalance ?? false) ? 'checked' : '' }}>
                                Tillat automatisk rebalansering
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Risk Policy --}}
        <div class="panel panel-default">
            <div class="panel-heading"><strong><i class="fa fa-exclamation-triangle"></i> Risikopolicy</strong></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Stop-loss daglig (kr)</label>
                            <input type="number" name="risk[stop_loss_daily]" class="form-control" step="0.01" value="{{ $activeProfile->riskPolicy->stop_loss_daily ?? '' }}" placeholder="Valgfritt">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Stop-loss ukentlig (kr)</label>
                            <input type="number" name="risk[stop_loss_weekly]" class="form-control" step="0.01" value="{{ $activeProfile->riskPolicy->stop_loss_weekly ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Maks CPA (kr)</label>
                            <input type="number" name="risk[max_cpa_threshold]" class="form-control" step="0.01" value="{{ $activeProfile->riskPolicy->max_cpa_threshold ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Min ROAS</label>
                            <input type="number" name="risk[min_roas_threshold]" class="form-control" step="0.01" value="{{ $activeProfile->riskPolicy->min_roas_threshold ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Maks forbruk uten konv. (kr)</label>
                            <input type="number" name="risk[max_spend_without_conversion]" class="form-control" step="0.01" value="{{ $activeProfile->riskPolicy->max_spend_without_conversion ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Min visninger før eval.</label>
                            <input type="number" name="risk[min_impressions_before_eval]" class="form-control" value="{{ $activeProfile->riskPolicy->min_impressions_before_eval ?? '1000' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" style="padding-top: 25px;">
                            <label>
                                <input type="checkbox" name="risk[auto_pause_losers]" value="1" {{ ($activeProfile->riskPolicy->auto_pause_losers ?? false) ? 'checked' : '' }}>
                                Auto-pause tapere
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" style="padding-top: 25px;">
                            <label>
                                <input type="checkbox" name="risk[auto_scale_winners]" value="1" {{ ($activeProfile->riskPolicy->auto_scale_winners ?? false) ? 'checked' : '' }}>
                                Auto-skaler vinnere
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Approval Policy --}}
        <div class="panel panel-default">
            <div class="panel-heading"><strong><i class="fa fa-check-circle"></i> Godkjenningspolicy</strong></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3"><label><input type="checkbox" name="approval[approve_new_campaigns]" value="1" {{ ($activeProfile->approvalPolicy->approve_new_campaigns ?? true) ? 'checked' : '' }}> Godkjenn nye kampanjer</label></div>
                    <div class="col-md-3"><label><input type="checkbox" name="approval[approve_budget_increase]" value="1" {{ ($activeProfile->approvalPolicy->approve_budget_increase ?? true) ? 'checked' : '' }}> Godkjenn budsjettøkninger</label></div>
                    <div class="col-md-3"><label><input type="checkbox" name="approval[approve_pause_campaigns]" value="1" {{ ($activeProfile->approvalPolicy->approve_pause_campaigns ?? false) ? 'checked' : '' }}> Godkjenn pausing</label></div>
                    <div class="col-md-3"><label><input type="checkbox" name="approval[approve_new_creatives]" value="1" {{ ($activeProfile->approvalPolicy->approve_new_creatives ?? true) ? 'checked' : '' }}> Godkjenn nye kreative</label></div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-3"><label><input type="checkbox" name="approval[approve_targeting_changes]" value="1" {{ ($activeProfile->approvalPolicy->approve_targeting_changes ?? true) ? 'checked' : '' }}> Godkjenn målgruppendringer</label></div>
                    <div class="col-md-3"><label><input type="checkbox" name="approval[approve_major_reallocation]" value="1" {{ ($activeProfile->approvalPolicy->approve_major_reallocation ?? true) ? 'checked' : '' }}> Godkjenn store omfordelinger</label></div>
                    <div class="col-md-3">
                        <label>Budsjett-godkjenning over (%)</label>
                        <input type="number" name="approval[budget_increase_approval_threshold_percent]" class="form-control input-sm" step="0.01" value="{{ $activeProfile->approvalPolicy->budget_increase_approval_threshold_percent ?? '20' }}">
                    </div>
                    <div class="col-md-3">
                        <label>Omfordeling-godkjenning over (%)</label>
                        <input type="number" name="approval[major_reallocation_threshold_percent]" class="form-control input-sm" step="0.01" value="{{ $activeProfile->approvalPolicy->major_reallocation_threshold_percent ?? '25' }}">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Lagre strategi</button>
    </form>
</div>
@stop
