@extends($layout)

@section('title')
    <title>{{ $project->title }} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div id="app-container">
        <div class="page-toolbar">
            <a href="{{ $backRoute }}" class="btn btn-default" style="margin-right: 10px">
                << {{ trans('site.back') }}
            </a>

            <h3><em>{{ $project->name }} Notes</em></h3>
        </div>
        <div class="col-md-12">
            <project-notes :current-project="{{ json_encode($project) }}"></project-notes>
        </div>
    </div>
@stop

@section('scripts')
    <script src="{{ asset('js/app.js?v='.time()) }}"></script>
@stop