@extends('backend.layout')

@section('title')
    <title>{{ $solution->title }} Articles &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> {{ $solution->title }} Articles</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a href="{{ route('admin.solution.index') }}" class="btn btn-default margin-top">< {{ trans('site.back') }}</a>
        <a href="{{ route('admin.solution-article.create', $solution->id) }}" class="btn btn-success margin-top">{{ trans('site.add-article') }}</a>
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ trans('site.id') }}</th>
                    <th>{{ trans_choice('site.articles', 1) }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($articles as $article)
                        <tr>
                            <td>{{ $article->id }}</td>
                            <td>{{ $article->title }}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.solution-article.edit',
                                ['solution_id' => $solution->id, 'id' => $article->id]) }}">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop