@extends('backend.layout')

@section('title')
<title>Juleantologi 2026 &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<style>
    .anthology-stats { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 20px; }
    .anthology-stat { background: #fff; border-radius: 8px; padding: 15px 20px; border: 1px solid #e5e7eb; min-width: 120px; }
    .anthology-stat__number { font-size: 28px; font-weight: 700; color: #111827; line-height: 1; }
    .anthology-stat__label { font-size: 12px; color: #6b7280; margin-top: 4px; }
    .anthology-stat--elev .anthology-stat__number { color: #22c55e; }
    .anthology-stat--tidligere .anthology-stat__number { color: #eab308; }
    .anthology-stat--ny .anthology-stat__number { color: #3b82f6; }

    .anthology-filters { background: #fff; border-radius: 8px; padding: 15px 20px; border: 1px solid #e5e7eb; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .anthology-filters select, .anthology-filters input { padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px; }
    .anthology-filters .btn { padding: 6px 14px; font-size: 13px; }

    .submission-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px; margin-bottom: 12px; }
    .submission-card__header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
    .submission-card__title { font-size: 16px; font-weight: 600; color: #111827; }
    .submission-card__genre { font-size: 12px; color: #6b7280; }
    .submission-card__meta { font-size: 13px; color: #6b7280; line-height: 1.6; }
    .submission-card__meta strong { color: #374151; }
    .submission-card__actions { display: flex; gap: 6px; margin-top: 12px; flex-wrap: wrap; }

    .badge-connection { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .badge-connection--elev { background: #dcfce7; color: #166534; }
    .badge-connection--tidligere_elev { background: #fef9c3; color: #854d0e; }
    .badge-connection--ny { background: #dbeafe; color: #1e40af; }

    .badge-status { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .badge-status--received { background: #f3f4f6; color: #374151; }
    .badge-status--under_review { background: #fef3c7; color: #92400e; }
    .badge-status--selected { background: #dcfce7; color: #166534; }
    .badge-status--not_selected { background: #fee2e2; color: #991b1b; }
    .badge-status--feedback_sent { background: #dbeafe; color: #1e40af; }

    .genre-stats { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }
    .genre-stat { font-size: 12px; color: #6b7280; background: #f9fafb; padding: 4px 10px; border-radius: 4px; }
    .genre-stat strong { color: #374151; }

    .bulk-bar { display: none; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 15px; margin-bottom: 15px; align-items: center; gap: 10px; }
    .bulk-bar.active { display: flex; }
    .bulk-bar select, .bulk-bar .btn { padding: 5px 10px; font-size: 12px; }
</style>
@stop

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-book"></i> Juleantologi 2026</h3>
    <div style="float:right;">
        <a href="{{ route('admin.anthology.export') }}" class="btn btn-default btn-sm"><i class="fa fa-download"></i> Eksporter CSV</a>
    </div>
</div>

<!-- Statistikk -->
<div class="anthology-stats">
    <div class="anthology-stat">
        <div class="anthology-stat__number">{{ $stats['total'] }}</div>
        <div class="anthology-stat__label">Totalt innsendte</div>
    </div>
    <div class="anthology-stat anthology-stat--elev">
        <div class="anthology-stat__number">{{ $stats['elev'] }}</div>
        <div class="anthology-stat__label">Elever</div>
    </div>
    <div class="anthology-stat anthology-stat--tidligere">
        <div class="anthology-stat__number">{{ $stats['tidligere'] }}</div>
        <div class="anthology-stat__label">Tidligere elever</div>
    </div>
    <div class="anthology-stat anthology-stat--ny">
        <div class="anthology-stat__number">{{ $stats['ny'] }}</div>
        <div class="anthology-stat__label">Nye skribenter</div>
    </div>
    <div class="anthology-stat">
        <div class="anthology-stat__number">{{ $stats['selected'] }}</div>
        <div class="anthology-stat__label">Valgt ut</div>
    </div>
    <div class="anthology-stat">
        <div class="anthology-stat__number">{{ $stats['feedback_sent'] }}</div>
        <div class="anthology-stat__label">Tilbakemelding sendt</div>
    </div>
</div>

<!-- Sjanger-statistikk -->
@if($genreStats->count())
<div class="genre-stats">
    @php
        $genreLabels = ['novelle'=>'Novelle','krim'=>'Krim','barnefortelling'=>'Barnefortelling','dikt'=>'Dikt','feelgood'=>'Feelgood','sakprosa'=>'Sakprosa'];
    @endphp
    @foreach($genreStats as $genre => $count)
        <div class="genre-stat"><strong>{{ $genreLabels[$genre] ?? $genre }}:</strong> {{ $count }}</div>
    @endforeach
</div>
@endif

<!-- Filtre -->
<div class="anthology-filters">
    <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
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
        <input type="text" name="search" placeholder="Søk navn/e-post/tittel..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
        @if(request()->hasAny(['connection','genre','status','search']))
            <a href="{{ route('admin.anthology.index') }}" class="btn btn-default btn-sm">Nullstill</a>
        @endif
    </form>
</div>

<!-- Bulk-handlinger -->
<form id="bulkForm" method="POST" action="{{ route('admin.anthology.bulk-status') }}">
    @csrf
    <div class="bulk-bar" id="bulkBar">
        <span id="bulkCount">0</span> valgt &nbsp;
        <select name="status">
            <option value="under_review">Under vurdering</option>
            <option value="selected">Velg ut</option>
            <option value="not_selected">Ikke valgt</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Oppdater status</button>
    </div>

    <!-- Innsendinger -->
    @forelse($submissions as $submission)
        <div class="submission-card">
            <div class="submission-card__header">
                <div>
                    <div class="submission-card__title">
                        <input type="checkbox" class="bulk-check" name="ids[]" value="{{ $submission->id }}" style="margin-right:6px;">
                        &laquo;{{ $submission->title }}&raquo; — {{ $submission->genre_label }}
                    </div>
                </div>
                <div>
                    <span class="badge-connection badge-connection--{{ $submission->connection }}">{{ $submission->connection_label }}</span>
                    <span class="badge-status badge-status--{{ $submission->status }}">{{ $submission->status_label }}</span>
                </div>
            </div>
            <div class="submission-card__meta">
                <strong>{{ $submission->first_name }} {{ $submission->last_name }}</strong> &middot; {{ $submission->email }}
                @if($submission->course_name) &middot; {{ $submission->course_name }} @endif
                <br>
                <strong>Fil:</strong> {{ $submission->file_name }}
                @if($submission->word_count) &middot; {{ number_format($submission->word_count, 0, ',', ' ') }} ord @endif
                &middot; Sendt inn {{ $submission->created_at->format('d.m.Y H:i') }}
                @if($submission->consent_marketing) &middot; <span style="color:#22c55e;">&#9993; Marketing: Ja</span> @endif
                @if($submission->description)
                    <br><em style="color:#9ca3af;">{{ $submission->description }}</em>
                @endif
                @if($submission->editor_feedback)
                    <br><strong>Tilbakemelding:</strong> <em>{{ \Illuminate\Support\Str::limit($submission->editor_feedback, 100) }}</em>
                @endif
            </div>
            <div class="submission-card__actions">
                <a href="{{ route('admin.anthology.download', $submission->id) }}" class="btn btn-info btn-xs"><i class="fa fa-download"></i> Last ned tekst</a>

                <!-- Status-knapper -->
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
                    <button type="submit" class="btn btn-warning btn-xs"><i class="fa fa-eye"></i> Under vurdering</button>
                </form>
                @endif

                <!-- Tilbakemelding-knapp -->
                <button class="btn btn-default btn-xs" onclick="toggleFeedback({{ $submission->id }})"><i class="fa fa-comment"></i> Gi tilbakemelding</button>
            </div>

            <!-- Tilbakemelding-skjema (skjult) -->
            <div id="feedback-{{ $submission->id }}" style="display:none; margin-top:12px; padding-top:12px; border-top:1px solid #e5e7eb;">
                <form method="POST" action="{{ route('admin.anthology.send-feedback', $submission->id) }}">
                    @csrf
                    <div class="form-group">
                        <label>Tilbakemelding til {{ $submission->first_name }}:</label>
                        <textarea name="editor_feedback" rows="4" class="form-control" placeholder="Skriv tilbakemelding her..." required>{{ $submission->editor_feedback }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="margin-top:8px;">
                        <i class="fa fa-send"></i> Send tilbakemelding via e-post
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="submission-card" style="text-align:center;color:#9ca3af;padding:40px;">
            <i class="fa fa-inbox" style="font-size:32px;margin-bottom:10px;"></i><br>
            Ingen innsendinger ennå.
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
}

// Bulk checkbox
document.querySelectorAll('.bulk-check').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var checked = document.querySelectorAll('.bulk-check:checked').length;
        document.getElementById('bulkCount').textContent = checked;
        document.getElementById('bulkBar').className = 'bulk-bar' + (checked > 0 ? ' active' : '');
    });
});
</script>
@stop
