@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Dashboard &rsaquo; Indiemoon</title>
@stop

@section('breadcrumbs')
    <span class="bc-current">Dashboard</span>
@stop

@section('styles')
<style>
    /* ── Welcome Banner ── */
    .dash-welcome {
        background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand-primary) 55%, var(--brand-light) 100%);
        border-radius: var(--radius);
        padding: 30px 34px;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 6px 24px rgba(134,39,54,.2);
    }
    .dash-welcome::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,.05);
    }
    .dash-welcome::after {
        content: '';
        position: absolute;
        bottom: -60px; right: 80px;
        width: 140px; height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,.04);
    }
    .dash-welcome h1 {
        font-family: var(--font-display);
        font-size: 24px;
        font-weight: 600;
        margin: 0 0 6px;
    }
    .dash-welcome p {
        opacity: .85;
        font-size: 14px;
        margin-bottom: 18px;
    }
    .dash-welcome-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }
    .dash-welcome-actions a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: rgba(255,255,255,.15);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255,255,255,.2);
        border-radius: var(--radius-sm);
        color: #fff;
        font-size: 13px;
        font-weight: 500;
        transition: all .2s;
    }
    .dash-welcome-actions a:hover {
        background: rgba(255,255,255,.25);
        color: #fff;
        text-decoration: none;
    }

    /* ── Next Step Banner ── */
    .next-step-banner {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 20px;
        background: #fef9e7;
        border: 1px solid #f5dfa0;
        border-left: 4px solid var(--warning);
        border-radius: var(--radius-sm);
        margin-bottom: 24px;
        font-size: 13.5px;
    }
    .next-step-banner > i { color: var(--warning); font-size: 18px; flex-shrink: 0; }
    .next-step-banner strong { color: var(--text); }
    .next-step-banner .btn-step {
        margin-left: auto;
        padding: 6px 14px;
        background: var(--brand-primary);
        color: #fff;
        border-radius: var(--radius-sm);
        font-size: 12.5px;
        font-weight: 500;
        white-space: nowrap;
        transition: background .2s;
        text-decoration: none;
    }
    .next-step-banner .btn-step:hover { background: var(--brand-light); color: #fff; }

    /* ── Stats Row ── */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        box-shadow: var(--shadow-sm);
        transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }
    .stat-icon {
        width: 42px; height: 42px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 17px;
        flex-shrink: 0;
    }
    .stat-icon.books { background: #fdf0f2; color: var(--brand-primary); }
    .stat-icon.published { background: #eaf7f0; color: var(--success); }
    .stat-icon.sales { background: #fef9e7; color: var(--warning); }
    .stat-icon.steps { background: #eaf2fa; color: var(--info); }
    .stat-value {
        font-family: var(--font-display);
        font-size: 26px;
        font-weight: 600;
        line-height: 1.1;
        color: var(--text);
    }
    .stat-label { font-size: 12px; color: var(--muted); margin-top: 2px; }

    /* ── Inventory ── */
    .inventory-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }
    .inventory-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        background: var(--bg);
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-light);
    }
    .inventory-isbn {
        font-family: 'SF Mono', 'Fira Code', monospace;
        font-size: 12.5px;
        color: var(--brand-primary);
        font-weight: 500;
    }
    .inventory-balance {
        font-family: var(--font-display);
        font-size: 22px;
        font-weight: 600;
        color: var(--text);
    }
    .inventory-label { font-size: 11px; color: var(--muted); }

    /* ── Activity Feed ── */
    .activity-list { list-style: none; padding: 0; margin: 0; }
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-light);
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-icon {
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }
    .activity-icon.upload { background: #eaf2fa; color: var(--info); }
    .activity-icon.check { background: #eaf7f0; color: var(--success); }
    .activity-icon.edit { background: #fef9e7; color: var(--warning); }
    .activity-title { font-size: 13.5px; color: var(--text); }
    .activity-time { font-size: 12px; color: var(--muted); margin-top: 2px; }

    /* ── Responsive ── */
    @media (max-width: 1100px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .stats-row { grid-template-columns: 1fr; }
        .dash-welcome { padding: 22px 20px; }
        .dash-welcome h1 { font-size: 20px; }
        .dash-welcome-actions a { font-size: 12px; padding: 7px 12px; }
        .next-step-banner { flex-wrap: wrap; }
        .next-step-banner .btn-step { margin-left: 0; margin-top: 8px; }
    }
</style>
@stop

@section('content')

    {{-- ═══ Welcome Banner ═══ --}}
    <div class="dash-welcome sp-anim sp-d1">
        <h1>{{ trans('site.author-portal.welcome-back') ?? 'Velkommen tilbake' }}, {{ auth()->user()->first_name ?? '' }} 👋</h1>
        <p>{{ trans('site.author-portal.dashboard-intro') ?? 'Her er en oversikt over bokprosjektene dine. Hva vil du jobbe med i dag?' }}</p>
        <div class="dash-welcome-actions">
            <a href="{{ route('learner.progress-plan') ?? '#' }}">
                <i class="fas fa-tasks"></i> {{ trans('site.author-portal.progress-plan') ?? 'Fremdriftsplan' }}
            </a>
            @if ($projects->count() < 1)
                <a href="#" data-bs-toggle="modal" data-bs-target="#projectModal">
                    <i class="fas fa-plus"></i> {{ trans('site.author-portal.add-book-project') ?? 'Ny bok' }}
                </a>
            @endif
            <a href="#">
                <i class="fas fa-chart-bar"></i> {{ trans('site.author-portal.view-sales') ?? 'Se salg' }}
            </a>
        </div>
    </div>

    {{-- ═══ Next Step Banner (optional – show if $nextStep exists) ═══ --}}
    @if(isset($nextStep))
        <div class="next-step-banner sp-anim sp-d2">
            <i class="fas fa-lightbulb"></i>
            <div>
                <strong>{{ trans('site.author-portal.next-step') ?? 'Neste steg' }}:</strong>
                {{ $nextStep->description }}
            </div>
            <a href="{{ $nextStep->url ?? '#' }}" class="btn-step">
                {{ trans('site.author-portal.go-to-step') ?? 'Gå til steg' }} →
            </a>
        </div>
    @endif

    {{-- ═══ Stats Row ═══ --}}
    <div class="stats-row sp-anim sp-d3">
        <div class="stat-card">
            <div class="stat-icon books"><i class="fas fa-book-open"></i></div>
            <div>
                <div class="stat-value">{{ $projects->count() }}</div>
                <div class="stat-label">{{ trans('site.author-portal.book-projects') ?? 'Bokprosjekter' }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon published"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="stat-value">{{ $projects->where('status', 'finished')->count() }}</div>
                <div class="stat-label">{{ trans('site.author-portal.published') ?? 'Publisert' }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon sales"><i class="fas fa-coins"></i></div>
            <div>
                <div class="stat-value">{{ $totalSold ?? 0 }}</div>
                <div class="stat-label">{{ trans('site.author-portal.total-sold') ?? 'Totalt solgt' }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon steps"><i class="fas fa-hourglass-half"></i></div>
            <div>
                <div class="stat-value">{{ $pendingSteps ?? 0 }}</div>
                <div class="stat-label">{{ trans('site.author-portal.steps-remaining') ?? 'Steg gjenstår' }}</div>
            </div>
        </div>
    </div>

    {{-- ═══ Projects Table ═══ --}}
    <div class="sp-card sp-anim sp-d4">
        <div class="sp-card-header">
            <h2>{{ trans('site.author-portal.book-project') }}</h2>
            @if ($projects->count() < 1)
                <button class="sp-btn sp-btn-primary" data-bs-toggle="modal" data-bs-target="#projectModal">
                    <i class="fas fa-plus"></i> {{ trans('site.author-portal.add-book-project') }}
                </button>
            @endif
        </div>
        <div class="sp-card-body" style="padding: 0;">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>{{ trans('site.author-portal.project-number') }}</th>
                        <th>{{ trans('site.author-portal.project-name') }}</th>
                        <th>{{ trans('site.description') }}</th>
                        <th>{{ trans('site.status') }}</th>
                        <th>{{ trans('site.author-portal.standard-project') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                        <tr>
                            <td style="font-family: 'SF Mono', monospace; font-size: 12.5px; color: var(--muted);">
                                {{ $project->identifier }}
                            </td>
                            <td>
                                <a href="{{ route('learner.project.show', $project->id) }}">
                                    {{ $project->name }}
                                </a>
                            </td>
                            <td style="color: var(--text-secondary); font-size: 13px;">
                                {{ $project->description }}
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: var(--text-secondary);">
                                    {{ $project->start_date }}
                                    @if($project->end_date)
                                        – {{ $project->end_date }}
                                    @endif
                                </span>
                                <br>
                                @if($project->status === 'active')
                                    <span class="sp-badge sp-badge-active">
                                        {{ trans('site.author-portal.active') }}
                                    </span>
                                @elseif ($project->status === 'lead')
                                    <span class="sp-badge sp-badge-lead">Lead</span>
                                @elseif($project->status === 'finished')
                                    <span class="sp-badge sp-badge-finished">Finished</span>
                                @endif
                            </td>
                            <td>
                                @if ($project->is_standard)
                                    <span class="sp-badge sp-badge-current">
                                        {{ trans('site.author-portal.current') }}
                                    </span>
                                @else
                                    <button class="sp-btn sp-btn-outline sp-btn-sm standardProjectBtn"
                                            data-bs-toggle="modal"
                                            data-action="{{ route('learner.project.set-standard', $project->id) }}"
                                            data-bs-target="#standardProjectModal">
                                        {{ trans('site.author-portal.set-standard') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══ Two Column: Inventory + Activity ═══ --}}
    <div class="sp-grid-2col">

        {{-- Inventory --}}
        @if (count($inventorySummaries))
            <div class="sp-card sp-anim sp-d5">
                <div class="sp-card-header">
                    <h2>{{ trans('site.author-portal.inventory') ?? 'Lagerstatus' }}</h2>
                </div>
                <div class="sp-card-body">
                    <div class="inventory-grid">
                        @foreach ($inventorySummaries as $inventorySummary)
                            <div class="inventory-item">
                                <div>
                                    <div class="inventory-isbn">{{ $inventorySummary['isbn'] }}</div>
                                    <div class="inventory-label">{{ $inventorySummary['title'] ?? '' }}</div>
                                </div>
                                <div style="text-align:right;">
                                    <div class="inventory-balance">{{ $inventorySummary['total_balance'] }}</div>
                                    <div class="inventory-label">{{ trans('site.author-portal.in-stock') ?? 'på lager' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Activity Feed --}}
        @if(isset($activities) && count($activities))
            <div class="sp-card sp-anim sp-d6">
                <div class="sp-card-header">
                    <h2>{{ trans('site.author-portal.recent-activity') ?? 'Siste aktivitet' }}</h2>
                </div>
                <div class="sp-card-body">
                    <ul class="activity-list">
                        @foreach($activities as $activity)
                            <li class="activity-item">
                                <div class="activity-icon {{ $activity->type ?? 'check' }}">
                                    <i class="fas fa-{{ $activity->icon ?? 'check' }}"></i>
                                </div>
                                <div>
                                    <div class="activity-title">{{ $activity->description }}</div>
                                    <div class="activity-time">
                                        <i class="far fa-clock"></i> {{ $activity->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    {{-- ═══ MODALS ═══ --}}

    {{-- Add Project Modal --}}
    <div id="projectModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        {{ trans('site.author-portal.add-book-project') }}
                    </h4>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('learner.save-project') }}" 
                          onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans('site.author-portal.project-name') }}</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.description') }}</label>
                            <textarea name="description" cols="30" rows="6" class="form-control"></textarea>
                        </div>
                        <div class="text-end">
                            <button class="sp-btn sp-btn-primary" type="submit">
                                {{ trans('site.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Set Standard Project Modal --}}
    <div id="standardProjectModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        {{ trans('site.author-portal.standard-project') }}
                    </h4>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <p>
                            {{ trans('site.author-portal.confirm-set-standard') ?? 'Er du sikker på at du vil sette dette prosjektet som standard?' }}
                        </p>
                        <div class="text-end">
                            <button class="sp-btn sp-btn-primary" type="submit">
                                {{ trans('site.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
<script>
    // Set Standard Project modal action
    $(".standardProjectBtn").click(function() {
        var action = $(this).data('action');
        $("#standardProjectModal").find("form").attr("action", action);
    });

    // Animate progress bars if present
    $(function() {
        $('.progress-bar[data-width]').each(function() {
            var w = $(this).data('width') + '%';
            $(this).animate({ width: w }, 900);
        });
    });
</script>
@stop
