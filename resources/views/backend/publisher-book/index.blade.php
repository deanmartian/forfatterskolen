@extends('backend.layout')

@section('title')
    <title>Publisher Book &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> {{ trans('site.publisher-book-page') }}</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a href="{{ route('admin.publisher-book.create') }}" class="btn btn-success margin-top">{{ trans('site.add-publisher-book') }}</a>

        <div class="table-users table-responsive" style="margin-top: 20px">
            <table class="table dt-table" id="invoice-table">
                <thead>
                <tr>
                    <th>{{ trans('site.id') }}</th>
                    <th>{{ trans('site.title') }}</th>
                    <th>{{ trans('site.display-order') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($books as $book)
                    <tr>
                        <td>{{ $book->id }}</td>
                        <td>{{ $book->title }}</td>
                        <td>{{ $book->display_order }}</td>
                        <td>
                            <a href="{{ route('admin.publisher-book.edit', $book->id) }}" class="btn btn-info btn-xs">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <button class="btn btn-danger btn-xs deletePublisherBookBtn"
                                    data-toggle="modal" data-target="#deletePublisherBookModal"
                                    data-action="{{ route('admin.publisher-book.destroy', $book->id) }}"
                            ><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{--<div class="pull-right">
            {{ $books->render() }}
        </div>--}}

    </div>

    @include('backend.publisher-book.partials.delete')

@stop

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>
        $(document).ready(function(){

            $(".deletePublisherBookBtn").click(function(){
                var action        = $(this).data('action'),
                    modal           = $("#deletePublisherBookModal"),
                    form          = modal.find('form');
                form.attr('action', action);
            });
        });
    </script>
@stop