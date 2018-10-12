@extends('backend.layout')

@section('title')
    <title>Blog &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> {{ trans('site.blog-page') }}</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a href="{{ route('admin.blog.create') }}" class="btn btn-success margin-top">{{ trans('site.add-blog') }}</a>

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ trans('site.id') }}</th>
                    <th>{{ trans('site.title') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($blogList as $blog)
                    <tr>
                        <td>
                            <a href="{{ route('admin.blog.edit', $blog->id) }}">
                                {{ $blog->id }}
                            </a>
                        </td>
                        <td>{{ $blog->title }}</td>
                        <td>
                            <a href="{{ route('admin.blog.edit', $blog->id) }}" class="btn btn-info btn-xs">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <button class="btn btn-danger btn-xs deleteBlogBtn"
                                    data-toggle="modal" data-target="#deleteBlogModal"
                                    data-action="{{ route('admin.blog.destroy', $blog->id) }}"
                            ><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @include('backend.blog.partials.delete')

@stop

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>
        $(document).ready(function(){

            $(".deleteBlogBtn").click(function(){
                var action        = $(this).data('action'),
                    modal           = $("#deleteBlogModal"),
                    form          = modal.find('form');
                form.attr('action', action);
            });
        });
    </script>
@stop