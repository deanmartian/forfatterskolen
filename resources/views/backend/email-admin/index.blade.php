@extends('backend.layout')

@section('title')
    <title>E-postmaler</title>
@stop

@section('content')
<div class="container-fluid" style="padding: 20px;">
    <h3><i class="fa fa-envelope"></i> E-postmaler</h3>
    <p class="text-muted">{{ $templates->count() }} maler totalt</p>

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-8">
            <form class="form-inline" method="GET" action="{{ route('admin.email-admin.index') }}">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Søk på navn eller emne..." value="{{ $search }}" style="margin-right: 8px; width: 250px;">
                <select name="category" class="form-control form-control-sm" style="margin-right: 8px;">
                    <option value="all">Alle kategorier</option>
                    @foreach(array_keys($categories) as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fa fa-search"></i> Søk</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Malnavn</th>
                <th>Emne</th>
                <th>Fra</th>
                <th style="width: 120px;">Handlinger</th>
            </tr>
        </thead>
        <tbody>
            @foreach($templates as $template)
                <tr>
                    <td>{{ $template->id }}</td>
                    <td><strong>{{ $template->page_name }}</strong></td>
                    <td>{{ \Illuminate\Support\Str::limit($template->subject, 50) }}</td>
                    <td><small>{{ $template->from_email ?? '—' }}</small></td>
                    <td>
                        <a href="{{ route('admin.email-admin.edit', $template->id) }}" class="btn btn-xs btn-primary" title="Rediger">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a href="{{ route('admin.email-admin.preview', $template->id) }}" class="btn btn-xs btn-info" title="Forhåndsvis" target="_blank">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
