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
    </script>
@stop