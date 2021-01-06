<form method="POST" action="@if(Request::is('poem/*/edit')){{route('admin.poem.update', $poem['id'])}}@else{{route('admin.poem.store')}}@endif"
      enctype="multipart/form-data" onsubmit="disableSubmit(this)">
    {{ csrf_field() }}
    @if(Request::is('poem/*/edit'))
        {{ method_field('PUT') }}
    @endif

    <div class="col-sm-12">
        @if(Request::is('poem/*/edit'))
            <h3>{{ trans('site.edit') }} <em>{{$poem['title']}}</em></h3>
        @else
            <h3>Add Poem</h3>
        @endif
    </div>

    <div class="col-sm-12 col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>{{ trans('site.title') }}</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title', $poem['title']) }}" required>
                </div>
                <div class="form-group">
                    <label>Poem</label>
                    <textarea name="poem" cols="30" rows="10" class="form-control tinymce">{{ old('poem', $poem['poem']) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="panel panel-default">
            <div class="panel-body">

                <div class="form-group">
                    <label>{{ trans('site.author-image') }}</label>
                    <input type="file" name="author_image" accept="image/*" class="form-control">
                    <p class="text-center">
                        <small class="text-muted">270*308</small>
                        <br>
                        <small class="text-muted">
                            <a href="{{ asset($poem['author_image']) }}" target="_blank">
                                {{ \App\Http\AdminHelpers::extractFileName($poem['author_image']) }}
                            </a>
                        </small>
                    </p>
                </div>

                <div class="form-group">
                    <label>Author Name</label>
                    <input type="text" name="author" class="form-control" value="{{ old('author', $poem['author']) }}" required>
                </div>

                @if(Request::is('poem/*/edit'))
                    <button type="submit" class="btn btn-primary">Update Poem</button>
                    <button type="button" class="btn btn-danger deletePoemBtn" data-toggle="modal" data-target="#deletePoemModal"
                            data-action="{{ route('admin.poem.destroy', $poem['id']) }}">Delete Poem</button>
                @else
                    <button type="submit" class="btn btn-primary btn-block btn-lg">Create Poem</button>
                @endif
            </div>
        </div>
    </div>
</form>

@if(Request::is('poem/*/edit'))
    @include('backend.poem.partials.delete')
@endif

@section('scripts')
    <script>
        $(".deletePoemBtn").click(function(){
            let action        = $(this).data('action'),
                modal           = $("#deletePoemModal"),
                form          = modal.find('form');
            form.attr('action', action);
        });
    </script>
@stop