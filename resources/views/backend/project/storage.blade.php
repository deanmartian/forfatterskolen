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

                        @if(!$projectUserBook)
                            <button class="btn btn-primary btn-sm pull-right bookBtn" data-toggle="modal" 
                            data-target="#bookModal" data-action="{{ route('admin.project.storage.save-book', $projectId) }}"
                            data-title="Select Book">
                                Select Book
                            </button>
                        @endif
                    </div>
                    <div class="panel-body table-users">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>
                                        Book name
                                    </th>
                                    <th width="100"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($projectUserBook)
                                    <tr>
                                        <td>
                                            {{ $projectUserBook->title }}
                                        </td>
                                        <td>
                                            <button class="btn btn-xs btn-primary bookBtn" data-toggle="modal" 
                                            data-target="#bookModal" data-title="Edit Book" 
                                            data-record="{{ json_encode ($projectUserBook)}}"
                                            data-action="{{ route('admin.project.storage.save-book', $projectId) }}">
                                                <i class="fa fa-edit"></i>
                                            </button>

                                            <button class="btn btn-danger btn-xs deleteBtn" data-toggle="modal" 
                                            data-target="#deleteModal"
                                            data-action="{{ route('admin.project.storage.delete-book', $projectId) }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        @if($projectUserBook)
            <ul class="nav nav-tabs margin-top">
                <li @if( Request::input('tab') == 'master' || Request::input('tab') == '') class="active" @endif>
                    <a href="?tab=master">Master Data</a>
                </li>
                <li @if( Request::input('tab') == 'various' ) class="active" @endif>
                    <a href="?tab=various">Various</a>
                </li>
                <li @if( Request::input('tab') == 'inventory' ) class="active" @endif>
                    <a href="?tab=inventory">Inventory Data</a>
                <li @if( Request::input('tab') == 'distribution' ) class="active" @endif>
                    <a href="?tab=distribution">Distribution Cost</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade in active">
                    @if( Request::input('tab') == 'various')
                        @include('backend.project.partials._various')
                    @elseif( Request::input('tab') == 'inventory')
                        @include('backend.project.partials._inventory')
                    @elseif( Request::input('tab') == 'distribution')
                    @else
                        @include('backend.project.partials._master')
                    @endif
                </div>
            </div>
        @endif
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
                                @foreach ($userBooksForSale as $book)
                                    <option value="{{ $book->id }}">
                                        {{ $book->title }}
                                    </option>
                                @endforeach
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

    $("#editMasterBtn").click(function(){
        toggleButtons('master');
        toggleFields('master', 'enabled');
    });

    $("#cancelMasterBtn").click(function(){
        toggleButtons('master');
        toggleFields('master', 'disabled', true);
    });

    $("#editVariousBtn").click(function(){
        toggleButtons('various');
        toggleFields('various', 'enabled');
    });

    $("#cancelVariousBtn").click(function(){
        toggleButtons('various');
        toggleFields('various', 'disabled', true);
    });

    function toggleButtons(identifier) {

        $("#edit" + capitalizeFirstLetter(identifier) + "Btn").toggleClass('hidden');
        $(".save-" + identifier + "-container").toggleClass('hidden');
    }

    function toggleFields(identifier, attr = 'disabled', resetFields = false) {
        let panel = $("#" + identifier + "-panel");
        let fields = panel.find("input");
        let record = panel.data('record');

        if (resetFields) {
            $.each(record, function(k, v) {
                panel.find("[name='" + k + "']").val(v);
            });
        }

        $.each(fields, function(k, v) {
            if (attr === 'enabled') {
                v.removeAttribute('disabled');
            } else {
                v.setAttribute('disabled', true);
            }
        });
    }

    function capitalizeFirstLetter(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

</script>
@stop