@extends('backend.layout')

@section('page_title', 'Publikasjoner &rsaquo; Forfatterskolen Admin')

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-book"></i> Publikasjoner (Indiemoon ombrekk)</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tittel</th>
                    <th>Forfatter</th>
                    <th>Bruker</th>
                    <th>Status</th>
                    <th>Ord</th>
                    <th>Sider</th>
                    <th>Tema</th>
                    <th>Format</th>
                    <th>Opprettet</th>
                </tr>
                </thead>
                <tbody>
                @foreach($publications as $publication)
                    <tr>
                        <td>{{ $publication->id }}</td>
                        <td>
                            <a href="{{ route('admin.publications.show', $publication->id) }}">
                                {{ $publication->title }}
                            </a>
                        </td>
                        <td>{{ $publication->author_name }}</td>
                        <td>{{ $publication->user->name ?? '—' }}</td>
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
                        <td>{{ $publication->word_count ? number_format($publication->word_count, 0, ',', ' ') : '—' }}</td>
                        <td>{{ $publication->page_count ?? '—' }}</td>
                        <td>{{ $publication->theme }}</td>
                        <td>{{ $publication->trim_size }}</td>
                        <td>{{ $publication->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="pull-right">{!! $publications->render() !!}</div>
        </div>
    </div>
@stop
