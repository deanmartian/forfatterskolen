@extends('editor.layout')

@section('page_title'){{ trans('site.upcoming-assignment') }} &rsaquo; Forfatterskolen Redaktørportal@endsection

@section('page-title', trans('site.upcoming-assignment'))

@section('styles')
<style>
    .ua-wrapper { max-width: 960px; margin: 0 auto; padding: 0 16px; }

    .ua-header {
        background: linear-gradient(135deg, #862736 0%, #5e1a26 100%);
        border-radius: 12px;
        padding: 28px 32px;
        color: #fff;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .ua-header__info h2 { font-size: 1.35rem; font-weight: 700; margin: 0 0 4px; }
    .ua-header__info p { font-size: 0.85rem; opacity: 0.8; margin: 0; }
    .ua-header__stat {
        background: rgba(255,255,255,0.15);
        border-radius: 8px;
        padding: 12px 20px;
        text-align: center;
    }
    .ua-header__stat-num { font-size: 1.75rem; font-weight: 700; line-height: 1; }
    .ua-header__stat-label { font-size: 0.7rem; opacity: 0.75; text-transform: uppercase; letter-spacing: 0.5px; }

    .ua-empty {
        text-align: center;
        padding: 48px 20px;
        color: #8a8580;
    }
    .ua-empty i { font-size: 2.5rem; opacity: 0.3; display: block; margin-bottom: 12px; }
    .ua-empty p { font-size: 0.95rem; }

    .ua-list { display: flex; flex-direction: column; gap: 10px; }

    .ua-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 10px;
        padding: 18px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .ua-card:hover { border-color: rgba(134,39,54,0.2); box-shadow: 0 2px 12px rgba(0,0,0,0.04); }

    .ua-card__date {
        min-width: 52px;
        text-align: center;
        background: #faf8f5;
        border-radius: 8px;
        padding: 8px 6px;
        flex-shrink: 0;
    }
    .ua-card__date-day { font-size: 1.2rem; font-weight: 700; color: #862736; line-height: 1; }
    .ua-card__date-month { font-size: 0.6rem; font-weight: 600; text-transform: uppercase; color: #862736; margin-top: 2px; }

    .ua-card__info { flex: 1; min-width: 0; }
    .ua-card__title { font-size: 0.95rem; font-weight: 700; color: #1a1a1a; margin-bottom: 2px; }
    .ua-card__course { font-size: 0.8rem; color: #8a8580; }

    .ua-card__meta {
        display: flex;
        gap: 16px;
        flex-shrink: 0;
        align-items: center;
    }
    .ua-card__meta-item {
        text-align: center;
        min-width: 50px;
    }
    .ua-card__meta-value { font-size: 0.85rem; font-weight: 600; color: #1a1a1a; }
    .ua-card__meta-label { font-size: 0.65rem; color: #8a8580; text-transform: uppercase; }

    .ua-card__badge {
        font-size: 0.65rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 4px;
        white-space: nowrap;
    }
    .ua-card__badge--soon { background: #fff3e0; color: #e65100; }
    .ua-card__badge--future { background: #e3f2fd; color: #1565c0; }
    .ua-card__badge--overdue { background: #fce8ea; color: #862736; }

    @media (max-width: 600px) {
        .ua-card { flex-direction: column; align-items: flex-start; gap: 10px; }
        .ua-card__meta { width: 100%; justify-content: space-between; }
        .ua-header { flex-direction: column; text-align: center; }
    }
</style>
@stop

@section('content')
<div class="ua-wrapper">

    @php
        $norwegianMonths = ['jan','feb','mar','apr','mai','jun','jul','aug','sep','okt','nov','des'];
    @endphp

    <div class="ua-header">
        <div class="ua-header__info">
            <h2>Nye oppgaver</h2>
            <p>Oppgaver som venter på innlevering fra elever.</p>
        </div>
        <div class="ua-header__stat">
            <div class="ua-header__stat-num">{{ $upcomingAssignments->count() }}</div>
            <div class="ua-header__stat-label">Oppgaver</div>
        </div>
    </div>

    @if($upcomingAssignments->isEmpty())
        <div class="ua-empty">
            <i class="fa fa-check-circle"></i>
            <p>Ingen nye oppgaver akkurat nå. Alt er under kontroll!</p>
        </div>
    @else
        <div class="ua-list">
            @foreach($upcomingAssignments as $assignment)
                @php
                    $subDate = $assignment->submission_date ? \Carbon\Carbon::parse($assignment->submission_date) : null;
                    $daysUntil = $subDate ? (int) now()->diffInDays($subDate, false) : null;

                    if ($daysUntil !== null && $daysUntil < 0) {
                        $badgeClass = 'overdue';
                        $badgeText = abs($daysUntil) . ' dager siden';
                    } elseif ($daysUntil !== null && $daysUntil <= 7) {
                        $badgeClass = 'soon';
                        $badgeText = $daysUntil == 0 ? 'I dag' : ($daysUntil == 1 ? 'I morgen' : 'Om ' . $daysUntil . ' dager');
                    } else {
                        $badgeClass = 'future';
                        $badgeText = $subDate ? $subDate->format('d.m.Y') : '—';
                    }
                @endphp
                <div class="ua-card">
                    @if($subDate)
                        <div class="ua-card__date">
                            <div class="ua-card__date-day">{{ $subDate->format('d') }}</div>
                            <div class="ua-card__date-month">{{ $norwegianMonths[$subDate->month - 1] }}</div>
                        </div>
                    @else
                        <div class="ua-card__date">
                            <div class="ua-card__date-day">—</div>
                        </div>
                    @endif

                    <div class="ua-card__info">
                        <div class="ua-card__title">{{ $assignment->title }}</div>
                        <div class="ua-card__course">{{ $assignment->course->title ?? 'Ingen kurs' }}</div>
                    </div>

                    <div class="ua-card__meta">
                        @if($assignment->max_words)
                        <div class="ua-card__meta-item">
                            <div class="ua-card__meta-value">{{ number_format($assignment->max_words) }}</div>
                            <div class="ua-card__meta-label">Maks ord</div>
                        </div>
                        @endif
                        <span class="ua-card__badge ua-card__badge--{{ $badgeClass }}">{{ $badgeText }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@stop
