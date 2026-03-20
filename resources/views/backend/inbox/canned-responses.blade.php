@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-bolt"></i> Hurtigsvar</h3>
    <a href="{{ route('admin.inbox.index') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake til inbox</a>
</div>

<div class="col-md-12">
    @if(session('message'))
        <div class="alert alert-{{ session('alert_type', 'info') }}">{{ session('message') }}</div>
    @endif

    {{-- Create --}}
    <div class="panel panel-info">
        <div class="panel-heading"><strong>Opprett nytt hurtigsvar</strong></div>
        <div class="panel-body">
            <form action="{{ route('admin.inbox.canned-responses.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tittel</label>
                            <input type="text" name="title" class="form-control" required placeholder="f.eks. Velkomstsvar">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Snarvei (valgfritt)</label>
                            <input type="text" name="shortcut" class="form-control" placeholder="f.eks. /velkommen">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kategori (valgfritt)</label>
                            <input type="text" name="category" class="form-control" placeholder="f.eks. Kurs">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Tekst</label>
                    <textarea name="body" class="form-control" rows="4" required placeholder="Skriv hurtigsvar-teksten her..."></textarea>
                </div>
                <button type="submit" class="btn btn-info"><i class="fa fa-plus"></i> Opprett</button>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Eksisterende hurtigsvar</strong></div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead><tr><th>Tittel</th><th>Snarvei</th><th>Kategori</th><th>Tekst</th><th>Brukt</th></tr></thead>
                <tbody>
                    @forelse($responses as $r)
                        <tr>
                            <td><strong>{{ $r->title }}</strong></td>
                            <td><code>{{ $r->shortcut ?? '-' }}</code></td>
                            <td>{{ $r->category ?? '-' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($r->body, 80) }}</td>
                            <td>{{ $r->usage_count }}x</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Ingen hurtigsvar opprettet ennå.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
