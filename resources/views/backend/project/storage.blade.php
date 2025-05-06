@extends($layout)

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>
        .label-cell {
          font-weight: bold;
          vertical-align: middle;
          text-align: right;
          width: 100px;
        }
      </style>
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <a href="{{ $backRoute }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <h3><i class="fa fa-file-text-o"></i> Storage</h3>
    </div>
    <div class="col-sm-12 margin-top">

        <div class="row">
            <div class="col-md-6">
                <div class="panel">
                    <div class="panel-header" style="padding: 10px">
                        <em>
                            <b>
                                Book
                            </b>
                        </em>

                        @if($centralISBNs->count())
                            <button class="btn btn-primary btn-sm pull-right bookBtn" data-toggle="modal" 
                            data-target="#bookModal" data-action="{{ route($saveBookRoute, $projectId) }}"
                            data-title="Select ISBN">
                                Select ISBN
                            </button>
                        @endif
                    </div>
                    <div class="panel-body table-users">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ISBN</th>
                                    <th>Type</th>
                                    <th>
                                        Book name
                                    </th>
                                    <th width="100"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($projectCentralDistributions as $projectCentralDistribution)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.project.storage-details', 
                                                [$projectId, $projectCentralDistribution->id]) }}">
                                                {{ $projectCentralDistribution->value }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $isbnTypes[$projectCentralDistribution->type_of_isbn] ?? NULL }}
                                        </td>
                                        <td>
                                            {{ $projectBook->book_name ?? '' }}
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-xs deleteBtn" data-toggle="modal" 
                                            data-target="#deleteModal"
                                            data-action="{{ route($deleteBookRoute, $projectCentralDistribution->id) }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <div id="bookModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" onsubmit="disableSubmit(this)">
                        @csrf
                        <div class="form-group">
                            <label>Book</label>
                            <select name="user_book_for_sale_id" class="form-control" required>
                                <option value="">- Select Book -</option>
                                @foreach ($centralISBNs as $centralISBN)
                                    <option value="{{ $centralISBN->value }}">
                                        {{ $centralISBN->value }} | {{ $centralISBN->custom_type }}
                                    </option>
                                @endforeach
                                {{-- @if ($projectBook)
                                    <option value="{{ $projectBook->id }}">
                                        {{ $projectBook->book_name }}
                                    </option>
                                @endif --}}
                                
                                {{-- @foreach ($userBooksForSale as $book)
                                    <option value="{{ $book->id }}">
                                        {{ $book->title }}
                                    </option>
                                @endforeach --}}
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Record</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" onsubmit="disableSubmit(this)">
                        @csrf
                        @method('DELETE')
                        <p>
                            Are you sure you want to delete this record?
                        </p>

                        <button type="submit" class="btn btn-danger pull-right">{{ trans('site.delete') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
@stop

@section('scripts')
<script>
    $(".bookBtn").click(function() {
        let modal = $("#bookModal");
        let action = $(this).data('action');
        let title = $(this).data('title');
        let record = $(this).data('record');

        modal.find('.modal-title').text(title);
        modal.find('form').attr('action', action);
        modal.find('[name=user_book_for_sale_id]').val('');

        if (record) {
            modal.find('[name=user_book_for_sale_id]').val(record.id);
        }
    })

    $(".deleteBtn").click(function() {
        let modal = $("#deleteModal");
        let action = $(this).data('action');

        modal.find('form').attr('action', action);
    });

</script>
@stop