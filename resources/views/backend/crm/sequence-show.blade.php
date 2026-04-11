@extends('backend.layout')

@section('page_title', $sequence->name . ' — CRM')

@section('content')
<div class="container-fluid" style="padding: 20px;">
    <a href="{{ route('admin.crm.index', ['tab' => 'sequences']) }}" class="btn btn-sm btn-outline-secondary mb-3">← Tilbake</a>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3>{{ $sequence->name }}</h3>
            <p class="text-muted mb-0">
                Trigger: <code>{{ $sequence->trigger_event }}</code> &middot;
                Fra: {{ $sequence->from_type }} &middot;
                {!! $sequence->is_active ? '<span class="badge badge-success">Aktiv</span>' : '<span class="badge badge-secondary">Inaktiv</span>' !!}
            </p>
        </div>
        <form method="POST" action="{{ route('admin.crm.sequences.toggle', $sequence->id) }}">
            @csrf
            <button type="submit" class="btn btn-sm {{ $sequence->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                {{ $sequence->is_active ? 'Deaktiver' : 'Aktiver' }}
            </button>
        </form>
    </div>

    @if($sequence->description)
        <p>{{ $sequence->description }}</p>
    @endif

    <!-- Steg -->
    <div class="card">
        <div class="card-header"><strong>E-poster i sekvensen</strong></div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Emne</th>
                        <th>Forsinkelse</th>
                        <th>Tidspunkt</th>
                        <th>Neste utsendelse</th>
                        <th>I kø</th>
                        <th>Fra</th>
                        <th>Kun uten kurs</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($sequence->steps as $step)
                    <tr>
                        <td>{{ $step->step_number }}</td>
                        <td>{{ $step->subject }}</td>
                        <td>{{ $step->delay_hours }}t</td>
                        <td>{{ $step->send_time ?? 'Straks' }}</td>
                        <td>
                            @php
                                $nextSend = DB::table('email_automation_queue')
                                    ->where('sequence_id', $sequence->id)
                                    ->where('step_id', $step->id)
                                    ->where('status', 'pending')
                                    ->orderBy('scheduled_at')
                                    ->value('scheduled_at');
                            @endphp
                            @if($nextSend)
                                <span class="badge badge-info">{{ \Carbon\Carbon::parse($nextSend)->format('d.m.Y H:i') }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ DB::table('email_automation_queue')->where('sequence_id', $sequence->id)->where('step_id', $step->id)->where('status', 'pending')->count() }}</span>
                        </td>
                        <td><small>{{ $step->from_type }}</small></td>
                        <td>{!! $step->only_without_active_course ? '<i class="fa fa-check text-success"></i>' : '' !!}</td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('admin.crm.sequences.steps.edit', [$sequence->id, $step->id]) }}" class="btn btn-xs btn-primary" title="Rediger">
                                <i class="fa fa-pencil"></i> Rediger
                            </a>
                            <form method="POST" action="{{ route('admin.crm.sequences.steps.delete', [$sequence->id, $step->id]) }}" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Slette steg?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addStepModal"><i class="fa fa-plus"></i> Legg til nytt steg</button>
        </div>
    </div>
</div>

{{-- Modal: Legg til steg --}}
<div class="modal fade" id="addStepModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#862736;color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-plus"></i> Nytt steg i sekvensen</h4>
            </div>
            <form method="POST" action="{{ route('admin.crm.sequences.steps.create', $sequence->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Steg nummer</label>
                        <input type="number" name="step_number" class="form-control" value="{{ $sequence->steps->count() + 1 }}" required>
                    </div>
                    <div class="form-group">
                        <label>Emne</label>
                        <input type="text" name="subject" class="form-control" placeholder="F.eks. 'Påminnelse om webinaret'" required>
                    </div>
                    <div class="form-group">
                        <label>Forsinkelse (timer etter forrige)</label>
                        <input type="number" name="delay_hours" class="form-control" value="24" min="0">
                    </div>
                    <div class="form-group">
                        <label>Send kl. (valgfritt)</label>
                        <input type="time" name="send_time" class="form-control" value="10:00">
                    </div>
                    <div class="form-group">
                        <label>Fra-type</label>
                        <select name="from_type" class="form-control">
                            <option value="transactional">Transaksjonell</option>
                            <option value="newsletter">Nyhetsbrev</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Innhold (kan redigeres etterpå)</label>
                        <textarea name="body_html" class="form-control" rows="5" placeholder="Skriv e-postinnholdet her..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Opprett steg</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Avbryt</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
