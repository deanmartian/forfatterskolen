@extends('backend.layout')

@section('page_title', 'Påbyggingstreff &rsaquo; Forfatterskolen Admin')

@section('styles')
<style>
    .treff-stats { display: flex; gap: 16px; margin-bottom: 24px; }
    .treff-stat { background: #fff; border-radius: 8px; padding: 16px 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); flex: 1; }
    .treff-stat .number { font-size: 32px; font-weight: 700; color: #862736; }
    .treff-stat .label { font-size: 13px; color: #888; }
</style>
@stop

@section('content')
<div class="container-fluid" style="padding: 20px;">
    <h2>Påbyggingstreff — 8. og 9. mai 2026</h2>
    <p class="text-muted">Kurs 120 &mdash; Oversikt over påmeldinger</p>

    @php
        $fridaySignups = $signups->where('pabygg_treff_day', 'friday');
        $saturdaySignups = $signups->where('pabygg_treff_day', 'saturday');
        $notSignedUp = $allEnrolled->filter(fn($ct) => !$ct->pabygg_treff_day);
    @endphp

    <div class="treff-stats">
        <div class="treff-stat">
            <div class="number">{{ $fridaySignups->count() }}</div>
            <div class="label">Fredag 8. mai</div>
        </div>
        <div class="treff-stat">
            <div class="number">{{ $saturdaySignups->count() }}</div>
            <div class="label">Lørdag 9. mai</div>
        </div>
        <div class="treff-stat">
            <div class="number">{{ $notSignedUp->count() }}</div>
            <div class="label">Ikke påmeldt</div>
        </div>
    </div>

    {{-- Friday --}}
    <h4 style="color: #862736;">Fredag 8. mai ({{ $fridaySignups->count() }})</h4>
    <table class="table table-striped table-bordered" style="margin-bottom: 30px;">
        <thead><tr><th>#</th><th>Navn</th><th>E-post</th></tr></thead>
        <tbody>
        @forelse($fridaySignups as $i => $ct)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $ct->user->name ?? '—' }}</td>
                <td>{{ $ct->user->email ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="text-muted">Ingen påmeldt ennå</td></tr>
        @endforelse
        </tbody>
    </table>

    {{-- Saturday --}}
    <h4 style="color: #862736;">Lørdag 9. mai ({{ $saturdaySignups->count() }})</h4>
    <table class="table table-striped table-bordered" style="margin-bottom: 30px;">
        <thead><tr><th>#</th><th>Navn</th><th>E-post</th></tr></thead>
        <tbody>
        @forelse($saturdaySignups as $i => $ct)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $ct->user->name ?? '—' }}</td>
                <td>{{ $ct->user->email ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="text-muted">Ingen påmeldt ennå</td></tr>
        @endforelse
        </tbody>
    </table>

    {{-- Not signed up --}}
    <h4>Ikke påmeldt ({{ $notSignedUp->count() }})</h4>
    <table class="table table-striped table-bordered">
        <thead><tr><th>#</th><th>Navn</th><th>E-post</th></tr></thead>
        <tbody>
        @forelse($notSignedUp as $i => $ct)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $ct->user->name ?? '—' }}</td>
                <td>{{ $ct->user->email ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="text-muted">Alle er påmeldt!</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@stop
