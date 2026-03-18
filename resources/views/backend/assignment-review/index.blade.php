@extends('backend.layout')

@section('title')
<title>AI-tilbakemeldinger &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

<div class="page-toolbar">
    <h3><i class="fa fa-robot"></i> AI-tilbakemeldinger</h3>
    <div class="clearfix"></div>
</div>

<div class="margin-top">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Venter på godkjenning ({{ $submissions->total() }})</h3>
        </div>
        <div class="panel-body" style="padding:0;">
            @if($submissions->count() > 0)
                @foreach($submissions as $sub)
                    <div class="review-item" id="review-{{ $sub->id }}" style="padding:1.25rem;border-bottom:1px solid #e8e4de;">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.75rem;">
                            <div>
                                <strong>{{ $sub->user->full_name ?? $sub->user->email }}</strong>
                                <span style="color:#8a8580;font-size:0.85rem;">· {{ $sub->created_at }}</span>
                                <br>
                                <span style="font-size:0.85rem;color:#5a5550;">
                                    {{ $sub->assignment->lesson->course->title ?? '—' }} → {{ $sub->assignment->lesson->title ?? '—' }}
                                </span>
                            </div>
                            <span class="label {{ $sub->status === 'ai_generated' ? 'label-info' : 'label-warning' }}">
                                {{ $sub->status === 'ai_generated' ? 'AI generert' : 'Venter på AI' }}
                            </span>
                        </div>

                        <div style="background:#f5f3f0;padding:0.75rem 1rem;border-radius:6px;margin-bottom:0.5rem;">
                            <div style="font-size:0.75rem;font-weight:600;color:#862736;text-transform:uppercase;margin-bottom:0.25rem;">Oppgave</div>
                            {{ $sub->assignment->question_text }}
                        </div>

                        <div style="background:#fff;padding:0.75rem 1rem;border-radius:6px;border:1px solid #e8e4de;margin-bottom:0.75rem;">
                            <div style="font-size:0.75rem;font-weight:600;color:#1a1a1a;text-transform:uppercase;margin-bottom:0.25rem;">Elevens svar</div>
                            {{ $sub->answer_text }}
                        </div>

                        @if($sub->ai_feedback)
                            <div style="background:#e3f2fd;padding:0.75rem 1rem;border-radius:6px;margin-bottom:0.75rem;">
                                <div style="font-size:0.75rem;font-weight:600;color:#1565c0;text-transform:uppercase;margin-bottom:0.25rem;">AI-forslag</div>
                                {{ $sub->ai_feedback }}
                            </div>
                        @endif

                        <div style="display:flex;gap:0.5rem;align-items:flex-start;">
                            <textarea class="form-control" id="feedback-{{ $sub->id }}" rows="3" placeholder="Rediger tilbakemeldingen eller skriv din egen..." style="flex:1;">{{ $sub->ai_feedback }}</textarea>
                            <button class="btn btn-success" onclick="approveSubmission({{ $sub->id }})" style="white-space:nowrap;">
                                <i class="fa fa-check"></i> Godkjenn
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="padding:2rem;text-align:center;color:#8a8580;">
                    Ingen innsendte oppgaver venter på godkjenning
                </div>
            @endif
        </div>
    </div>

    <div class="pull-right">{!! $submissions->render() !!}</div>
    <div class="clearfix"></div>

    @if($approvedSubmissions->count() > 0)
        <div class="panel panel-default" style="margin-top:2rem;">
            <div class="panel-heading">
                <h3 class="panel-title">Nylig godkjent</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Elev</th>
                            <th>Kurs / Leksjon</th>
                            <th>Godkjent av</th>
                            <th>Godkjent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvedSubmissions as $sub)
                            <tr>
                                <td>{{ $sub->user->full_name ?? $sub->user->email }}</td>
                                <td>{{ $sub->assignment->lesson->title ?? '—' }}</td>
                                <td>{{ $sub->approver->full_name ?? '—' }}</td>
                                <td>{{ $sub->approved_at ? $sub->approved_at->format('d.m.Y H:i') : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@stop

@section('scripts')
<script>
function approveSubmission(id) {
    var feedback = document.getElementById('feedback-' + id).value.trim();
    if (!feedback) { alert('Tilbakemeldingen kan ikke være tom'); return; }

    fetch('{{ url("/course/assignment-review") }}/' + id + '/approve', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ feedback: feedback })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            var el = document.getElementById('review-' + id);
            el.style.background = '#e8f5e9';
            el.innerHTML = '<div style="padding:1rem;text-align:center;color:#2e7d32;font-weight:600;"><i class="fa fa-check"></i> Godkjent!</div>';
        }
    });
}
</script>
@stop
