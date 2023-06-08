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
                                <td>{{ $book->isbn }}</td>
                                <td>
                                    {{ $book->title }}
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
        <li @if( Request::input('tab') == 'sales') class="active" @endif>
            <a href="?tab=sales">Sales Report</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade in active">
            @if( Request::input('tab') == 'inventory' || Request::input('tab') == '')
                @include('backend.book-for-sale.partials._inventory')
            @elseif (Request::input('tab') == 'sales')
                @include('backend.book-for-sale.partials._sales_report')
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

@stop

@section('scripts')
<script>

    function showDetails(type, book_for_sale_id, isEdit = false) {
        let modal = $("#salesReportModal");

        modal.find(".modal-title").text('Add ' + formatText(type));
        modal.find("[name=type]").val(type);
        $("#sales-report-details").removeClass('hidden');
        console.log("show details here");

        $.ajax({
            type:'GET',
            url:'/book-for-sale/' + book_for_sale_id + '/details',
            data: { "type" : type},
            success: function(data){
                let table = $("#sales-details-table");

                $.each(data.details, function(k, record) {
                    let tr = "<tr>";
                          tr += "<td>" + record.id + "</td>";  
                        tr += "</tr>";

                    table.find('tbody').append(tr);
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