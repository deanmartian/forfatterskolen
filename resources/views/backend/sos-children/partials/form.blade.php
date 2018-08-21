<form method="POST" action="@if(Request::is('sos-children/*/edit')){{route('admin.sos-children.update', $document['id'])}}@else{{route('admin.sos-children.store')}}@endif">
    {{ csrf_field() }}
    @if(Request::is('sos-children/*/edit'))
        {{ method_field('PUT') }}
    @endif

    <div class="col-sm-12">
        @if(Request::is('sos-children/*/edit'))
            <h3>Edit <em>{{$document['title']}}</em></h3>
        @else
            <h3>Add New SOS Children Document Video</h3>
        @endif
    </div>

    <div class="col-sm-12 col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" class="form-control" name="title" value="{{ $document['title'] }}" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" cols="30" rows="10" class="form-control">{{ $document['description'] }}</textarea>
                </div>
                <div class="form-group">
                    <label>Video URL</label>
                    <input type="url" class="form-control" name="video_url" value="{{ $document['video_url'] }}" required>
                </div>
                @if(!count($primaryVideo) || $primaryVideo->id == $document['id'])
                    <div class="form-group">
                        <label>Is Primary Video?</label> <br>
                        <input type="checkbox" data-toggle="toggle" data-on="Yes"
                               class="for-sale-toggle" data-off="No"
                               name="is_primary">
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="panel panel-default">
            <div class="panel-body">
                @if(Request::is('sos-children/*/edit'))
                    <button type="submit" class="btn btn-primary">Update Document</button>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteDocumentModal">Delete Document</button>
                @else
                    <button type="submit" class="btn btn-primary btn-block btn-lg">Create Document</button>
                @endif
            </div>
        </div>
        {{--@if ( $errors->any() )
            <div class="alert alert-danger no-bottom-margin">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif--}}
    </div>
</form>


@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>

        var primaryVideo = '{{ count($primaryVideo) ? $primaryVideo->id : 0 }}';
        var documentId = '{{ $document['id'] }}';

        if (primaryVideo === documentId) {
            $("input[name=is_primary]").bootstrapToggle('on');
        }
    </script>
@stop