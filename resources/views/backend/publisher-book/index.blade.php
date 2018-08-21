@extends('backend.layout')

@section('title')
    <title>Publisher Book &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> Publisher Book Page</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a href="{{ route('admin.publisher-book.create') }}" class="btn btn-success margin-top">Add Publisher Book</a>

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Display Order</th>
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

        <div class="pull-right">
            {{ $books->render() }}
        </div>

    </div>

    <div id="deletePublisherBookModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Publisher Book</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <p>Are you sure you want to delete this publisher book?</p>
                        <button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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