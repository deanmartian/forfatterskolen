@extends($layout)

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Invoice</h3>
        <a href="{{ $backRoute }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="col-sm-12 margin-top">
        <div class="panel panel-default" style="border-top: 0">
            <div class="panel-body">
                <button class="btn btn-primary margin-bottom invoiceBtn" data-toggle="modal"
                        data-target="#uploadInvoiceModal">
                    Upload Invoice
                </button>

                <table class="table">
                    <thead>
                    <tr>
                        <th>File</th>
                        <th>Note</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td>
                                <a href="{{ $invoice->invoice_file }}" download="">
                                    <i class="fa fa-download"></i>
                                </a>
                                <a href="{{ $invoice->invoice_file }}">
                                    {{ $invoice->filename }}
                                </a>
                            </td>
                            <td>
                                {!! $invoice->notes !!}
                            </td>
                            <td>
                                <button class="btn btn-primary btn-xs invoiceBtn" data-toggle="modal"
                                        data-target="#uploadInvoiceModal"
                                        data-type="cover" data-id="{{ $invoice->id }}"
                                        data-record="{{ json_encode($invoice) }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-xs deleteInvoiceBtn" data-toggle="modal"
                                        data-target="#deleteInvoiceModal"
                                        data-action="{{ route($deleteInvoiceRoute, [$invoice->project_id, $invoice->id]) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <button class="btn btn-primary margin-bottom manualInvoiceBtn" data-toggle="modal"
                        data-target="#uploadManualInvoiceModal">
                    Add Faktura
                </button>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Faktura</th>
                        <th>Amount</th>
                        <th>Assigned to</th>
                        <th>Date</th>
                        <th>Note</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($manualInvoices as $manualInvoice)
                        <tr>
                            <td>
                                {{ $manualInvoice->invoice }}
                            </td>
                            <td>
                                {{ $manualInvoice->amount_formatted }}
                            </td>
                            <td>
                                {{ $manualInvoice->assigned_to_name }}
                            </td>
                            <td>
                                {{ $manualInvoice->date }}
                            </td>
                            <td>
                                {{ $manualInvoice->note }}
                            </td>
                            <td>
                                <button class="btn btn-primary btn-xs manualInvoiceBtn" data-toggle="modal"
                                        data-target="#uploadManualInvoiceModal" data-id="{{ $manualInvoice->id }}"
                                        data-record="{{ json_encode($manualInvoice) }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-xs deleteInvoiceBtn" data-toggle="modal"
                                        data-target="#deleteInvoiceModal"
                                        data-action="{{ route($deleteManualInvoiceRoute, [$manualInvoice->project_id, $manualInvoice->id]) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <button class="btn btn-primary margin-bottom powerOfficeBtn" data-toggle="modal"
                        data-target="#powerOfficeModal">
                    Add Power Office Invoice
                </button>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Self Publishing</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($poInvoices as $invoice)
                            <tr>
                                <td>
                                    {{ $invoice->selfPublishing->title }}
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-xs powerOfficeOrderBtn" 
                                    data-action="{{ route('admin.power-office.self-publishing.view-po-order', 
                                    [$invoice->parent_id, $invoice->id]) }}" 
                                        data-target="#powerOfficeOrderModal"
                                        data-toggle="modal">
                                        View Invoice
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="uploadInvoiceModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Upload Invoice
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route($saveInvoiceRoute, $project->id) }}"
                          enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">

                        <div class="form-group">
                            <label>Invoice</label>
                            <input type="file" class="form-control" name="invoice" accept="application/pdf">
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea class="form-control" name="notes" rows="10" cols="30"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success pull-right margin-top">
                            {{ trans('site.save') }}
                        </button>

                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteInvoiceModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}

                        <p>Are you sure you want to delete this record?</p>

                        <button type="submit" class="btn btn-danger pull-right margin-top">
                            {{ trans('site.delete') }}
                        </button>

                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="uploadManualInvoiceModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Faktura
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route($saveManualInvoiceRoute, $project->id) }}"
                          enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">

                        <div class="form-group">
                            <label>Faktura</label>
                            <input type="text" class="form-control" name="invoice" required>
                        </div>

                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount">
                        </div>

                        <div class="form-group">
                            <label>
                                {{ trans('site.assign-to') }}
                            </label>
                            <select name="assigned_to" class="form-control select2" required>
                                <option value="" disabled="" selected>-- Select Assignee --</option>
                                @foreach( App\User::whereIn('role', array(1,3))->orderBy('created_at', 'desc')->get() as $editor )
                                    <option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" name="date">
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea class="form-control" name="note" rows="10" cols="30"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success pull-right margin-top">
                            {{ trans('site.save') }}
                        </button>

                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="powerOfficeModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Power Office
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action=""
                          enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        
                        <div class="form-group">
                            <label>Self Publishing</label>
                            <select name="self_publishing" class="form-control" onchange="selectSelfPublishing(this)" required>
                                <option value="">-- Select Self Publishing --</option>
                                @foreach ($selfPublishingList as $selfPublishing)
                                    <option value="{{ $selfPublishing->id }}" data-fields="{{ json_encode($selfPublishing) }}">
                                        {{ $selfPublishing->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>
                                Price
                            </label>
                            <input type="text" class="form-control" name="price" required>
                        </div>

                        <button type="submit" class="btn btn-success pull-right margin-top">
                            {{ trans('site.save') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="powerOfficeOrderModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        <em>
                            Faktura - kopi
                        </em>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="invoice-container"></div>
                    <div class="text-center loader-container" style="font-size: 50px">
                        <i class="fa fa-spinner fa-pulse"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(".invoiceBtn").click(function() {
            let id = $(this).data('id');
            let record = $(this).data('record');
            let modal = $("#uploadInvoiceModal");
            let form = modal.find("form");

            form.find('[name=id]').val('');
            if (id) {
                form.find('[name=id]').val(id);
                form.find('[name=notes]').text(record.notes);
            }
        });

        $(".deleteInvoiceBtn").click(function() {
            let modal = $("#deleteInvoiceModal");
            let form = modal.find("form");
            let action = $(this).data('action');
            let pageTitle = 'Delete Invoice';

            modal.find('.modal-title').text(pageTitle);
            form.attr('action', action);
        });

        $(".manualInvoiceBtn").click(function() {
            let id = $(this).data('id');
            let record = $(this).data('record');
            let modal = $("#uploadManualInvoiceModal");
            let form = modal.find("form");

            form.find('[name=id]').val('');
            if (id) {
                form.find('[name=id]').val(id);
                form.find('[name=invoice]').val(record.invoice);
                form.find('[name=amount]').val(record.amount);
                form.find('[name=assigned_to]').val(record.assigned_to).trigger('change');
                form.find('[name=date]').val(record.date);
                form.find('[name=note]').text(record.note);
            }
        });

        $(document).on('click', '.powerOfficeOrderBtn', function() {
            let action = $(this).data('action');
            let modal = $('#powerOfficeOrderModal');
            
            modal.find(".invoice-container").empty();
            modal.find(".loader-container").show();
            
            $.ajax({
                type:'GET',
                url: action,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: {},
                success: function(data){
                    modal.find(".invoice-container").html(data);
                    modal.find(".loader-container").hide();
                }
            });
        });

        $(document).on('click', '.downloadInvoice', function() {
            const self = $(this);
            const spinner = self.find('.fa-spinner');
            const action = self.data('action');
            self.attr('disabled', true);
            spinner.show();

            $.ajax({
                url: action,
                method: 'GET',
                xhrFields: {
                    responseType: 'blob' // Important for binary data
                },
                success: function(data, status, xhr) {
                    // Hide the loading indicator
                    spinner.hide();
                    self.removeAttr('disabled');

                    // Extract the file name from the response headers
                    var disposition = xhr.getResponseHeader('Content-Disposition');
                    var fileName = "invoice.pdf"; // Default file name

                    if (disposition && disposition.indexOf('filename=') !== -1) {
                        var matches = /filename="(.+)"/.exec(disposition);
                        if (matches != null && matches[1]) {
                            fileName = matches[1];
                        }
                    } else {
                        // Fallback to X-File-Name header
                        var headerFileName = xhr.getResponseHeader('X-File-Name');
                        if (headerFileName) {
                            fileName = headerFileName;
                        }
                    }

                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(new Blob([data]));
                    link.download = fileName;
                    link.click();
                    
                },
                error: function() {
                    // Hide the loading indicator
                    spinner.hide();
                    self.removeAttr('disabled');

                    alert('Failed to download the PDF. Please try again.');
                }
            });
        });

        function selectSelfPublishing(selectElement) {
            const modal = $("#powerOfficeModal");
            const form = modal.find('form');

            // Get the selected option
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            
            // Retrieve the data-fields attribute (which contains the JSON-encoded data)
            var fieldsData = selectedOption.getAttribute('data-fields');
            
            // Parse the JSON data to a JavaScript object
            var fields = JSON.parse(fieldsData);
            
            if (fields) {
                form.attr('action', '/power-office/self-publishing/' + fields.id + '/add-to-po');
                form.find('[name=price]').val(fields.price);
            } else {
                form.find('[name=price]').val('');
            }
        }
    </script>
@stop