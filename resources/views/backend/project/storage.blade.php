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

                        @if(!$projectBook || ($projectBook && !$projectBook->in_storage))
                            <button class="btn btn-primary btn-sm pull-right bookBtn" data-toggle="modal" 
                            data-target="#bookModal" data-action="{{ route($saveBookRoute, $projectId) }}"
                            data-title="Select Book">
                                Select Book
                            </button>
                        @endif
                    </div>
                    <div class="panel-body table-users">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ISBN</th>
                                    <th>
                                        Book name
                                    </th>
                                    <th width="100"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($projectBook && $projectBook->in_storage)
                                    <tr>
                                        <td>
                                            @if ($project)
                                                <ul>
                                                    @foreach ($project->registrations as $registration)
                                                        @if ($registration->field === 'isbn')
                                                            <li>{{ $registration->value }}</li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $projectBook->book_name }}
                                        </td>
                                        <td>
                                            {{-- <button class="btn btn-xs btn-primary bookBtn" data-toggle="modal" 
                                            data-target="#bookModal" data-title="Edit Book" 
                                            data-record="{{ json_encode ($projectBook)}}"
                                            data-action="{{ route($saveBookRoute, $projectId) }}">
                                                <i class="fa fa-edit"></i>
                                            </button> --}}

                                            <button class="btn btn-danger btn-xs deleteBtn" data-toggle="modal" 
                                            data-target="#deleteModal"
                                            data-action="{{ route($deleteBookRoute, $projectId) }}">
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
        
        @if($projectBook && $projectBook->in_storage/* $projectUserBook */)
            @php
                $projectUserBook = $projectBook;
            @endphp
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
                    @include('backend.project.partials._distributions')
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
                                @if ($projectBook)
                                    <option value="{{ $projectBook->id }}">
                                        {{ $projectBook->book_name }}
                                    </option>
                                @endif
                                
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

    <div id="distributionsModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Distribution Cost</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" 
                        onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">
    
                        <div class="form-group">
                            <label>Nr</label>
                            <input type="text" class="form-control" name="nr" required>
                        </div>
    
                        <div class="form-group">
                            <label>Service</label>
                            <input type="text" class="form-control" name="service" required>
                        </div>
    
                        <div class="form-group">
                            <label>Number</label>
                            <input type="number" class="form-control" name="number" required>
                        </div>
    
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount" required>
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
    
                        <button class="btn btn-primary pull-right" type="submit">
                            {{ trans('site.save') }}
                        </button>
    
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($projectBook)
    <div id="bookSalesModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Book sales</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route($saveBookSaleRoute, $projectBook->project_id) }}" 
                        onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">
    
                        <div class="form-group">
                            <label>Book</label>
                            <input type="text" class="form-control" value="{{ $projectBook->book_name }}" disabled>
                            <input type="hidden" name="project_book_id" value="{{ $projectBook->id }}">
                        </div>
    
                        <div class="form-group">
                            <label>Sale Type</label>
                            <select name="sale_type" class="form-control" required>
                                <option value="" disabled selected>
                                    - Select Sale Type-
                                </option>
                                @foreach ($bookSaleTypes as $key => $saleType)
                                    <option value="{{ $key }}">
                                        {{ $saleType }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
    
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" class="form-control" name="quantity" required>
                        </div>
    
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount">
                        </div>
    
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
    
                        <button class="btn btn-primary pull-right" type="submit">
                            {{ trans('site.save') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end bookSalesModal -->
    @endif
    
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

    $(".inventory-selector").change(function() {
        var form = document.getElementById('inventory-form');
        form.submit();
    });

    $(".distributionsBtn").click(function() {
        let modal = $("#distributionsModal");
        let record = $(this).data('record');
        let action = $(this).data('action');

        modal.find("form").attr('action', action);
        modal.find('[name=id]').val('');
        modal.find('[name=nr]').val('');
        modal.find('[name=service]').val('');
        modal.find('[name=number]').val('');
        modal.find('[name=amount]').val('');
        modal.find('[name=date]').val('');

        if (record) {
            modal.find('[name=id]').val(record.id);
            modal.find('[name=nr]').val(record.nr);
            modal.find('[name=service]').val(record.service);
            modal.find('[name=number]').val(record.number);
            modal.find('[name=amount]').val(record.amount);
            modal.find('[name=date]').val(record.date);
        }
    });

    $(".bookSalesBtn").click(function() {
        let modal = $("#bookSalesModal");
        let record = $(this).data('record');
        modal.find('[name=id]').val('');
        //modal.find('[name=project_book_id]').val('');
        modal.find('[name=sale_type]').val('');
        modal.find('[name=quantity]').val('');
        modal.find('[name=amount]').val('');
        modal.find('[name=date]').val('');

        if (record) {
            modal.find('[name=id]').val(record.id);
            //modal.find('[name=project_book_id]').val(record.project_book_id);
            modal.find('[name=sale_type]').val(record.sale_type);
            modal.find('[name=quantity]').val(record.quantity);
            modal.find('[name=amount]').val(record.amount);
            modal.find('[name=date]').val(record.date);
        }
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