@extends($layout)

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>
        .dropdown-container {
            position: relative;
            width: 100%;
        }
        .dropdown-results {
            position: absolute;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            background-color: #fff;
            z-index: 1000;
        }
        .dropdown-results div {
            padding: 8px;
            cursor: pointer;
        }
        .dropdown-results div:hover {
            background-color: #f1f1f1;
        }
    </style>
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Projects</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12" id="app-container">
        <project :learners="{{ json_encode($learners) }}" :activities="{{ json_encode($activities) }}"
                 :projects="{{ json_encode($projects) }}" :project-notes="{{ json_encode($projectNotes) }}" 
                 :next-project-number="{{ json_encode($nextProjectNumber) }}"
                 :editors="{{ json_encode($editors) }}"></project>
    </div>
@stop

@section('scripts')
    <script src="{{ mix('/js/app.js') }}"></script>
@stop