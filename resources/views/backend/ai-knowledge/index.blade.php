@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-lightbulb"></i> AI-kunnskap (kjente feil &amp; workarounds)</h3>
    <a href="{{ route('admin.inbox.index') }}" class="btn btn-default btn-sm pull-right">
        <i class="fa fa-arrow-left"></i> Tilbake til inbox
    </a>
</div>

<div class="col-md-12">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="alert alert-info">
        <strong>Hva er dette?</strong> Aktive saker her blir automatisk lagt inn i prompten til inbox-AI-en når den lager utkast. AI-en vil nevne disse for kunden hvis det er relevant. Bruk denne lista for ferske bugs, midlertidige problemer, eller spesielle workarounds. For mer permanent kunnskap, oppdater <code>docs/ai-knowledge.md</code> i koden.
    </div>

    {{-- Create form --}}
    <div class="panel panel-info">
        <div class="panel-heading"><strong>Legg til ny kjent feil eller workaround</strong></div>
        <div class="panel-body">
            <form action="{{ route('admin.ai-knowledge.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Tittel <span style="color:#862736;">*</span></label>
                            <input type="text" name="title" class="form-control" required maxlength="255" placeholder="f.eks. Vipps-betaling henger på Mac">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Alvorlighet</label>
                            <select name="severity" class="form-control">
                                <option value="info">Info</option>
                                <option value="low">Lav</option>
                                <option value="medium" selected>Middels</option>
                                <option value="high">Høy</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Oppdaget</label>
                            <input type="date" name="discovered_at" class="form-control" value="{{ now()->toDateString() }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kategori (valgfritt)</label>
                            <input type="text" name="category" class="form-control" placeholder="f.eks. innlogging, betaling, kurs">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Beskrivelse <span style="color:#862736;">*</span></label>
                    <textarea name="description" class="form-control" rows="3" required placeholder="Hva er problemet? Hva opplever eleven?"></textarea>
                </div>
                <div class="form-group">
                    <label>Workaround (valgfritt)</label>
                    <textarea name="workaround" class="form-control" rows="3" placeholder="Hva skal AI foreslå til eleven? f.eks. 'Be eleven prøve hard refresh (Cmd+Shift+R) eller bytte til Chrome'"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Legg til
                </button>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Alle saker ({{ $issues->count() }})</strong>
            — <span class="text-muted">aktive vises i AI-prompten</span>
        </div>
        <div class="panel-body" style="padding:0;">
            @if($issues->isEmpty())
                <p style="padding:20px; text-align:center; color:#999;">Ingen saker enda. Legg til den første ovenfor.</p>
            @else
                <table class="table table-striped" style="margin-bottom:0;">
                    <thead>
                        <tr>
                            <th>Tittel</th>
                            <th>Kategori</th>
                            <th>Alvor</th>
                            <th>Oppdaget</th>
                            <th>Status</th>
                            <th style="width:140px;">Handlinger</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($issues as $issue)
                            <tr style="{{ $issue->status === 'resolved' ? 'opacity:0.55;' : '' }}">
                                <td>
                                    <strong>{{ $issue->title }}</strong>
                                    <br>
                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($issue->description, 120) }}</small>
                                    @if($issue->workaround)
                                        <br><small style="color:#862736;"><i class="fa fa-wrench"></i> {{ \Illuminate\Support\Str::limit($issue->workaround, 100) }}</small>
                                    @endif
                                </td>
                                <td>{{ $issue->category ?: '—' }}</td>
                                <td>
                                    @php
                                        $sevColor = match($issue->severity) {
                                            'high' => '#d9534f',
                                            'medium' => '#f0ad4e',
                                            'low' => '#5bc0de',
                                            default => '#999',
                                        };
                                    @endphp
                                    <span style="background:{{ $sevColor }}; color:#fff; padding:2px 8px; border-radius:4px; font-size:11px; text-transform:uppercase;">
                                        {{ $issue->severity }}
                                    </span>
                                </td>
                                <td>{{ $issue->discovered_at?->format('d.m.Y') ?: '—' }}</td>
                                <td>
                                    @if($issue->status === 'active')
                                        <span class="label label-success">Aktiv</span>
                                    @else
                                        <span class="label label-default">Løst {{ $issue->resolved_at?->format('d.m.Y') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.ai-knowledge.toggle', $issue->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-default" title="Veksle aktiv/løst">
                                            @if($issue->status === 'active')
                                                <i class="fa fa-check"></i> Marker løst
                                            @else
                                                <i class="fa fa-undo"></i> Reaktiver
                                            @endif
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.ai-knowledge.destroy', $issue->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Slette denne saken permanent?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" title="Slett">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
