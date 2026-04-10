@extends('editor.layout')

@section('page_title', 'Mine elever &rsaquo; Forfatterskolen Redaktørportal')

@section('page-title', 'Mine elever')

@section('styles')
<style>
    .ms-wrapper { max-width: 100%; padding: 0 20px; }

    .ms-header {
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
    .ms-header h2 { font-size: 1.5rem; font-weight: 700; margin: 0 0 4px; }
    .ms-header p { font-size: 0.95rem; opacity: 0.85; margin: 0; }
    .ms-stats { display: flex; gap: 12px; }
    .ms-stat {
        background: rgba(255,255,255,0.15);
        border-radius: 8px;
        padding: 12px 20px;
        text-align: center;
    }
    .ms-stat__num { font-size: 1.75rem; font-weight: 700; line-height: 1; }
    .ms-stat__label { font-size: 0.7rem; opacity: 0.75; text-transform: uppercase; letter-spacing: 0.5px; }

    .ms-section {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 12px;
        margin-bottom: 20px;
        overflow: hidden;
    }
    .ms-section__header {
        padding: 16px 22px;
        border-bottom: 1px solid rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ms-section__header--warning { background: #fff3e0; border-bottom-color: #ffe0b2; }
    .ms-section__header--info { background: #e3f2fd; border-bottom-color: #bbdefb; }
    .ms-section__header--default { background: #faf8f5; }
    .ms-section__title { font-size: 1.1rem; font-weight: 700; margin: 0; }
    .ms-section__title--warning { color: #e65100; }
    .ms-section__title--info { color: #1565c0; }
    .ms-section__count {
        background: #862736; color: #fff;
        font-size: 0.7rem; font-weight: 700;
        padding: 2px 8px; border-radius: 10px;
    }

    .ms-table { width: 100%; border-collapse: collapse; }
    .ms-table th {
        font-size: 0.8rem; font-weight: 600; text-transform: uppercase;
        letter-spacing: 0.3px; color: #8a8580;
        padding: 12px 18px; border-bottom: 1px solid rgba(0,0,0,0.06);
        text-align: left;
    }
    .ms-table td {
        padding: 14px 18px; font-size: 0.95rem; color: #1a1a1a;
        border-bottom: 1px solid rgba(0,0,0,0.04); vertical-align: middle;
    }
    .ms-table tbody tr:hover { background: #faf8f5; }
    .ms-table a { color: #862736; text-decoration: none; font-weight: 500; }
    .ms-table a:hover { text-decoration: underline; }

    .ms-btn { display: inline-flex; align-items: center; gap: 4px; padding: 6px 14px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; }
    .ms-btn--approve { background: #2e7d32; color: #fff; }
    .ms-btn--approve:hover { background: #1b5e20; color: #fff; }
    .ms-btn--reject { background: #c62828; color: #fff; }
    .ms-btn--reject:hover { background: #b71c1c; color: #fff; }
    .ms-btn--remind { background: #1565c0; color: #fff; }
    .ms-btn--remind:hover { background: #0d47a1; color: #fff; }

    .ms-reason {
        font-size: 0.85rem; color: #5a5550; font-style: italic;
        max-width: 300px; overflow: hidden; text-overflow: ellipsis;
        white-space: nowrap; cursor: help;
    }

    .ms-empty { text-align: center; padding: 32px; color: #8a8580; font-size: 0.95rem; }

    @media (max-width: 768px) {
        .ms-stats { flex-wrap: wrap; }
        .ms-header { flex-direction: column; text-align: center; }
    }
</style>
@stop

@section('content')
<div class="ms-wrapper">

    @if(session('success'))
        <div class="alert alert-success" style="border-radius:10px;margin-bottom:16px;">{{ session('success') }}</div>
    @endif

    <div class="ms-header">
        <div>
            <h2><i class="fa fa-users"></i> Mine elever</h2>
            <p>Utsettelser, påminnelser og oversikt over dine tildelte elever.</p>
        </div>
        <div class="ms-stats">
            <div class="ms-stat">
                <div class="ms-stat__num">{{ $extensionRequests->count() }}</div>
                <div class="ms-stat__label">Utsettelser</div>
            </div>
            <div class="ms-stat">
                <div class="ms-stat__num">{{ $activeManuscripts->count() }}</div>
                <div class="ms-stat__label">Aktive manus</div>
            </div>
        </div>
    </div>

    {{-- ═══════ UTSETTELSESFORESPØRSLER ═══════ --}}
    <div class="ms-section">
        <div class="ms-section__header ms-section__header--warning">
            <i class="fa fa-clock-o" style="color:#e65100;font-size:1.2rem;"></i>
            <h3 class="ms-section__title ms-section__title--warning">Utsettelsesforespørsler</h3>
            @if($extensionRequests->count())
                <span class="ms-section__count" style="background:#e65100;">{{ $extensionRequests->count() }}</span>
            @endif
        </div>
        @if($extensionRequests->isEmpty())
            <div class="ms-empty">Ingen ventende utsettelsesforespørsler.</div>
        @else
            <table class="ms-table">
                <thead>
                    <tr>
                        <th>Elev</th>
                        <th>Oppgave</th>
                        <th>Opprinnelig frist</th>
                        <th>Ønsket frist</th>
                        <th>Begrunnelse</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($extensionRequests as $ext)
                        <tr>
                            <td><strong>{{ $ext->user->full_name }}</strong></td>
                            <td>{{ $ext->assignment->title }}</td>
                            <td>{{ $ext->original_deadline ? \Carbon\Carbon::parse($ext->original_deadline)->format('d.m.Y') : '—' }}</td>
                            <td><strong>{{ $ext->requested_deadline->format('d.m.Y') }}</strong></td>
                            <td><span class="ms-reason" title="{{ $ext->reason }}">{{ $ext->reason }}</span></td>
                            <td style="white-space:nowrap;">
                                <form action="{{ route('editor.extension.decide', [$ext->id, 'approve']) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="ms-btn ms-btn--approve" onclick="return confirm('Godkjenne utsettelse?')">
                                        <i class="fa fa-check"></i> Godkjenn
                                    </button>
                                </form>
                                <form action="{{ route('editor.extension.decide', [$ext->id, 'reject']) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="ms-btn ms-btn--reject" onclick="return confirm('Avslå utsettelse?')">
                                        <i class="fa fa-times"></i> Avslå
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ═══════ GODKJENTE UTSETTELSER ═══════ --}}
    @if(isset($approvedExtensions) && $approvedExtensions->isNotEmpty())
        <div class="ms-section">
            <div class="ms-section__header" style="background:#f0fdf4;border-left:3px solid #22c55e;">
                <i class="fa fa-check-circle" style="color:#16a34a;font-size:1.2rem;"></i>
                <h3 class="ms-section__title" style="color:#166534;">Godkjente utsettelser</h3>
                <span class="ms-section__count" style="background:#16a34a;">{{ $approvedExtensions->count() }}</span>
            </div>
            <table class="ms-table">
                <thead>
                    <tr>
                        <th>Elev</th>
                        <th>Oppgave</th>
                        <th>Opprinnelig frist</th>
                        <th>Ny frist</th>
                        <th>Godkjent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approvedExtensions as $ext)
                        @php
                            $newDeadline = \Carbon\Carbon::parse($ext->requested_deadline);
                            $daysLeft = (int) round(now()->diffInDays($newDeadline, false));
                        @endphp
                        <tr>
                            <td><strong>{{ $ext->user->full_name ?? 'Ukjent' }}</strong><br><small style="color:#8a8580;">#{{ $ext->user->id ?? '' }}</small></td>
                            <td>{{ $ext->assignment->title ?? '—' }}</td>
                            <td>{{ $ext->original_deadline ? \Carbon\Carbon::parse($ext->original_deadline)->format('d.m.Y') : '—' }}</td>
                            <td>
                                <strong style="color:#16a34a;">{{ $newDeadline->format('d.m.Y') }}</strong>
                                @if($daysLeft >= 0)
                                    <br><small style="color:#16a34a;">{{ $daysLeft === 0 ? 'i dag' : ($daysLeft === 1 ? 'i morgen' : "om {$daysLeft} dager") }}</small>
                                @else
                                    <br><small style="color:#c62828;">{{ abs($daysLeft) }} dager forsinket</small>
                                @endif
                            </td>
                            <td>
                                <small style="color:#666;">{{ $ext->decided_at ? \Carbon\Carbon::parse($ext->decided_at)->format('d.m.Y') : '—' }}</small>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ═══════ AKTIVE MANUS ═══════ --}}
    <div class="ms-section">
        <div class="ms-section__header ms-section__header--default">
            <i class="fa fa-pencil" style="color:#862736;font-size:1.1rem;"></i>
            <h3 class="ms-section__title">Aktive manus tildelt deg</h3>
            <span class="ms-section__count">{{ $activeManuscripts->count() }}</span>
        </div>
        @if($activeManuscripts->isEmpty())
            <div class="ms-empty">Ingen aktive manus akkurat nå.</div>
        @else
            <table class="ms-table">
                <thead>
                    <tr>
                        <th>Elev</th>
                        <th>Kurs</th>
                        <th>Oppgave</th>
                        <th>Ord</th>
                        <th>Levert</th>
                        <th>Frist</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeManuscripts as $m)
                        @php
                            $deadline = $m->editor_expected_finish ?: ($m->assignment->editor_expected_finish ?? null);
                            $isOverdue = $deadline && \Carbon\Carbon::parse($deadline)->isPast();
                        @endphp
                        <tr @if($isOverdue) style="background:#fef2f2;" @endif>
                            <td><strong>{{ $m->user->full_name ?? 'Ukjent' }}</strong><br><small style="color:#8a8580;">#{{ $m->user->id ?? '' }}</small></td>
                            <td>{{ $m->assignment->course->title ?? '—' }}</td>
                            <td>{{ $m->assignment->title ?? '—' }}</td>
                            <td>{{ $m->words ? number_format($m->words) : '—' }}</td>
                            <td>{{ $m->uploaded_at ? \Carbon\Carbon::parse($m->uploaded_at)->format('d.m.Y') : '—' }}</td>
                            <td>
                                @if($deadline)
                                    <span style="{{ $isOverdue ? 'color:#c62828;font-weight:600;' : '' }}">
                                        {{ \Carbon\Carbon::parse($deadline)->format('d.m.Y') }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if(!$m->has_feedback)
                                    <form action="{{ route('editor.student.remind', $m->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="ms-btn ms-btn--remind" onclick="return confirm('Sende påminnelse til {{ $m->user->first_name ?? 'eleven' }}?')">
                                            <i class="fa fa-envelope"></i> Påminn
                                        </button>
                                    </form>
                                @else
                                    <span style="color:#2e7d32;font-size:0.85rem;"><i class="fa fa-check"></i> Tilbakemelding gitt</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ═══════ ELEVER SOM IKKE HAR LEVERT (etter fristen) ═══════ --}}
    @if(isset($overdueStudents) && $overdueStudents->isNotEmpty())
        <div class="ms-section">
            <div class="ms-section__header" style="background:#fef2f2;border-left:3px solid #dc2626;">
                <i class="fa fa-exclamation-triangle" style="color:#dc2626;font-size:1.15rem;"></i>
                <h3 class="ms-section__title" style="color:#991b1b;">Ikke levert — etter frist</h3>
                <span class="ms-section__count" style="background:#dc2626;">{{ $overdueStudents->count() }}</span>
            </div>
            <div style="padding:12px 16px;background:#fef9f9;font-size:13px;color:#7c2d12;">
                <i class="fa fa-info-circle"></i>
                Disse elevene har ikke levert oppgaven, og fristen er passert. Du kan sende en påminnelse om du vil — det er helt valgfritt.
            </div>
            <table class="ms-table">
                <thead>
                    <tr>
                        <th>Elev</th>
                        <th>Kurs</th>
                        <th>Oppgave</th>
                        <th>Frist (passert)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overdueStudents as $item)
                        @php
                            $daysOverdue = $item->deadline ? (int) round(\Carbon\Carbon::parse($item->deadline)->diffInDays(now(), false)) : null;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $item->user->full_name ?? 'Ukjent' }}</strong><br>
                                <small style="color:#8a8580;">#{{ $item->user->id ?? '' }}</small>
                            </td>
                            <td>{{ $item->course->title ?? '—' }}</td>
                            <td>{{ $item->assignment->title ?? '—' }}</td>
                            <td>
                                <span style="color:#c62828;">{{ $item->deadline ? \Carbon\Carbon::parse($item->deadline)->format('d.m.Y') : '—' }}</span>
                                @if($daysOverdue !== null && $daysOverdue > 0)
                                    <br><small style="color:#c62828;">{{ $daysOverdue }} {{ $daysOverdue === 1 ? 'dag' : 'dager' }} forsinket</small>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('editor.student.remind-overdue', ['userId' => $item->user->id, 'assignmentId' => $item->assignment->id]) }}"
                                      method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="ms-btn ms-btn--remind"
                                            onclick="return confirm('Sende påminnelse til {{ addslashes($item->user->first_name ?? 'eleven') }}?')">
                                        <i class="fa fa-envelope"></i> Påminn
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
@stop
