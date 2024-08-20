<div class="table-users table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>{{ trans_choice('site.learners', 1) }}</th>
                <th>Self Publishing</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ( $invoices as $invoice)
                <tr>
                    <td>
                        <a href="{{ route('admin.learner.show', $invoice->user_id) }}">
                            {{ $invoice->user->full_name }}
                        </a>
                    </td>
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

<div class="pull-right">{{$invoices->appends(request()->except('page'))}}</div>