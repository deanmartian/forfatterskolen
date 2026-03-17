@extends('backend.layout')

@section('title')
<title>Juleantologi 2026 &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<style>
    /* ── Header ─────────────────────────────── */
    .anth-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding: 20px 24px;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 12px;
        color: #fff;
    }
    .anth-header__left h2 {
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 4px;
        color: #fff;
    }
    .anth-header__left p {
        font-size: 13px;
        color: #94a3b8;
        margin: 0;
    }
    .anth-header__right { display: flex; gap: 8px; }
    .anth-header__right .btn {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.15);
        color: #e2e8f0;
        font-size: 12px;
        padding: 6px 14px;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .anth-header__right .btn:hover { background: rgba(255,255,255,0.18); color: #fff; }
    .anth-header__right .btn i { margin-right: 4px; }

    /* ── Stats Grid ─────────────────────────── */
    .anth-stats {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .anth-stat {
        background: #fff;
        border-radius: 10px;
        padding: 16px 18px;
        border: 1px solid #e5e7eb;
        position: relative;
        overflow: hidden;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .anth-stat:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .anth-stat::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: 10px 10px 0 0;
    }
    .anth-stat--total::before { background: #6366f1; }
    .anth-stat--elev::before { background: #22c55e; }
    .anth-stat--tidligere::before { background: #eab308; }
    .anth-stat--ny::before { background: #3b82f6; }
    .anth-stat--selected::before { background: #10b981; }
    .anth-stat--feedback::before { background: #8b5cf6; }
    .anth-stat__number { font-size: 30px; font-weight: 800; color: #111827; line-height: 1; }
    .anth-stat--total .anth-stat__number { color: #6366f1; }
    .anth-stat--elev .anth-stat__number { color: #22c55e; }
    .anth-stat--tidligere .anth-stat__number { color: #eab308; }
    .anth-stat--ny .anth-stat__number { color: #3b82f6; }
    .anth-stat--selected .anth-stat__number { color: #10b981; }
    .anth-stat--feedback .anth-stat__number { color: #8b5cf6; }
    .anth-stat__label { font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 6px; }

    /* ── Genre chips ────────────────────────── */
    .anth-genres { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 18px; }
    .anth-genre-chip {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 12px; color: #4b5563;
        background: #f9fafb; border: 1px solid #e5e7eb;
        padding: 5px 12px; border-radius: 20px;
    }
    .anth-genre-chip strong { color: #111827; font-weight: 700; }

    /* ── Filters ─────────────────────────────── */
    .anth-filters {
        background: #fff;
        border-radius: 10px;
        padding: 14px 18px;
        border: 1px solid #e5e7eb;
        margin-bottom: 18px;
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }
    .anth-filters select, .anth-filters input[type="text"] {
        padding: 7px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 13px;
        background: #f9fafb;
        color: #374151;
        transition: border-color 0.2s;
    }
    .anth-filters select:focus, .anth-filters input:focus { border-color: #6366f1; outline: none; }
    .anth-filters input[type="text"] { min-width: 200px; }
    .anth-filters .btn-filter {
        background: #6366f1; color: #fff; border: none;
        padding: 7px 18px; border-radius: 6px; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: background 0.2s;
    }
    .anth-filters .btn-filter:hover { background: #4f46e5; }
    .anth-filters .btn-reset {
        background: #f3f4f6; color: #6b7280; border: 1px solid #d1d5db;
        padding: 7px 14px; border-radius: 6px; font-size: 13px;
        text-decoration: none; transition: all 0.2s;
    }
    .anth-filters .btn-reset:hover { background: #e5e7eb; color: #374151; }

    /* ── Bulk bar ─────────────────────────────── */
    .anth-bulk {
        display: none; background: #eff6ff; border: 1px solid #bfdbfe;
        border-radius: 8px; padding: 10px 16px; margin-bottom: 14px;
        align-items: center; gap: 10px; font-size: 13px; color: #1e40af;
    }
    .anth-bulk.active { display: flex; }
    .anth-bulk select { padding: 5px 10px; font-size: 12px; border-radius: 4px; border: 1px solid #93c5fd; }
    .anth-bulk .btn { padding: 5px 12px; font-size: 12px; }

    /* ── Submission cards ────────────────────── */
    .sub-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 10px;
        transition: box-shadow 0.15s, border-color 0.15s;
    }
    .sub-card:hover { border-color: #c7d2fe; box-shadow: 0 2px 8px rgba(99,102,241,0.06); }
    .sub-card__top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
    .sub-card__title { font-size: 15px; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 8px; }
    .sub-card__title input[type="checkbox"] { width: 15px; height: 15px; accent-color: #6366f1; }
    .sub-card__badges { display: flex; gap: 6px; flex-shrink: 0; }
    .sub-card__meta { font-size: 13px; color: #6b7280; line-height: 1.7; }
    .sub-card__meta strong { color: #374151; }
    .sub-card__meta em { color: #9ca3af; font-style: italic; }
    .sub-card__actions { display: flex; gap: 6px; margin-top: 14px; flex-wrap: wrap; }
    .sub-card__actions .btn { border-radius: 6px; font-size: 12px; font-weight: 500; }

    .sub-card__feedback-panel {
        display: none; margin-top: 16px; padding-top: 16px;
        border-top: 1px solid #f3f4f6;
    }
    .sub-card__feedback-panel textarea {
        border-radius: 8px; border: 1px solid #d1d5db;
        font-size: 13px; padding: 10px 14px;
    }
    .sub-card__feedback-panel textarea:focus { border-color: #6366f1; outline: none; }

    /* ── Badges ───────────────────────────────── */
    .badge-c { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; letter-spacing: 0.3px; }
    .badge-c--elev { background: #dcfce7; color: #166534; }
    .badge-c--tidligere_elev { background: #fef9c3; color: #854d0e; }
    .badge-c--ny { background: #dbeafe; color: #1e40af; }
    .badge-s { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; letter-spacing: 0.3px; }
    .badge-s--received { background: #f3f4f6; color: #374151; }
    .badge-s--under_review { background: #fef3c7; color: #92400e; }
    .badge-s--selected { background: #dcfce7; color: #166534; }
    .badge-s--not_selected { background: #fee2e2; color: #991b1b; }
    .badge-s--feedback_sent { background: #ede9fe; color: #5b21b6; }

    /* ── Empty state ─────────────────────────── */
    .anth-empty {
        text-align: center; padding: 60px 20px;
        background: #fff; border: 2px dashed #e5e7eb; border-radius: 12px;
    }
    .anth-empty__icon { font-size: 48px; margin-bottom: 12px; opacity: 0.3; }
    .anth-empty__text { font-size: 15px; color: #9ca3af; font-weight: 500; }
    .anth-empty__hint { font-size: 13px; color: #d1d5db; margin-top: 6px; }

    /* ── Responsive ──────────────────────────── */
    @media (max-width: 992px) {
        .anth-stats { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 600px) {
        .anth-stats { grid-template-columns: repeat(2, 1fr); }
        .anth-header { flex-direction: column; gap: 12px; }
    }
</style>
@stop

@section('content')

<!-- Header -->
<div class="anth-header">
    <div class="anth-header__left">
        <h2><i class="fa fa-snowflake-o" style="margin-right:8px;opacity:0.6;"></i>Juleantologi 2026</h2>
        <p>Frist: 20. august &middot; {{ $stats['total'] }} innsendt{{ $stats['total'] != 1 ? 'e' : '' }} &middot; {{ (int) max(0, now()->diffInDays(\Carbon\Carbon::parse('2026-08-20'), false)) }} dager igjen</p>
    </div>
    <div class="anth-header__right">
        <a href="{{ route('admin.anthology.export') }}" class="btn"><i class="fa fa-download"></i> Eksporter CSV</a>
        <a href="/juleantologi" target="_blank" class="btn"><i class="fa fa-external-link"></i> Se landingsside</a>
    </div>
</div>

<!-- Stats -->
<div class="anth-stats">
    <div class="anth-stat anth-stat--total">
        <div class="anth-stat__number">{{ $stats['total'] }}</div>
        <div class="anth-stat__label">Totalt</div>
    </div>
    <div class="anth-stat anth-stat--elev">
        <div class="anth-stat__number">{{ $stats['elev'] }}</div>
        <div class="anth-stat__label">Elever</div>
    </div>
    <div class="anth-stat anth-stat--tidligere">
        <div class="anth-stat__number">{{ $stats['tidligere'] }}</div>
        <div class="anth-stat__label">Tidligere</div>
    </div>
    <div class="anth-stat anth-stat--ny">
        <div class="anth-stat__number">{{ $stats['ny'] }}</div>
        <div class="anth-stat__label">Nye leads</div>
    </div>
    <div class="anth-stat anth-stat--selected">
        <div class="anth-stat__number">{{ $stats['selected'] }}</div>
        <div class="anth-stat__label">Valgt ut</div>
    </div>
    <div class="anth-stat anth-stat--feedback">
        <div class="anth-stat__number">{{ $stats['feedback_sent'] }}</div>
        <div class="anth-stat__label">Feedback sendt</div>
    </div>
</div>

<!-- Genre chips -->
@if($genreStats->count())
<div class="anth-genres">
    @php
        $genreLabels = ['novelle'=>'Novelle','krim'=>'Krim','barnefortelling'=>'Barn','dikt'=>'Dikt','feelgood'=>'Feelgood','sakprosa'=>'Sakprosa'];
        $genreIcons = ['novelle'=>'&#128367;&#65039;','krim'=>'&#128270;','barnefortelling'=>'&#11088;','dikt'=>'&#10052;&#65039;','feelgood'=>'&#128293;','sakprosa'=>'&#128221;'];
    @endphp
    @foreach($genreStats as $genre => $count)
        <div class="anth-genre-chip">{!! $genreIcons[$genre] ?? '' !!} {{ $genreLabels[$genre] ?? $genre }}: <strong>{{ $count }}</strong></div>
    @endforeach
</div>
@endif

<!-- Filters -->
<div class="anth-filters">
    <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;width:100%;">
        <select name="connection">
            <option value="">Alle tilknytninger</option>
            <option value="elev" {{ request('connection') == 'elev' ? 'selected' : '' }}>Elev</option>
            <option value="tidligere_elev" {{ request('connection') == 'tidligere_elev' ? 'selected' : '' }}>Tidligere elev</option>
            <option value="ny" {{ request('connection') == 'ny' ? 'selected' : '' }}>Ny skribent</option>
        </select>
        <select name="genre">
            <option value="">Alle sjangre</option>
            <option value="novelle" {{ request('genre') == 'novelle' ? 'selected' : '' }}>Novelle</option>
            <option value="krim" {{ request('genre') == 'krim' ? 'selected' : '' }}>Krim & spenning</option>
            <option value="barnefortelling" {{ request('genre') == 'barnefortelling' ? 'selected' : '' }}>Barnefortelling</option>
            <option value="dikt" {{ request('genre') == 'dikt' ? 'selected' : '' }}>Dikt & lyrikk</option>
            <option value="feelgood" {{ request('genre') == 'feelgood' ? 'selected' : '' }}>Feelgood</option>
            <option value="sakprosa" {{ request('genre') == 'sakprosa' ? 'selected' : '' }}>Sakprosa / essay</option>
        </select>
        <select name="status">
            <option value="">Alle statuser</option>
            <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Mottatt</option>
            <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under vurdering</option>
            <option value="selected" {{ request('status') == 'selected' ? 'selected' : '' }}>Valgt ut</option>
            <option value="not_selected" {{ request('status') == 'not_selected' ? 'selected' : '' }}>Ikke valgt</option>
            <option value="feedback_sent" {{ request('status') == 'feedback_sent' ? 'selected' : '' }}>Tilbakemelding sendt</option>
        </select>
        <input type="text" name="search" placeholder="Søk navn, e-post, tittel..." value="{{ request('search') }}">
        <button type="submit" class="btn-filter"><i class="fa fa-search"></i> Filtrer</button>
        @if(request()->hasAny(['connection','genre','status','search']))
            <a href="{{ route('admin.anthology.index') }}" class="btn-reset"><i class="fa fa-times"></i> Nullstill</a>
        @endif
    </form>
</div>

<!-- Bulk bar -->
<form id="bulkForm" method="POST" action="{{ route('admin.anthology.bulk-status') }}">
    @csrf
    <div class="anth-bulk" id="bulkBar">
        <strong><span id="bulkCount">0</span> valgt</strong>
        <select name="status">
            <option value="under_review">Under vurdering</option>
            <option value="selected">Velg ut</option>
            <option value="not_selected">Ikke valgt</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Oppdater status</button>
    </div>

    <!-- Submissions -->
    @forelse($submissions as $submission)
        <div class="sub-card">
            <div class="sub-card__top">
                <div class="sub-card__title">
                    <input type="checkbox" class="bulk-check" name="ids[]" value="{{ $submission->id }}">
                    &laquo;{{ $submission->title }}&raquo; <span style="font-weight:400;color:#6b7280;font-size:13px;">— {{ $submission->genre_label }}</span>
                </div>
                <div class="sub-card__badges">
                    <span class="badge-c badge-c--{{ $submission->connection }}">{{ $submission->connection_label }}</span>
                    <span class="badge-s badge-s--{{ $submission->status }}">{{ $submission->status_label }}</span>
                </div>
            </div>
            <div class="sub-card__meta">
                <strong>{{ $submission->first_name }} {{ $submission->last_name }}</strong> &middot; {{ $submission->email }}
                @if($submission->course_name) &middot; <em>{{ $submission->course_name }}</em> @endif
                <br>
                <i class="fa fa-file-o" style="color:#d1d5db;"></i> {{ $submission->file_name }}
                @if($submission->word_count) &middot; {{ number_format($submission->word_count, 0, ',', ' ') }} ord @endif
                &middot; <i class="fa fa-clock-o" style="color:#d1d5db;"></i> {{ $submission->created_at->format('d.m.Y H:i') }}
                @if($submission->consent_marketing) &middot; <span style="color:#22c55e;"><i class="fa fa-check-circle"></i> Marketing</span> @endif
                @if($submission->description)
                    <br><em>{{ $submission->description }}</em>
                @endif
                @if($submission->editor_feedback)
                    <br><i class="fa fa-comment-o" style="color:#8b5cf6;"></i> <em>{{ \Illuminate\Support\Str::limit($submission->editor_feedback, 120) }}</em>
                @endif
            </div>
            <div class="sub-card__actions">
                <a href="{{ route('admin.anthology.download', $submission->id) }}" class="btn btn-info btn-xs"><i class="fa fa-download"></i> Last ned</a>

                @if($submission->status !== 'selected')
                <form method="POST" action="{{ route('admin.anthology.update-status', $submission->id) }}" style="display:inline;">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="selected">
                    <button type="submit" class="btn btn-success btn-xs"><i class="fa fa-check"></i> Velg ut</button>
                </form>
                @endif
                @if($submission->status !== 'not_selected')
                <form method="POST" action="{{ route('admin.anthology.update-status', $submission->id) }}" style="display:inline;">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="not_selected">
                    <button type="submit" class="btn btn-danger btn-xs"><i class="fa fa-times"></i> Avslå</button>
                </form>
                @endif
                @if($submission->status === 'received')
                <form method="POST" action="{{ route('admin.anthology.update-status', $submission->id) }}" style="display:inline;">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="under_review">
                    <button type="submit" class="btn btn-warning btn-xs"><i class="fa fa-eye"></i> Vurdér</button>
                </form>
                @endif

                <button type="button" class="btn btn-default btn-xs" onclick="toggleFeedback({{ $submission->id }})"><i class="fa fa-comment"></i> Tilbakemelding</button>
            </div>

            <!-- Feedback panel -->
            <div id="feedback-{{ $submission->id }}" class="sub-card__feedback-panel">
                <form method="POST" action="{{ route('admin.anthology.send-feedback', $submission->id) }}">
                    @csrf
                    <div class="form-group" style="margin-bottom:10px;">
                        <label style="font-size:13px;font-weight:600;color:#374151;">Tilbakemelding til {{ $submission->first_name }}:</label>
                        <textarea name="editor_feedback" rows="4" class="form-control" placeholder="Skriv tilbakemelding her..." required>{{ $submission->editor_feedback }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-send"></i> Send tilbakemelding via e-post
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="anth-empty">
            <div class="anth-empty__icon"><i class="fa fa-snowflake-o"></i></div>
            <div class="anth-empty__text">Ingen innsendinger ennå</div>
            <div class="anth-empty__hint">Del landingssiden for å motta bidrag: <a href="/juleantologi" target="_blank" style="color:#6366f1;">/juleantologi</a></div>
        </div>
    @endforelse
</form>

{{ $submissions->appends(request()->query())->links() }}

@stop

@section('scripts')
<script>
function toggleFeedback(id) {
    var el = document.getElementById('feedback-' + id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
    if (el.style.display === 'block') {
        el.querySelector('textarea').focus();
    }
}

document.querySelectorAll('.bulk-check').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var checked = document.querySelectorAll('.bulk-check:checked').length;
        document.getElementById('bulkCount').textContent = checked;
        document.getElementById('bulkBar').className = 'anth-bulk' + (checked > 0 ? ' active' : '');
    });
});
</script>
@stop
