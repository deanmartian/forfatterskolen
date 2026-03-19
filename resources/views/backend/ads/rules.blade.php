@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-gavel"></i> Ad OS - Regler</h3>
    <a href="{{ route('admin.ads.dashboard') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake</a>
</div>

<div class="col-md-12">
    @if(session('message'))
        <div class="alert alert-{{ session('alert_type', 'info') }}">{{ session('message') }}</div>
    @endif

    {{-- Create Rule --}}
    <div class="panel panel-info">
        <div class="panel-heading"><strong><i class="fa fa-plus"></i> Opprett ny regel</strong></div>
        <div class="panel-body">
            <form action="{{ route('admin.ads.rules.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Regelnavn</label>
                            <input type="text" name="name" class="form-control" required placeholder="f.eks. Stopp høy CPA">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Metrikk</label>
                            <select name="metric" class="form-control">
                                <option value="cpa">CPA</option>
                                <option value="ctr">CTR</option>
                                <option value="cpc">CPC</option>
                                <option value="roas">ROAS</option>
                                <option value="spend">Forbruk</option>
                                <option value="conversions">Konverteringer</option>
                                <option value="impressions">Visninger</option>
                                <option value="clicks">Klikk</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>Operator</label>
                            <select name="operator" class="form-control">
                                <option value=">">></option>
                                <option value=">=">>=</option>
                                <option value="<"><</option>
                                <option value="<="><=</option>
                                <option value="==">=</option>
                                <option value="!=">!=</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Terskel</label>
                            <input type="number" name="threshold" class="form-control" step="0.01" required placeholder="f.eks. 200">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Handling</label>
                            <select name="action" class="form-control">
                                <option value="pause_campaign">Pause kampanje</option>
                                <option value="reduce_budget">Reduser budsjett</option>
                                <option value="increase_budget">Øk budsjett</option>
                                <option value="create_new_variants">Lag nye varianter</option>
                                <option value="request_human_review">Be om vurdering</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Risikonivå</label>
                            <select name="risk_level" class="form-control">
                                <option value="low">Lav</option>
                                <option value="medium" selected>Middels</option>
                                <option value="high">Høy</option>
                                <option value="critical">Kritisk</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Evalueringsvindu (dager)</label>
                            <input type="number" name="evaluation_window_days" class="form-control" value="7">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Min forbruk (kr)</label>
                            <input type="number" name="min_spend_threshold" class="form-control" step="0.01" placeholder="Valgfritt">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Prioritet (1-100)</label>
                            <input type="number" name="priority" class="form-control" value="50" min="1" max="100">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group" style="padding-top: 25px;">
                            <label><input type="checkbox" name="auto_apply" value="1"> Auto-utfør</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group" style="padding-top: 25px;">
                            <label><input type="checkbox" name="is_active" value="1" checked> Aktiv</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group" style="padding-top: 20px;">
                            <button type="submit" class="btn btn-info btn-block"><i class="fa fa-plus"></i> Opprett</button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Beskrivelse (valgfritt)</label>
                    <input type="text" name="description" class="form-control" placeholder="Kort beskrivelse av hva regelen gjør">
                </div>
            </form>
        </div>
    </div>

    {{-- Existing Rules --}}
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Aktive regler</strong></div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Navn</th>
                        <th>Betingelse</th>
                        <th>Handling</th>
                        <th>Risiko</th>
                        <th>Vindu</th>
                        <th>Auto</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rules as $rule)
                        <tr class="{{ !$rule->is_active ? 'text-muted' : '' }}">
                            <td>
                                <strong>{{ $rule->name }}</strong>
                                @if($rule->description)
                                    <br><small class="text-muted">{{ $rule->description }}</small>
                                @endif
                            </td>
                            <td><code>{{ $rule->metric }} {{ $rule->operator }} {{ $rule->threshold }}</code></td>
                            <td>{{ ucfirst(str_replace('_', ' ', $rule->action)) }}</td>
                            <td>
                                <span class="label label-{{ $rule->risk_level === 'low' ? 'success' : ($rule->risk_level === 'medium' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($rule->risk_level) }}
                                </span>
                            </td>
                            <td>{{ $rule->evaluation_window_days }}d</td>
                            <td>{!! $rule->auto_apply ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-muted"></i>' !!}</td>
                            <td>
                                <span class="label label-{{ $rule->is_active ? 'success' : 'default' }}">{{ $rule->is_active ? 'Aktiv' : 'Inaktiv' }}</span>
                            </td>
                            <td>
                                <form action="{{ route('admin.ads.rules.delete', $rule->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Slett denne regelen?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">Ingen regler opprettet. Bruk skjemaet over for å lage din første regel.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
