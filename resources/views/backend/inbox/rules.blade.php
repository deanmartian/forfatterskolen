@extends('backend.layout')

@section('page_title', 'Inbox-regler — Forfatterskolen Admin')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-gavel"></i> Inbox-regler</h3>
    <div class="pull-right">
        <a href="{{ route('admin.inbox.index') }}" class="btn btn-sm btn-default"><i class="fa fa-arrow-left"></i> Tilbake til inbox</a>
        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newRuleModal"><i class="fa fa-plus"></i> Ny regel</button>
    </div>
</div>

<div class="col-md-12">
    @if(session('message'))
        <div class="alert alert-{{ session('alert_type', 'info') }}">{{ session('message') }}</div>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading"><strong>Auto-tildelingsregler</strong>
            <span class="pull-right text-muted" style="font-size:12px;">
                Status: {{ config('inbox.auto_assign.enabled') ? '✅ Aktivert' : '❌ Deaktivert' }}
                &nbsp;|&nbsp; Standard: {{ \App\User::find(config('inbox.auto_assign.default_user_id'))?->first_name ?? 'Ingen' }}
            </span>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Navn</th>
                    <th>Nøkkelord</th>
                    <th>Tildeles til</th>
                    <th>Prioritet</th>
                    <th>Kategori</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($rules as $i => $rule)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $rule['name'] }}</strong></td>
                        <td>
                            @foreach($rule['keywords'] as $kw)
                                <span class="label label-default">{{ $kw }}</span>
                            @endforeach
                        </td>
                        <td>{{ \App\User::find($rule['assign_to'])?->first_name ?? 'Ukjent' }}</td>
                        <td>{{ ucfirst($rule['set_priority'] ?? 'normal') }}</td>
                        <td>{{ $rule['set_category'] ?? '-' }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.inbox.rules.delete', $i) }}" style="display:inline;" onsubmit="return confirm('Slett denne regelen?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-muted text-center">Ingen regler opprettet ennå.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="panel panel-info">
        <div class="panel-body">
            <strong>Slik fungerer regler:</strong> Når en ny e-post kommer inn, sjekkes emne og innhold mot nøkkelordene.
            Første regel som matcher vinner. Hvis ingen regel matcher, tildeles samtalen til <strong>{{ \App\User::find(config('inbox.auto_assign.default_user_id'))?->first_name ?? 'standard-admin' }}</strong>
            (kun for nye kunder uten tidligere kontakt).
        </div>
    </div>
</div>

{{-- Modal for ny regel --}}
<div class="modal fade" id="newRuleModal">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;">
            <div style="background:#862736;padding:18px 24px;color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8;">&times;</button>
                <h4 style="margin:0;font-size:17px;"><i class="fa fa-gavel"></i> Ny inbox-regel</h4>
            </div>
            <form action="{{ route('admin.inbox.rules.store') }}" method="POST">
                @csrf
                <div style="padding:24px;">
                    <div class="form-group">
                        <label>Regelnavn</label>
                        <input type="text" name="name" class="form-control" placeholder="F.eks. 'Coaching-henvendelser'" required>
                    </div>
                    <div class="form-group">
                        <label>Nøkkelord (komma-separert)</label>
                        <input type="text" name="keywords" class="form-control" placeholder="coaching, coach, veiledning" required>
                        <p class="help-block">Samtaler med disse ordene i emne eller innhold vil matche.</p>
                    </div>
                    <div class="form-group">
                        <label>Tildel til</label>
                        <select name="assign_to" class="form-control" required>
                            @foreach($teamMembers as $member)
                                <option value="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sett prioritet</label>
                                <select name="set_priority" class="form-control">
                                    <option value="normal">Normal</option>
                                    <option value="high">Høy</option>
                                    <option value="low">Lav</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sett kategori</label>
                                <select name="set_category" class="form-control">
                                    <option value="">Ingen</option>
                                    <option value="kurs">Kurs</option>
                                    <option value="betaling">Betaling</option>
                                    <option value="teknisk">Teknisk</option>
                                    <option value="bok">Bok/Manus</option>
                                    <option value="coaching">Coaching</option>
                                    <option value="webinar">Webinar</option>
                                    <option value="generelt">Generelt</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="padding:0 24px 24px;">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Opprett regel</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Avbryt</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
