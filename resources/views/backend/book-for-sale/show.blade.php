@extends('backend.layout')

@section('title')
<title>Books For Sale &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<style>
    .input-group .form-control {
        width: 98%;
        border-top-right-radius: 4px !important;
        border-bottom-right-radius: 4px !important;
    }

    #sales-details-table {
        width: 100% !important;
    }
</style>
@stop

@section('content')
<div class="page-toolbar">
	<a href="{{ route('admin.book-for-sale.index') }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> Back
    </a>
	<div class="clearfix"></div>
</div>

<div class="col-md-12 margin-top">
    <div class="row">
        <div class="col-md-6">
            <div class="panel">
                <div class="panel-header" style="padding: 10px">
                    <em>
                        <b>
                            Details
                        </b>
                    </em>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ISBN</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    @if ($book->project)
                                        <ul>
                                            @foreach ($book->project->registrations as $registration)
                                                @if ($registration->field === 'isbn')
                                                    <li>{{ $registration->value }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                                <td>
                                    {{ $book->project ? $book->project->book_name : '' }}
                                </td>
                                <td>
                                    {{ $book->description }}
                                </td>
                                <td>{{ $book->price_formatted }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> <!-- end panel -->
        </div> <!-- end col-md-6 -->
    </div> <!-- end row -->

    <ul class="nav nav-tabs margin-top">
        <li @if( Request::input('tab') == 'inventory' || Request::input('tab') == '') class="active" @endif>
            <a href="?tab=inventory">Inventory</a>
        </li>
        <li @if( Request::input('tab') == 'sales-report') class="active" @endif>
            <a href="?tab=sales-report">Sales Report</a>
        </li>
        <li @if( Request::input('tab') == 'book-sales') class="active" @endif>
            <a href="?tab=book-sales">Book Sales</a>
        </li>
        <li @if( Request::input('tab') == 'distribution') class="active" @endif>
            <a href="?tab=distribution">Distribution</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade in active">
            @if( Request::input('tab') == 'inventory' || Request::input('tab') == '')
                @include('backend.book-for-sale.partials._inventory')
            @elseif (Request::input('tab') == 'sales-report')
                @include('backend.book-for-sale.partials._sales_report')
            @elseif (Request::input('tab') == 'book-sales')
                @include('backend.book-for-sale.partials._book_sales')
            @elseif (Request::input('tab') == 'distribution')
                @include('backend.book-for-sale.partials._distributions')
            @endif
        </div>
    </div>
</div>

<div id="inventoryModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Inventory</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.book-for-sale.save-inventory', $book->id) }}" 
                    onsubmit="disableSubmit(this)">
                    @csrf

                    <div class="form-group">
                        <label>Total</label>
                        <input type="number" class="form-control" name="total" 
                        value="{{ $book->inventory->total ?? '' }}">
                    </div>

                    <div class="form-group">
                        <label>Delivered</label>
                        <input type="number" class="form-control" name="delivered"
                        value="{{ $book->inventory->delivered ?? '' }}">
                    </div>

                    <div class="form-group">
                        <label>Physical Items</label>
                        <input type="number" class="form-control" name="physical_items"
                        value="{{ $book->inventory->physical_items ?? '' }}">
                    </div>

                    <div class="form-group">
                        <label>Returns</label>
                        <input type="number" class="form-control" name="returns"
                        value="{{ $book->inventory->returns ?? '' }}">
                    </div>

                    <div class="form-group">
                        <label>Balance</label>
                        <input type="number" class="form-control" name="balance"
                        value="{{ $book->inventory->balance ?? '' }}">
                    </div>

                    <div class="form-group">
                        <label>Order</label>
                        <input type="number" class="form-control" name="order"
                        value="{{ $book->inventory->order ?? '' }}">
                    </div>

                    <div class="form-group">
                        <label>Reservations</label>
                        <input type="number" class="form-control" name="reservations"
                        value="{{ $book->inventory->reservations ?? '' }}">
                    </div>
                    
                    <button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div> <!-- end inventory modal -->

<div id="salesReportModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.book-for-sale.save-sales', $book->id) }}" 
                    onsubmit="disableSubmit(this)">
                    @csrf
                    <input type="hidden" name="id">
                    <input type="hidden" name="type">

                    <div class="form-group">
                        <label>Value</label>
                        <input type="number" class="form-control" name="value" required>
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

<div id="bookSalesModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Book sales</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.learner.save-book-sales', $book->user_id) }}" 
                    onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<input type="hidden" name="id">

					<div class="form-group">
						<label>Book</label>
						<input type="text" class="form-control" value="{{ $book->title }}" disabled>
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
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
</div> <!-- end deleteModal -->

<div id="distributionsModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Distribution Cost</h4>
			</div>
			<div class="modal-body">
                <form method="POST" action="{{ route('admin.book-for-sale.save-distribution-cost', $book->id) }}" 
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

                    <button class="btn btn-primary pull-right" type="submit">
						{{ trans('site.save') }}
					</button>

                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>

@stop

@section('scripts')
<script>

    let book_id = "{{ $book->id }}";

    $(document).ready(function(){
        $(".salesReportBtn").click(function() {
            let type = $("[name=hidden_type]").val();

            fillSalesReportModalFields(type);
        });

        $(".bookSalesBtn").click(function() {
            let modal = $("#bookSalesModal");
            let record = $(this).data('record');
            modal.find('[name=id]').val('');
            modal.find('[name=book_id]').val(book_id);
            modal.find('[name=sale_type]').val('');
            modal.find('[name=quantity]').val('');
            modal.find('[name=amount]').val('');
            modal.find('[name=date]').val('');

            if (record) {
                modal.find('[name=id]').val(record.id);
                modal.find('[name=book_id]').val(record.user_book_for_sale_id);
                modal.find('[name=sale_type]').val(record.sale_type);
                modal.find('[name=quantity]').val(record.quantity);
                modal.find('[name=amount]').val(record.amount);
                modal.find('[name=date]').val(record.date);
            }
        });

        $(".deleteRecordBtn").click(function() {
            let modal = $("#deleteModal");
            let action = $(this).data('action');
            let title = $(this).data('title');
            modal.find('.modal-title').text(title);
            modal.find('form').attr('action', action);
        });

        $(".distributionsBtn").click(function() {
            let modal = $("#distributionsModal");
            let record = $(this).data('record');
            modal.find('[name=id]').val('');
            modal.find('[name=nr]').val('');
            modal.find('[name=service]').val('');
            modal.find('[name=number]').val('');
            modal.find('[name=amount]').val('');

            if (record) {
                modal.find('[name=id]').val(record.id);
                modal.find('[name=nr]').val(record.nr);
                modal.find('[name=service]').val(record.service);
                modal.find('[name=number]').val(record.number);
                modal.find('[name=amount]').val(record.amount);
            }
        });
    });

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

    function showDeleteModal(id) {
        let modal = $("#deleteModal");
        let action = '/book-for-sale/sales-report/' + id + "/delete";
        modal.find("form").attr('action', action);
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

    function getDetails(type, book_for_sale_id) {
        $.ajax({
            type:'GET',
            url:'/book-for-sale/' + book_for_sale_id + '/details',
            data: { "type" : type},
            success: function(data){

                salesDetailsTable.clear().draw();

                $.each(data.details, function(k, record) {
                    salesDetailsTable.row.add([
                            record.id,
                            record.value,
                            record.date,
                            "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#salesReportModal' onclick='showDetails(\"" + type + "\", " 
                                + book_for_sale_id + ", "+JSON.stringify(record)+")'>"
                                + "<i class='fa fa-edit'></i>"
                            + "</button>"
                            + "<button class='btn btn-danger btn-xs' data-toggle='modal' data-target='#deleteModal' onclick='showDeleteModal(\"" + record.id + "\")' "
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
    
</script>
@stop