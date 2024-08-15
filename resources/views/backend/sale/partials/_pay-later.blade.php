<div class="table-users table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th>{{ trans_choice('site.packages', 1) }}</th>
            <th>{{ trans_choice('site.learners', 1) }}</th>
            <th>{{ trans('site.date-sold') }}</th>
            <th>Sent Invoice</th>
            <th>Trukket bestilling</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($payLaterOrders as $order)
                <tr>
                    <td>
                        @if (in_array($order->type, [1, 6]))
                            <a href="{{ route('admin.course.show', 
                                $order->package->course_id) }}?section=packages">
                                {{ $order->package->course->title . ' - ' .
                                $order->package->variation }}
                            </a>
                        @endif

                        @if (in_array($order->type, [2, 7, 9]))
                            {{ $order->item  }}
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.learner.show', $order->user->id) }}"
                            class="d-block">
                            {{ $order->user->full_name }}
                        </a>
                    </td>
                    <td>
                        {{ $order->created_at }}
                    </td>
                    <td>
                        <input type="checkbox" data-toggle="toggle" data-on="Yes"
                            class="is-invoice-sent-toggle" data-off="No"
                            data-id="{{$order->id}}" data-size="mini"
                             @if($order->is_invoice_sent) {{ 'checked' }} @endif>
                    </td>
                    <td>
                        <input type="checkbox" data-toggle="toggle" data-on="Yes"
                            class="is-order-withdrawn-toggle" data-off="No"
                            data-id="{{$order->id}}" data-size="mini"
                             @if($order->is_order_withdrawn) {{ 'checked' }} @endif>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pull-right">{{$payLaterOrders->appends(request()->except('page'))}}</div>
</div>

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>