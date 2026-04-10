@extends('frontend.learner.self-publishing.layout')

@section('page_title', 'Progress Plan &rsaquo; Forfatterskolen')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&display=swap');
    :root {
        --brand: #7B1A1A;
        --brand-light: #9B2525;
        --gold: #B8973A;
        --gold-light: #D4AF5A;
        --bg: #F8F6F2;
        --surface: #FFFFFF;
        --border: #E8E2D8;
        --text: #1A1410;
        --muted: #7A6F63;
        --finished: #2E7D52;
        --started: #B8973A;
        --not-started: #9A8E83;
        --not-planned: #C8BFB5;
    }

    .card-global.progress-plan-card {
        background: var(--bg);
        border: none;
        box-shadow: none;
    }

    .progress-plan-card .card-header {
        background: transparent;
        border-bottom: none;
        font-family: 'Playfair Display', serif;
        font-size: 28px;
        font-weight: 600;
        letter-spacing: -0.01em;
        color: var(--text);
        padding: 18px 6px 6px;
    }

    .progress-plan-card .card-body {
        padding: 8px 6px 22px;
    }

    .progress-plan-subtitle {
        color: var(--muted);
        font-size: 13.5px;
        margin-bottom: 28px;
    }

    .summary-bar {
        display: flex;
        gap: 20px;
        margin-bottom: 44px;
        padding: 20px 24px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
    }

    .summary-stat {
        flex: 1;
        text-align: center;
    }

    .summary-stat .val {
        font-size: 26px;
        font-family: 'Playfair Display', serif;
        font-weight: 600;
        line-height: 1.1;
    }

    .summary-stat .lbl {
        font-size: 11px;
        color: var(--muted);
        margin-top: 3px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .summary-stat.green .val {
        color: var(--finished);
    }

    .summary-stat.gold .val {
        color: var(--gold);
    }

    .summary-stat.gray .val {
        color: var(--not-started);
    }

    .summary-stat.brand .val {
        color: var(--brand);
    }

    .divider {
        width: 1px;
        background: var(--border);
    }

    .progress-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 40px;
    }

    .progress-track {
        flex: 1;
        background: var(--border);
        border-radius: 99px;
        height: 6px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--brand) 0%, var(--gold-light) 100%);
        border-radius: 99px;
        transition: width .8s ease;
    }

    .progress-percent {
        font-size: 12px;
        color: var(--muted);
        white-space: nowrap;
        font-weight: 500;
    }

    .timeline {
        position: relative;
        padding-left: 56px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 18px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: linear-gradient(180deg, var(--brand) 0%, var(--border) 100%);
    }

    .tl-item {
        position: relative;
        margin-bottom: 12px;
    }

    .tl-dot {
        position: absolute;
        left: -46px;
        top: 50%;
        transform: translateY(-50%);
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid var(--border);
        background: var(--surface);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        color: var(--muted);
        z-index: 2;
    }

    .tl-dot.finished {
        background: var(--finished);
        border-color: var(--finished);
        color: #fff;
    }

    .tl-dot.started {
        background: var(--gold);
        border-color: var(--gold);
        color: #fff;
    }

    .tl-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        transition: box-shadow .2s, transform .2s;
        text-decoration: none;
    }

    .tl-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, .07);
        transform: translateX(3px);
        text-decoration: none;
    }

    .tl-card.highlight-brand {
        border-left: 3px solid var(--brand);
    }

    .tl-card.highlight-gold {
        border-left: 3px solid var(--gold);
    }

    .tl-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .tl-step-num {
        font-size: 11px;
        font-weight: 700;
        color: var(--not-started);
        letter-spacing: 0.06em;
        min-width: 28px;
    }

    .tl-name {
        font-size: 14.5px;
        font-weight: 500;
        color: var(--text);
    }

    .tl-right {
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .tl-date {
        font-size: 12px;
        color: var(--muted);
        white-space: nowrap;
        text-align: left;
    }

    .tl-date span {
        display: block;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--not-planned);
        margin-bottom: 2px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 99px;
        font-size: 11.5px;
        font-weight: 600;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }

    .badge::before {
        content: '';
        display: block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .badge.finished {
        background: #EDF7F2;
        color: var(--finished);
    }

    .badge.finished::before {
        background: var(--finished);
    }

    .badge.started {
        background: #FDF8EC;
        color: #8A6B20;
    }

    .badge.started::before {
        background: var(--gold);
    }

    .badge.not-started {
        background: #F4F2EF;
        color: var(--not-started);
    }

    .badge.not-started::before {
        background: var(--not-started);
    }

    .badge.not-planned {
        background: #F4F2EF;
        color: var(--not-planned);
    }

    .badge.not-planned::before {
        background: var(--not-planned);
    }

    @media (max-width: 991px) {
        .summary-stat .val,
        .tl-name,
        .tl-date,
        .badge,
        .progress-percent,
        .tl-step-num {
            font-size: revert;
        }

        .tl-date span {
            font-size: 10px;
        }

        .tl-right {
            gap: 10px;
        }
    }

    @media (max-width: 767px) {
        .summary-bar {
            flex-wrap: wrap;
            gap: 8px;
        }

        .divider {
            display: none;
        }

        .timeline {
            padding-left: 44px;
        }

        .tl-dot {
            left: -40px;
            width: 28px;
            height: 28px;
            font-size: 11px;
        }

        .tl-card {
            padding: 14px;
            flex-direction: column;
            align-items: flex-start;
        }

        .tl-right {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>
@stop

@section('content')
    @php
        $total = count($steps);

        $normalizeStatus = function ($statusText) {
            $status = strtolower(trim((string) $statusText));

            if (in_array($status, ['finished', 'fullført'])) {
                return 'finished';
            }

            if (in_array($status, ['started', 'pågår', 'paagar'])) {
                return 'started';
            }

            if (in_array($status, ['not started', 'ikke startet'])) {
                return 'not-started';
            }

            return 'not-planned';
        };

        $finishedCount = collect($steps)->filter(fn ($step) => $normalizeStatus($step['status_text']) === 'finished')->count();
        $startedCount = collect($steps)->filter(fn ($step) => $normalizeStatus($step['status_text']) === 'started')->count();
        $notStartedCount = collect($steps)->filter(fn ($step) => $normalizeStatus($step['status_text']) === 'not-started')->count();

        $progressPercentage = $total > 0 ? (int) round(($finishedCount / $total) * 100) : 0;

        $statusLabelMap = [
            'finished' => 'Fullført',
            'started' => 'Pågår',
            'not-started' => 'Ikke startet',
            'not-planned' => 'Ikke planlagt',
        ];
    @endphp

    <div class="learner-container">
        <div class="container">
            <div class="card card-global progress-plan-card">
                <div class="card-header">
                    Prosjektfremdrift
                </div>
                <div class="card-body">
                    <p class="progress-plan-subtitle">Oversikt over alle steg i utgivelsesprosessen</p>

                    <div class="summary-bar">
                        <div class="summary-stat green">
                            <div class="val">{{ $finishedCount }}</div>
                            <div class="lbl">Fullført</div>
                        </div>
                        <div class="divider"></div>
                        <div class="summary-stat gold">
                            <div class="val">{{ $startedCount }}</div>
                            <div class="lbl">Pågår</div>
                        </div>
                        <div class="divider"></div>
                        <div class="summary-stat gray">
                            <div class="val">{{ $notStartedCount }}</div>
                            <div class="lbl">Ikke startet</div>
                        </div>
                        <div class="divider"></div>
                        <div class="summary-stat brand">
                            <div class="val">{{ $total }}</div>
                            <div class="lbl">Totalt</div>
                        </div>
                    </div>

                    <div class="progress-header">
                        <div class="progress-track">
                            <div class="progress-fill" style="width: {{ $progressPercentage }}%;"></div>
                        </div>
                        <div class="progress-percent">{{ $progressPercentage }}% fullført</div>
                    </div>

                    <div class="timeline">
                        @foreach ($steps as $step)
                            @php
                                $statusClass = $normalizeStatus($step['status_text']);
                                $isFinished = $statusClass === 'finished';
                                $isStarted = $statusClass === 'started';
                                $dotText = $isFinished ? '✓' : ($isStarted ? '↻' : str_pad((string) $step['step_number'], 2, '0', STR_PAD_LEFT));
                                $cardClass = $isFinished ? 'highlight-brand' : ($isStarted ? 'highlight-gold' : '');
                            @endphp

                            <div class="tl-item">
                                <div class="tl-dot {{ $isFinished ? 'finished' : ($isStarted ? 'started' : '') }}">{{ $dotText }}</div>
                                <a href="{{ route('learner.progress-plan.step', $step['step_number']) }}" class="tl-card {{ $cardClass }}">
                                    <div class="tl-left">
                                        <span class="tl-step-num">{{ str_pad((string) $step['step_number'], 2, '0', STR_PAD_LEFT) }}</span>
                                        <span class="tl-name">{{ $step['title'] }}</span>
                                    </div>
                                    <div class="tl-right">
                                        <div class="tl-date">
                                            <span>Forventet dato</span>
                                            {{ $step['expected_date'] ? \Carbon\Carbon::parse($step['expected_date'])->format('d.m.Y') : '—' }}
                                        </div>
                                        <span class="badge {{ $statusClass }}">{{ $statusLabelMap[$statusClass] }}</span>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
