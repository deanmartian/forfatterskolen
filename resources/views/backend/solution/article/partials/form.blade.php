<form method="POST" action="{{Request::is('solution/*/article/*/edit')
? route('admin.solution-article.update', ['solution_id' => $solution->id, 'id' => $article['id']])
: route('admin.solution-article.store', $solution_id)}}">

    @if(Request::is('solution/*/article/*/edit'))
        {{ method_field('PUT') }}
    @endif
    {{csrf_field()}}

    <div class="col-sm-12">
        @if(Request::is('solution/*/article/*/edit'))
            <h3>{{ trans('site.edit') }} <em>{{$article['title']}}</em></h3>
        @else
            <h3>{{ trans('site.add-new-article') }}</h3>
        @endif
    </div>

        <div class="col-sm-12 col-md-8">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label>{{ trans('site.title') }}</label>
                        <input type="text" class="form-control" name="title"
                               value="{{ old('title') ? old('title') : $article['title'] }}" required>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('site.details') }}</label>
                        <textarea name="details" id="editor" cols="30" rows="10" class="form-control"
                                  >{{ old('details') ? old('details') : $article['details'] }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-4">
            <div class="panel panel-default">
                <div class="panel-body">
                    @if(Request::is('solution/*/article/*/edit'))
                        <button type="submit" class="btn btn-primary">{{ trans('site.update-article') }}</button>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteSolutionArticleModal">{{ trans('site.delete-article') }}</button>
                    @else
                        <button type="submit" class="btn btn-primary btn-block btn-lg">{{ trans('site.create-article') }}</button>
                    @endif
                </div>
            </div>

            @if ( $errors->any() )
                <div class="alert alert-danger no-bottom-margin">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
</form>

@if(Request::is('solution/*/article/*/edit'))
    @include('backend.solution.article.partials.delete')
@endif

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script>
        // tinymce
        var editor_config = {
            path_absolute: "{{ URL::to('/') }}",
            height: '35em',
            selector: '#editor',
            plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern'],
            toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript superscript | forecolor backcolor | link | alignleft aligncenter alignright ' +
            'alignjustify  | removeformat',
            toolbar2: 'undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media code | print fullscreen',
            relative_urls: false,
            file_browser_callback : function(field_name, url, type, win) {
                var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

                var cmsURL = editor_config.path_absolute + '/laravel-filemanager?field_name=' + field_name;
                if (type == 'image') {
                    cmsURL = cmsURL + '&type=Images';
                } else {
                    cmsURL = cmsURL + '&type=Files';
                }

                tinyMCE.activeEditor.windowManager.open({
                    file : cmsURL,
                    title : 'Filemanager',
                    width : x * 0.8,
                    height : y * 0.8,
                    resizable : 'yes',
                    close_previous : 'no'
                });
            }
        };
        tinymce.init(editor_config);
    </script>
@stop