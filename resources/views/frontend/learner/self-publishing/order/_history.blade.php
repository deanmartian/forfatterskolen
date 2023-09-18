<table class="table">
    <thead>
        <tr>
            <th>Service</th>
            <th>Word Count</th>
            <th>Amount</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>
                    {{ $order->service_name }}
                </td>
                <td>
                    {{ $order->word_count }}
                </td>
                <td>
                    {{ FrontendHelpers::currencyFormat($order->price) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="float-right">
    {{ $orders->appends(request()->except('page')) }}
</div>

<div class="clearfix"></div>