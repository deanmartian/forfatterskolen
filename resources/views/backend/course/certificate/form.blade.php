@extends('backend.layout')

@section('title')
    <title>Certificate &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <style type="text/css" media="screen">
        #editor {
            min-height: 90vh;
            margin-top: 50px;
            font-size: 16px;
        }
    </style>
@stop

@section('content')

    @include('backend.course.partials.toolbar')

    <div class="course-container">

        @include('backend.partials.course_submenu')

        <div class="col-sm-12 col-md-10 sub-right-content">
            <div class="col-sm-12 col-md-12">
                <form action="{{ route('admin.package.save-certificate-template', [
                    'course_id' => $package->course_id, 
                    'package_id' => $package->id
                ]) }}" method="POST"
                      style="display: inline">
                    {{ csrf_field() }}
                    <button class="btn btn-success pull-right" type="submit">
                        Save
                    </button>

                    @if ($package->certificate)
                        <a href="{{ route('admin.package.download-certificate-template', [
                            'course_id' => $package->course_id, 
                            'package_id' => $package->id
                        ]) }}"
                           class="btn btn-info pull-right"
                           style="margin-right: 10px">
                            Download
                        </a>
                    @endif

                    <div id="editor">{{ $certificate }}</div>

                    <input name="template" id="template" type="hidden">
                </form>
            </div>
        </div>

        <div id="previewModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div id="preview-container">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>

@stop

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js" type="text/javascript" charset="utf-8"></script>
    <script>

        let editor = ace.edit("editor");
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode("ace/mode/html");


        /* $.ajax({
            type: 'GET',
            url: '/course/' + '{{ $course->id }}' + '/certificate',
            success: function(response) {
                editor.session.setValue(response.template);
            }
        }); */


         // set value to content
        let content = document.getElementById('template');
        content.value = editor.getValue();

        editor.session.on('change', function(delta) {
            content.value=editor.getValue();
        });


        $(".previewBtn").click(function(){
            $("#preview-container").html(editor.getValue());
        });
    </script>
@stop