@extends('backend.layout')

@section('page_title', 'Autosvar — Inbox — Forfatterskolen Admin')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-reply-all"></i> Autosvar</h3>
    <div class="pull-right">
        <a href="{{ route('admin.inbox.index') }}" class="btn btn-sm btn-default"><i class="fa fa-arrow-left"></i> Tilbake til inbox</a>
        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newAutoReplyModal"><i class="fa fa-plus"></i> Nytt autosvar</button>
    </div>
</div>

<div class="col-md-12">
    @if(session('message'))
        <div class="alert alert-{{ session('alert_type', 'info') }}">{{ session('message') }}</div>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading"><strong>Aktive og inaktive autosvar</strong></div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Navn</th>
                    <th>Type</th>
                    <th>Innboks</th>
                    <th>Forsinkelse</th>
                    <th>AI</th>
                    <th>Status</th>
                    <th>Handlinger</th>
                </tr>
            </thead>
            <tbody>
                @forelse($autoReplies as $ar)
                    <tr>
                        <td><strong>{{ $ar->name }}</strong></td>
                        <td>
                            @if($ar->trigger_type === 'new_conversation')
                                <span class="label label-info">Ny samtale</span>
                            @elseif($ar->trigger_type === 'out_of_office')
                                <span class="label label-warning">Fravær</span>
                            @else
                                <span class="label label-default">{{ $ar->trigger_type }}</span>
                            @endif
                        </td>
                        <td>{{ $ar->inbox ?: 'Alle' }}</td>
                        <td>{{ $ar->send_delay_minutes ? $ar->send_delay_minutes . ' min' : 'Umiddelbart' }}</td>
                        <td>{!! $ar->use_ai ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-muted"></i>' !!}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.inbox.auto-replies.toggle', $ar->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-{{ $ar->is_active ? 'success' : 'default' }}">
                                    {{ $ar->is_active ? 'Aktiv' : 'Inaktiv' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.inbox.auto-replies.delete', $ar->id) }}" style="display:inline;" onsubmit="return confirm('Slett dette autosvaret?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="background:#f9f9f9;font-size:12px;color:#666;padding:8px 16px;">
                            <strong>Mal:</strong> {{ \Illuminate\Support\Str::limit($ar->reply_template, 200) }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-muted text-center">Ingen autosvar opprettet ennå.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal for nytt autosvar --}}
<div class="modal fade" id="newAutoReplyModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:10px;overflow:hidden;">
            <div style="background:#862736;padding:18px 24px;color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:0.8;">&times;</button>
                <h4 style="margin:0;font-size:17px;"><i class="fa fa-reply-all"></i> Nytt autosvar</h4>
            </div>
            <form action="{{ route('admin.inbox.auto-replies.store') }}" method="POST">
                @csrf
                <div style="padding:24px;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Navn</label>
                                <input type="text" name="name" class="form-control" placeholder="F.eks. 'Bekreftelsesmail' eller 'Helgefravær'" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type</label>
                                <select name="trigger_type" class="form-control">
                                    <option value="new_conversation">Ny samtale (bekreftelse)</option>
                                    <option value="out_of_office">Fravær/Out of office</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Innboks (valgfritt)</label>
                                <select name="inbox" class="form-control">
                                    <option value="">Alle innbokser</option>
                                    <option value="post@forfatterskolen.no">post@forfatterskolen.no</option>
                                    <option value="sven.inge@forfatterskolen.no">sven.inge@forfatterskolen.no</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Forsinkelse (minutter)</label>
                                <input type="number" name="send_delay_minutes" class="form-control" value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" style="padding-top: 25px;">
                                <label><input type="checkbox" name="use_ai" value="1"> Bruk AI til å tilpasse svaret</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Svarmal</label>
                        <textarea name="reply_template" class="form-control" rows="8" placeholder="Hei!&#10;&#10;Takk for din henvendelse til Forfatterskolen. Vi har mottatt meldingen din og vil svare deg så snart som mulig.&#10;&#10;Med vennlig hilsen,&#10;Forfatterskolen" required></textarea>
                        <p class="help-block">Tilgjengelige variabler: {name}, {email}, {subject}</p>
                    </div>
                    <label><input type="checkbox" name="is_active" value="1" checked> Aktiver umiddelbart</label>
                </div>
                <div style="padding:0 24px 24px;">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Opprett autosvar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Avbryt</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
