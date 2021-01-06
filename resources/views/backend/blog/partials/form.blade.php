<form method="POST" action="@if(Request::is('blog/*/edit')){{route('admin.blog.update', $blog['id'])}}@else{{route('admin.blog.store')}}@endif"
      enctype="multipart/form-data">
    {{ csrf_field() }}
    @if(Request::is('blog/*/edit'))
        {{ method_field('PUT') }}
    @endif

    <div class="col-sm-12">
        @if(Request::is('blog/*/edit'))
            <h3>{{ trans('site.edit') }} <em>{{$blog['title']}}</em></h3>
        @else
            <h3>{{ trans('site.add-new-blog') }}</h3>
        @endif
    </div>

    <div class="col-sm-12 col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>{{ trans('site.title') }}</label>
                    <input type="text" class="form-control" name="title" value="{{ $blog['title'] }}" required>
                </div>
                <div class="form-group">
                    <label>{{ trans('site.description') }}</label>
                    <textarea name="description" cols="30" rows="10" class="form-control tinymce">{{ $blog['description'] }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>{{ trans('site.image') }}</label>
                    <input type="file" name="image" class="form-control"
                           accept="image/*" @if(!Request::is('blog/*/edit')) required @endif>
                    <p class="text-center">
                        <small class="text-muted">560*1120</small>
                        <br>
                        <small class="text-muted">
                            <a href="{{ asset($blog['image']) }}" target="_blank">
                                {{ \App\Http\AdminHelpers::extractFileName($blog['image']) }}
                            </a>
                        </small>
                    </p>
                </div>

                <div class="form-group">
                    <label>{{ trans('site.author-name') }}</label>
                    <input type="text" name="author_name" class="form-control"
                    value="{{ $blog['author_name'] }}">
                </div>

                <div class="form-group">
                    <label>{{ trans('site.author-image') }}</label>
                    <input type="file" name="author_image" class="form-control"
                           accept="image/*">
                    <p class="text-center">
                        <small class="text-muted">
                            <a href="{{ asset($blog['author_image']) }}" target="_blank">
                                {{ \App\Http\AdminHelpers::extractFileName($blog['author_image']) }}
                            </a>
                        </small>
                    </p>
                </div>

                <div class="form-group">
                    <label>Schedule Date</label>
                    <input type="date" class="form-control" name="schedule"
                       @if($blog['schedule']) value="{{ date_format(date_create($blog['schedule']), 'Y-m-d') }}" @endif>
                </div>

                <div class="form-group">
                    <label style="display: block;">{{ trans('site.status') }}</label>
                    <input type="checkbox" data-toggle="toggle" data-on="Active" data-off="Draft" name="status"
                           {{ Request::is('blog/*/edit') ? ($blog['status'] ? 'checked' : '') : 'checked' }}>
                </div>

                @if(Request::is('blog/*/edit'))
                    <button type="submit" class="btn btn-primary">{{ trans('site.update-blog') }}</button>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteBlogModal">{{ trans('site.delete-blog') }}</button>
                @else
                    <button type="submit" class="btn btn-primary btn-block btn-lg">{{ trans('site.create-blog') }}</button>
                @endif
            </div>
        </div>
    </div>
</form>

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
@stop