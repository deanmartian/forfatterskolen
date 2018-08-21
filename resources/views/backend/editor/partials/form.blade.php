<form method="POST" action="{{Request::is('editor/*/edit')
? route('admin.editor.update', $editor['id'])
: route('admin.editor.store')}}" enctype="multipart/form-data">
    @if(Request::is('editor/*/edit'))
        {{ method_field('PUT') }}
    @endif
        {{csrf_field()}}

        <div class="col-sm-12">
            @if(Request::is('editor/*/edit'))
                <h3>Edit <em>{{$editor['name']}}</em></h3>
            @else
                <h3>Add New Editor</h3>
            @endif
        </div>

        <div class="col-sm-12 col-md-8">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" value="{{ $editor['name'] }}" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="12" id="description-ct" class="form-control" required>{{ $editor['description'] }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-4">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label id="course-image">Image</label>
                        <div class="editor-form-image image-file margin-bottom">
                            <div class="image-preview" style="background-image: url('{{$editor['editor_image']}}')" data-default="{{Auth::user()->profile_image}}" title="Select Image" data-toggle="tooltip" data-placement="bottom"></div>
                            <input type="file" accept="image/*" name="editor_image" accept="image/jpg, image/jpeg, image/png">
                        </div>
                    </div>

                    @if(Request::is('editor/*/edit'))
                        <button type="submit" class="btn btn-primary">Update Editor</button>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteEditorModal">Delete Editor</button>
                    @else
                        <button type="submit" class="btn btn-primary btn-block btn-lg">Create Editor</button>
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

@if(Request::is('editor/*/edit'))
    @include('backend.editor.partials.delete')
@endif