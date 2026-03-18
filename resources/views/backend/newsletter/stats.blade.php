@extends('backend.layout')

@section('title')
    <title>Statistikk — {{ $newsletter->subject }}</title>
@stop

@section('content')
<div class="container-fluid" style="padding: 20px; max-width: 800px;">
    <a href="{{ route('admin.newsletter.index') }}" class="btn btn-sm btn-outline-secondary mb-3">← Tilbake</a>

    <h3>{{ $newsletter->subject }}</h3>
    <p class="text-muted">Sendt {{ $newsletter->sent_at?->format('d.m.Y H:i') ?? '—' }} &middot; Segment: {{ $newsletter->segment }}</p>

    <div class="row">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div style="font-size: 32px; font-weight: 700;">{{ number_format($newsletter->total_sends ?? 0) }}</div>
                    <div class="text-muted">Mottakere</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div style="font-size: 32px; font-weight: 700; color: #28a745;">{{ number_format($newsletter->sent_count ?? 0) }}</div>
                    <div class="text-muted">Sendt</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div style="font-size: 32px; font-weight: 700; color: #ffc107;">{{ number_format($newsletter->pending_count ?? 0) }}</div>
                    <div class="text-muted">Ventende</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div style="font-size: 32px; font-weight: 700; color: #dc3545;">{{ number_format($newsletter->failed_count ?? 0) }}</div>
                    <div class="text-muted">Feilet</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
