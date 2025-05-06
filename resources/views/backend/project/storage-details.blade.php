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

        #sales-details-table {
            width: 100% !important;
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
        <h3><i class="fa fa-file-text-o"></i> Storage Details</h3>
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
                                <tr>
                                    <td>
                                        {{ $projectUserBook->value }}
                                    </td>
                                    <td>
                                        {{ $projectBook->book_name ?? '' }}
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-xs deleteBtn" data-toggle="modal" 
                                        data-target="#deleteModal"
                                        data-action="{{ route($deleteBookRoute, $projectUserBook->id) }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
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
                </li>
                <li @if( Request::input('tab') == 'distribution' ) class="active" @endif>
                    <a href="?tab=distribution">Distribution Cost</a>
                </li>
                <li @if( Request::input('tab') == 'sales' ) class="active" @endif>
                    <a href="?tab=sales">Inventory Sales</a>
                </li>
                <li @if( Request::input('tab') == 'sales-report' ) class="active" @endif>
                    <a href="?tab=sales-report">Sales Report</a>
                </li>
                <li @if( Request::input('tab') == 'storage-cost' ) class="active" @endif>
                    <a href="?tab=storage-cost">Book Sales - Storage Cost</a>
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
                    @elseif( Request::input('tab') == 'sales')
                        @include('backend.project.partials._sales')
                    @elseif( Request::input('tab') == 'sales-report')
                        @include('backend.project.partials._sales_report')
                    @elseif( Request::input('tab') == 'storage-cost')
                        @include('backend.project.partials._storage_cost')
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
                            {{-- <input type="text" class="form-control" name="service" required> --}}
                            <select name="service" class="form-control" required>
                                <option value="">- Select Service -</option>
                                @foreach (AdminHelpers::distributionServices() as $service)
                                    <option value="{{ $service['label'] }}" data-number="{{ $service['number'] }}">
                                        {{ $service['value'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
    
                        <div class="form-group">
                            <label>Number</label>
                            <input type="number" class="form-control" name="number" step=".01" required>
                        </div>
    
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount" step=".01" required>
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
                        <input type="hidden" name="project_registration_id" value="{{ $registration_id }}">
    
                        <div class="form-group">
                            <label>Book</label>
                            <input type="text" class="form-control" value="{{ $projectBook->book_name }}" disabled>
                            <input type="hidden" name="project_book_id" value="{{ $projectBook->id }}">
                        </div>
    
                        <div class="form-group">
                            <label>
                                Customer Name
                            </label>
                            <input type="text" class="form-control" name="customer_name" required>
                        </div>
                        {{-- <div class="form-group">
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
                        </div> --}}
    
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" class="form-control" name="quantity" required>
                        </div>

                        <div class="form-group">
                            <label>
                                Price
                            </label>
                            <input type="number" class="form-control" name="full_price" required>
                        </div>

                        <div class="form-group">
                            <label>
                                Discount
                            </label>
                            <input type="number" class="form-control" name="discount">
                        </div>
    
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="text" class="form-control" name="amount">
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

    <div id="importBookSalesModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Import Book sales</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route($importBookSaleRoute, $projectBook->id) }}" 
                        enctype="multipart/form-data"
                        onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">
                        <input type="hidden" name="project_registration_id" value="{{ $registration_id }}">
    
                        <div class="form-group">
                            <input type="file" name="book_sale" class="form-control"
                            accept=".xls, .xlsx, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" 
                            required>
                        </div>
    
                        <button class="btn btn-primary pull-right" type="submit">
                            {{ trans('site.submit') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end importBookSalesModal -->

        <div id="inventorySalesModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Sales</h4>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route($saveStorageSaleRoute, $projectUserBook->id) }}" 
                            onsubmit="disableSubmit(this)">
                            {{ csrf_field() }}
                            <input type="hidden" name="id">
        
                            <div class="form-group">
                                <label>Book</label>
                                <input type="text" class="form-control" value="{{ $projectBook->book_name }}" disabled>
                            </div>
        
                            <div class="form-group">
                                <label>Sale Type</label>
                                <select name="type" class="form-control" required>
                                    <option value="" disabled selected>
                                        - Select Sale Type-
                                    </option>
                                    @foreach (AdminHelpers::inventorySalesType() as $invSale)
                                        <option value="{{ $invSale['label'] }}">
                                            {{ $invSale['value'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
        
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="number" class="form-control" name="value" required>
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
        </div> <!-- end inventorySaleModal -->

        <div id="salesReportModal" class="modal fade" role="dialog" data-backdrop="static">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route($saveStorageSaleRoute, $projectUserBook->id) }}" 
                            onsubmit="disableSubmit(this)">
                            @csrf
                            <input type="hidden" name="id">
                            <input type="hidden" name="type">
        
                            <div class="form-group">
                                <label>Value</label>
                                <input type="number" class="form-control" name="value" step="0.01" required>
                            </div>
        
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" class="form-control" name="date" required>
                            </div>
        
                            <button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div> <!-- end salesReportModal -->

        <div id="sendStorageCostModal" class="modal fade" role="dialog" data-backdrop="static">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">
                            Send Storage Cost
                        </h4>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="" onsubmit="disableSubmit(this)">
                            {{csrf_field()}}
                            @php
                                $storageCostEmailTemplate = AdminHelpers::emailTemplate('Storage Cost Payout');
                            @endphp
                            <div class="form-group">
                                <label>Quarter</label> <br>
                                <div style="display: inline-block">
                                    @foreach([1, 2, 3, 4] as $q)
                                        <label>Q{{ $q }}:
                                            <input type="checkbox" name="quarters[{{ $q }}]" class="quarter-checkbox" 
                                            data-quarter="{{ $q }}" style="margin-right: 5px">
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" class="form-control" name="subject" 
                                value="{{ $storageCostEmailTemplate->subject }}" required>
                            </div>

                            <div class="form-group">
                                <label>From Email</label>
                                <input type="text" class="form-control" name="from_email"
                                value="{{ $storageCostEmailTemplate->from_email }}" required>
                            </div>

                            <div class="form-group">
                                <label>Message</label>
                                <textarea name="message" cols="30" rows="10" 
						            class="form-control tinymce" required>{!! $storageCostEmailTemplate->email_content !!}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary pull-right">{{ trans('site.submit') }}</button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="payoutHistoryModal" class="modal fade" role="dialog" data-backdrop="static">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">
                            Payout History
                        </h4>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                              <tr>
                                <th>Year</th>
                                <th>Quarter</th>
                                <th>Amount</th>
                                <th>Date</th>
                              </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
@stop

@section('scripts')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
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

    $("#distributionsModal").find("[name=service]").change(function() {
        let selectedOption = $(this).find("option:selected");
        let dataNumber = selectedOption.data("number"); // Get the data-number value
    
        $("#distributionsModal").find('[name=nr]').val(dataNumber);
    });

    $(document).on('click', '.bookSalesBtn', function() {
        let modal = $("#bookSalesModal");
        let record = $(this).data('record');
        modal.find('[name=id]').val('');
        //modal.find('[name=project_book_id]').val('');
        modal.find('[name=customer_name]').val('');
        modal.find('[name=quantity]').val('');
        modal.find('[name=full_price]').val('');
        modal.find('[name=discount]').val('');
        modal.find('[name=amount]').val('');
        modal.find('[name=date]').val('');

        if (record) {
            modal.find('[name=id]').val(record.id);
            //modal.find('[name=project_book_id]').val(record.project_book_id);
            modal.find('[name=customer_name]').val(record.customer_name);
            modal.find('[name=quantity]').val(record.quantity);
            modal.find('[name=full_price]').val(record.full_price);
            modal.find('[name=discount]').val(record.discount);
            modal.find('[name=amount]').val(record.amount);
            modal.find('[name=date]').val(record.date);
        }
    });

    $(".inventorySalesBtn").click(function() {
        let modal = $("#inventorySalesModal");
        let record = $(this).data('record');
        console.log(record);
        modal.find('[name=id]').val('');
        modal.find('[name=type]').val('');
        modal.find('[name=value]').val('');
        modal.find('[name=date]').val('');

        if (record) {
            modal.find('[name=id]').val(record.id);
            modal.find('[name=type]').val(record.type);
            modal.find('[name=value]').val(record.value);
            modal.find('[name=date]').val(record.date);
        }
    });

    $(".sendStorageCostBtn").click(function () {
        let modal = $("#sendStorageCostModal");
        let action = $(this).data('action');

        modal.find("form").attr('action', action);

        // Scope only to the current <td>
        let td = $(this).closest('td');

        td.find('.hidden-quarter').each(function () {
            const quarter = $(this).attr('name').split('_')[1];
            const value = $(this).val();

            const checkbox = modal.find(`.quarter-checkbox[data-quarter="${quarter}"]`);
            if (checkbox.length) {
                checkbox.prop('checked', value === "1");
            }
        });
    });


    let salesDetailsTable = $("#sales-details-table").DataTable({
            columnDefs: [
                {
                    targets: [0], // Index of the hidden column
                    visible: false, // Hide the column
                    orderable: true, // Enable sorting on the column
                    render: function(data, type, row) {
                        return data; // Return the hidden data for sorting
                    },
                    type: 'numeric', // Specify the sorting type (e.g., 'string', 'numeric', 'date')
                }
            ]
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

    function showDetails(type, book_for_sale_id, record = null) {
        fillSalesReportModalFields(type, record);

        $("#sales-report-details").removeClass('hidden');
        $("#sales-report-details").find('.report-title').text(formatText(type));
        $("[name=hidden_type]").val(type);
        $("[name=hidden_book_id]").val(book_for_sale_id);

        if (!record) {
            getDetails(type, book_for_sale_id);
        }
    }

    function fillSalesReportModalFields(type, record) {
        let modal = $("#salesReportModal");

        modal.find(".modal-title").text('Add ' + formatText(type));
        modal.find("[name=id]").val('');
        modal.find("[name=type]").val(type);
        modal.find("[name=value]").val('');
        modal.find("[name=date]").val('');

        if (record) {
            modal.find(".modal-title").text('Edit ' + formatText(type));
            modal.find("[name=id]").val(record.id);
            modal.find("[name=value]").val(record.value);
            modal.find("[name=date]").val(record.date);
        }
    }

    function showDeleteSalesModal(id) {
        let modal = $("#deleteModal");
        let action = '/project/storage/' + id + '/delete-sales';
        modal.find("form").attr('action', action);
    }

    function getDetails(type, project_book_id) {
        console.log(type);
        console.log(project_book_id);

        $.ajax({
            type:'GET',
            url:'/project/book/' + project_book_id + '/storage/sales-details',
            data: { "type" : type},
            success: function(data){
                console.log(data);
                salesDetailsTable.clear().draw();

                $.each(data.details, function(k, record) {
                    salesDetailsTable.row.add([
                            record.id,
                            record.value,
                            record.date,
                            "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#salesReportModal' onclick='showDetails(\"" + type + "\", " 
                                + project_book_id + ", "+JSON.stringify(record)+")'>"
                                + "<i class='fa fa-edit'></i>"
                            + "</button>"
                            + "<button class='btn btn-danger btn-xs' data-toggle='modal' data-target='#deleteModal' onclick='showDeleteSalesModal(\"" + record.id + "\")' "
                            + "style='margin-left: 5px'>"
                                +"<i class='fa fa-trash'></i>"
                                +"</button>"
                        ]).draw(false);
                });
            }
        });
    }

    function formatText(text) {
        // Replace underscores with spaces
        var formattedText = text.replace(/-/g, ' ');
        
        // Split the text into an array of words
        var words = formattedText.split(' ');
        
        // Capitalize each word
        var capitalizedWords = words.map(function(word) {
            return word.charAt(0).toUpperCase() + word.slice(1);
        });
        
        // Join the capitalized words with spaces
        var result = capitalizedWords.join(' ');
        
        return result;
    }

    function payoutToggle(self) {
        let id = $(self).attr('data-id');
        let year = $(self).attr('data-value');
        let is_checked = $(self).prop('checked');
        let check_val = is_checked ? 1 : 0;

        $.ajax({
            type:'POST',
            url:'/project/registration/' + id + '/paid-year',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { 'is_checked' : check_val, 'year': year },
            success: function(data){
                console.log(data);
            }
        });
    }

    function payoutHistoryView(self) {
        let logs = $(self).data('record');
        console.log(logs);
        const tbody = $("#payoutHistoryModal").find("tbody");
        tbody.empty(); // clear existing rows
        logs.forEach(log => {
            const row = `
                <tr>
                    <td>${log.year}</td>
                    <td>${log.quarter}</td>
                    <td>${log.amount}</td>
                    <td>${log.date ?? ''}</td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('bookSalesModal');

        if (modal) {
            // Flag to detect if amount was changed manually
            let isManual = false;

            const quantityInput = modal.querySelector('input[name="quantity"]');
            const priceInput = modal.querySelector('input[name="full_price"]');
            const discountInput = modal.querySelector('input[name="discount"]');
            const amountInput = modal.querySelector('input[name="amount"]');

            // Mark the amount as manually changed if user types in it
            amountInput.addEventListener('input', function () {
                isManual = true;
            });

            // Function to auto-update the amount (only if not manually changed)
            function updateAmount() {
                if (isManual) return; // Don't update if user edited it manually

                const quantity = parseFloat(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const discount = parseFloat(discountInput.value) || 0;

                const amount = (quantity * price) - discount;
                amountInput.value = amount.toFixed(2);
            }

            // Attach input listeners
            [quantityInput, priceInput, discountInput].forEach(input => {
                input.addEventListener('input', function () {
                    isManual = false; // Reset manual override when values change
                    updateAmount();
                });
            });
        }
    });

</script>
@stop