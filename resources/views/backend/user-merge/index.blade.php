@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-users"></i> Sammenslå duplikat-brukerkontoer</h3>
</div>

<div class="col-md-12">
    @if(session('message'))
        <div class="alert alert-{{ session('alert_type', 'info') }}">{{ session('message') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="alert alert-info">
        <strong>Hva er dette?</strong> Hvis en person har to kontoer (f.eks. en gammel elev-konto + en redaktør-konto fra senere), kan du slå dem sammen her. Hovedkontoen beholder alt, og den sekundære slettes etter at all data er flyttet over. Den sekundære e-postadressen lagres slik at brukeren fortsatt kan logge inn med begge.
    </div>

    {{-- Auto-foreslåtte duplikater --}}
    @if(!empty($suggestions))
        <div class="panel panel-warning">
            <div class="panel-heading">
                <strong><i class="fa fa-magic"></i> Foreslåtte duplikater</strong>
                <small class="text-muted">— brukere med samme navn, ulik e-post</small>
            </div>
            <div class="panel-body" style="padding:0;">
                <table class="table table-striped" style="margin-bottom:0;">
                    <thead>
                        <tr>
                            <th>Navn</th>
                            <th>Kontoer</th>
                            <th style="width:120px;">Handling</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suggestions as $sug)
                            <tr>
                                <td>
                                    <strong>{{ $sug['name'] }}</strong>
                                    @if($sug['has_editor'])
                                        <span class="label label-warning" style="margin-left:6px;">Har redaktør</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach($sug['users'] as $u)
                                        @php
                                            $roleLabel = ['1'=>'Admin','2'=>'Elev','3'=>'Redaktør','4'=>'Giutbok'][$u->role] ?? '?';
                                            $roleColor = ['1'=>'#dc2626','2'=>'#3b82f6','3'=>'#7c3aed','4'=>'#059669'][$u->role] ?? '#999';
                                        @endphp
                                        <div style="margin-bottom:4px;">
                                            <span style="background:{{ $roleColor }}; color:#fff; padding:2px 8px; border-radius:4px; font-size:11px;">{{ $roleLabel }}</span>
                                            <strong>#{{ $u->id }}</strong>
                                            <small>{{ $u->email }}</small>
                                            @if(!$u->is_active)
                                                <span class="label label-default">inaktiv</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @php
                                        $editor = $sug['users']->where('role', 3)->first();
                                        $learner = $sug['users']->where('role', 2)->first();
                                    @endphp
                                    @if($editor && $learner)
                                        <a href="{{ route('admin.user-merge.index', ['primary_id' => $editor->id, 'secondary_id' => $learner->id]) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fa fa-arrow-right"></i> Forhåndsvis
                                        </a>
                                    @else
                                        <small class="text-muted">Velg manuelt</small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Manuelt søk + forhåndsvisning --}}
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Velg kontoer manuelt</strong></div>
        <div class="panel-body">
            <form method="GET" action="{{ route('admin.user-merge.index') }}">
                <div class="row">
                    <div class="col-md-6">
                        <label><strong>Hovedkonto</strong> (denne beholdes — bruk redaktør-kontoen)</label>
                        <input type="number" name="primary_id" value="{{ $primary?->id }}" class="form-control" placeholder="Bruker-ID">
                        @if($primary)
                            <small class="text-muted">{{ $primary->first_name }} {{ $primary->last_name }} &lt;{{ $primary->email }}&gt; — {{ ['1'=>'Admin','2'=>'Elev','3'=>'Redaktør','4'=>'Giutbok'][$primary->role] ?? '?' }}</small>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label><strong>Sekundærkonto</strong> (denne fjernes — bruk elev-kontoen)</label>
                        <input type="number" name="secondary_id" value="{{ $secondary?->id }}" class="form-control" placeholder="Bruker-ID">
                        @if($secondary)
                            <small class="text-muted">{{ $secondary->first_name }} {{ $secondary->last_name }} &lt;{{ $secondary->email }}&gt; — {{ ['1'=>'Admin','2'=>'Elev','3'=>'Redaktør','4'=>'Giutbok'][$secondary->role] ?? '?' }}</small>
                        @endif
                    </div>
                </div>
                <div style="margin-top:12px;">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-eye"></i> Forhåndsvis merge</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Forhåndsvisning + bekreftelses-form --}}
    @if($primary && $secondary && $primary->id !== $secondary->id)
        <div class="panel panel-info">
            <div class="panel-heading"><strong>Forhåndsvisning av merge</strong></div>
            <div class="panel-body">
                @if(empty($preview))
                    <p class="text-muted">Sekundæren har ingen rader i noen av tabellene som skal flyttes. Den kan trygt slettes.</p>
                @else
                    <p>Følgende rader vil bli flyttet fra <strong>#{{ $secondary->id }}</strong> til <strong>#{{ $primary->id }}</strong>:</p>
                    <table class="table table-condensed">
                        <thead>
                            <tr><th>Tabell.kolonne</th><th style="width:120px;">Antall rader</th></tr>
                        </thead>
                        <tbody>
                            @foreach($preview as $key => $count)
                                <tr><td><code>{{ $key }}</code></td><td>{{ $count }}</td></tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><th>Totalt</th><th>{{ array_sum($preview) }}</th></tr>
                        </tfoot>
                    </table>
                @endif

                <hr>

                <form method="POST" action="{{ route('admin.user-merge.merge') }}">
                    @csrf
                    <input type="hidden" name="primary_id" value="{{ $primary->id }}">
                    <input type="hidden" name="secondary_id" value="{{ $secondary->id }}">

                    <div class="alert alert-warning" style="margin-top:10px;">
                        <strong><i class="fa fa-exclamation-triangle"></i> Dette er irreversibelt!</strong>
                        <ul style="margin-top:8px;">
                            <li>All data fra #{{ $secondary->id }} flyttes til #{{ $primary->id }}</li>
                            <li>Den sekundære e-posten <strong>{{ $secondary->email }}</strong> lagres som sekundær på hovedkontoen</li>
                            <li>Den sekundære kontoen blir soft-deleted (kan teoretisk gjenopprettes via DB)</li>
                            <li>Brukeren kan logge inn med BEGGE e-postadressene etter merge</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <label>Skriv <code>JA SLÅ SAMMEN</code> for å bekrefte:</label>
                        <input type="text" name="confirm" class="form-control" placeholder="JA SLÅ SAMMEN" required>
                    </div>

                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-arrows-alt-h"></i> Slå sammen kontoer
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
