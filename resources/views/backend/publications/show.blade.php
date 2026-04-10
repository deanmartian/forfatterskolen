@extends('backend.layout')

@section('page_title')Publikasjon: {{ $publication->title }} &rsaquo; Forfatterskolen Admin@endsection

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-book"></i> Publikasjon: {{ $publication->title }}</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a href="{{ route('admin.publications.index') }}" class="btn btn-default margin-bottom">
            <i class="fa fa-arrow-left"></i> Tilbake til oversikt
        </a>

        <div class="panel panel-default">
            <div class="panel-heading"><strong>Detaljer</strong></div>
            <div class="panel-body">
                <table class="table table-striped">
                    <tr><th style="width:200px">ID</th><td>{{ $publication->id }}</td></tr>
                    <tr><th>Tittel</th><td>{{ $publication->title }}</td></tr>
                    <tr><th>Undertittel</th><td>{{ $publication->subtitle ?? '—' }}</td></tr>
                    <tr><th>Forfatter</th><td>{{ $publication->author_name }}</td></tr>
                    <tr><th>Bruker</th><td>{{ $publication->user->name ?? '—' }} (ID: {{ $publication->user_id }})</td></tr>
                    <tr><th>ISBN</th><td>{{ $publication->isbn ?? '—' }}</td></tr>
                    <tr><th>Språk</th><td>{{ $publication->language }}</td></tr>
                    <tr><th>Sjanger</th><td>{{ $publication->genre ?? '—' }}</td></tr>
                    <tr><th>Beskrivelse</th><td>{{ $publication->description ?? '—' }}</td></tr>
                    <tr><th>Dedikasjon</th><td>{{ $publication->dedication ?? '—' }}</td></tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @switch($publication->status)
                                @case('draft')
                                    <span class="label label-default">Utkast</span>
                                    @break
                                @case('parsing')
                                @case('composing')
                                @case('generating')
                                    <span class="label label-warning">{{ ucfirst($publication->status) }}</span>
                                    @break
                                @case('preview')
                                    <span class="label label-info">Forhåndsvisning</span>
                                    @break
                                @case('published')
                                @case('approved')
                                    <span class="label label-success">Publisert</span>
                                    @break
                                @case('error')
                                    <span class="label label-danger">Feil</span>
                                    @break
                                @default
                                    <span class="label label-default">{{ $publication->status }}</span>
                            @endswitch
                        </td>
                    </tr>
                    @if($publication->error_message)
                        <tr><th>Feilmelding</th><td class="text-danger">{{ $publication->error_message }}</td></tr>
                    @endif
                    <tr><th>Wizard-steg</th><td>{{ $publication->wizard_step }}</td></tr>
                </table>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><strong>Design</strong></div>
            <div class="panel-body">
                <table class="table table-striped">
                    <tr><th style="width:200px">Tema</th><td>{{ $publication->theme }}</td></tr>
                    <tr><th>Format (trim size)</th><td>{{ $publication->trim_size }}</td></tr>
                    <tr><th>Papirtype</th><td>{{ $publication->paper_type }}</td></tr>
                    <tr><th>Innbinding</th><td>{{ $publication->binding_type }}</td></tr>
                    <tr><th>Laminering</th><td>{{ $publication->cover_lamination }}</td></tr>
                </table>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><strong>Statistikk</strong></div>
            <div class="panel-body">
                <table class="table table-striped">
                    <tr><th style="width:200px">Ord</th><td>{{ $publication->word_count ? number_format($publication->word_count, 0, ',', ' ') : '—' }}</td></tr>
                    <tr><th>Sider</th><td>{{ $publication->page_count ?? '—' }}</td></tr>
                    <tr><th>Kapitler</th><td>{{ $publication->chapter_count ?? '—' }}</td></tr>
                    <tr><th>Ryggbredde (mm)</th><td>{{ $publication->spine_width_mm ?? '—' }}</td></tr>
                </table>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><strong>Filer</strong></div>
            <div class="panel-body">
                <table class="table table-striped">
                    <tr>
                        <th style="width:200px">Kilde-manuskript</th>
                        <td>{{ $publication->source_manuscript }}</td>
                    </tr>
                    @if($publication->output_pdf)
                        <tr>
                            <th>PDF</th>
                            <td><a href="{{ asset('storage/' . $publication->output_pdf) }}" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-download"></i> Last ned PDF</a></td>
                        </tr>
                    @endif
                    @if($publication->output_epub)
                        <tr>
                            <th>EPUB</th>
                            <td><a href="{{ asset('storage/' . $publication->output_epub) }}" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-download"></i> Last ned EPUB</a></td>
                        </tr>
                    @endif
                    @if($publication->output_docx)
                        <tr>
                            <th>DOCX</th>
                            <td><a href="{{ asset('storage/' . $publication->output_docx) }}" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-download"></i> Last ned DOCX</a></td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><strong>Tidsstempler</strong></div>
            <div class="panel-body">
                <table class="table table-striped">
                    <tr><th style="width:200px">Opprettet</th><td>{{ $publication->created_at->format('d.m.Y H:i') }}</td></tr>
                    <tr><th>Oppdatert</th><td>{{ $publication->updated_at->format('d.m.Y H:i') }}</td></tr>
                </table>
            </div>
        </div>

        <div class="panel panel-danger">
            <div class="panel-heading"><strong>Faresone</strong></div>
            <div class="panel-body">
                <form action="{{ route('admin.publications.destroy', $publication->id) }}" method="POST"
                      onsubmit="return confirm('Er du sikker på at du vil slette denne publikasjonen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash"></i> Slett publikasjon
                    </button>
                </form>
            </div>
        </div>
    </div>
@stop
