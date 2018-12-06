@extends('backend.layout')

@section('title')
    <title>Poem &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> Poem</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a href="{{ route('admin.poem.create') }}" class="btn btn-success margin-top">Create Poem</a>

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ trans('site.id') }}</th>
                        <th>{{ trans('site.title') }}</th>
                        <th>Author Name</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($poems as $poem)
                        <tr>
                            <td>{{ $poem->id }}</td>
                            <td>{{ $poem->title }}</td>
                            <td>{{ $poem->author }}</td>
                            <td>
                                <a href="{{ route('admin.poem.edit', $poem->id) }}" class="btn btn-info btn-xs">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <button class="btn btn-danger btn-xs deletePoemBtn"
                                        data-toggle="modal" data-target="#deletePoemModal"
                                        data-action="{{ route('admin.poem.destroy', $poem->id) }}"
                                ><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pull-right">
            {{ $poems->render() }}
        </div>

        @include('backend.poem.partials.delete')

    </div>
@stop

@section('scripts')
    <script>
        $(document).ready(function(){
            $(".deletePoemBtn").click(function(){
                let action        = $(this).data('action'),
                    modal           = $("#deletePoemModal"),
                    form          = modal.find('form');
                form.attr('action', action);
            });
        });
    </script>
@stop