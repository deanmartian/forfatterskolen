@extends('backend.layout')

@section('title')
<title>Admins &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<style>
    .badge {
        padding: 3px 8px;
        font-size: 13px;
        line-height: 1;
    }

    .badge-info {
        background-color: #6bb5b5;
        color: #fff;
    }

    .badge-warning {
        background-color: #f7be57;
        color: #666;
    }

    .badge-danger {
        background-color: #ff6060;
        color: #fff;
    }

    .lbl_table {
        display: block;
        margin-top: 4px;
        margin-left: 3px;
    }

    .field_cell {
        background: #f4f4f4;
        width: 150px;
    }

    table tr:hover {
        background-color: #f4f4f4;
    }

    .changed {
        background: antiquewhite;
    }
</style>
@endsection

@section('content')
<div class="page-toolbar">
	<h3>
        Showing {{ $fromCount }} to {{ $toCount }} of {{ $totalCount }} records
    </h3>
	
	<div class="clearfix"></div>
</div>
<div class="col-md-12">
    <div class="table-users table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Log Type</th>
                    <th>Done By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td>
                            {{ $log->id }}
                        </td>
                        <td>
                            {{ $log->formatted_date }} - 
                            {{ $log->dateHumanize }}
                        </td>
                        <td>
                            @if ($log->log_type === 'create')
                                <span class="badge badge-info">
                                    {{ $log->log_type }}
                                </span>
                                <span class="lbl_table">
                                    to {{ $log->table_name }}
                                </span>
                            @else
                                @if ($log->log_type === 'edit')
                                    <span class="badge badge-warning">
                                        {{ $log->log_type }}
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        {{ $log->log_type }}
                                    </span>
                                @endif

                                <span class="lbl_table">
                                    from {{ $log->table_name }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <strong>
                                {{ $log->user->full_name }}
                            </strong> <br>
                            <span>
                                {{ $log->user->email }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-default logDetailsBtn" data-toggle="modal" data-target="#logDetailsModal"
                            data-fields="{{ json_encode($log) }}">
                                Show
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pull-right">
            {{ $logs->render() }}
        </div>
    </div>
</div>

<div id="logDetailsModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Log Preview</h4>
		    </div>
		    <div class="modal-body" style="overflow: auto">
                <table class="table" id="info-table">
                    <thead>
                        <tr>
                            <td colspan="2">
                                Info
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

                <table class="table" id="data-table">
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
		    </div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<script>
    $(".logDetailsBtn").click(function() {
        const fields = $(this).data('fields');

        let table = $("#info-table");
        let tbody = table.find('tbody');
        tbody.empty();

        let tr = "<tr><td class='field_cell'>Type</td><td>" + fields.log_type +"</td></tr>";
        tr += "<tr><td class='field_cell'>Table</td><td>" + fields.table_name +"</td></tr>";
        tr += "<tr><td class='field_cell'>Time</td><td>" + fields.formatted_date + " - " + fields.dateHumanize +"</td></tr>";
        tr += "<tr><td class='field_cell'>Done by</td><td>" + fields.user.full_name + " - " + fields.user.email +"</td></tr>";
        tbody.append(tr);

        let tableData = $("#data-table");
        let tableDataHead = tableData.find('thead');
        let tableDataBody = tableData.find('tbody');
        tableDataHead.empty();
        tableDataBody.empty();

        let theadField = ['edit', 'delete'].includes(fields.log_type) ? 'Field' : '';
        let theadLogType = fields.log_type == 'edit' ? 'Previous' : 'Data';
        let theadTr = "<tr>";
            theadTr += "<td style='font-weight: bold;'>" + theadField +"</td>";
            theadTr += "<td style='font-weight: bold;'>" + theadLogType + "</td>";

            if (fields.log_type == 'edit')
            {
                theadTr += "<td style='font-weight: bold;'>Current</td>";
            }

            theadTr += "</tr>";

        tableDataHead.append(theadTr);

        $.ajax({
            type:'GET',
            url:'/user-activity/' + fields.id,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(response){
                let tbodyTr = "";
                $.each(fields.json_data, function(field, value) {
                    tbodyTr += "<tr>";
                    tbodyTr += "<td class='field_cell'>" + field + "</td>";
                    tbodyTr += "<td>" + value + "</td>";
                    if (fields.log_type == 'edit') {
                        let isChanged = value != response[field] ? 'changed' : '';
                        tbodyTr += "<td class='" + isChanged + "'>" + (response[field] || '') + "</td>";
                    }
                    tbodyTr += "</tr>";
                });
                tableDataBody.append(tbodyTr);
            }
        });
    });
</script>
@endsection