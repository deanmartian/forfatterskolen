<table class="table">
    <thead>
        <tr>
            <th>Service</th>
            <th>Word Count</th>
            <th>Amount</th>
            <th></th>
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
                <td>
                    <button class="btn btn-danger btn-xs deleteOrderBtn" data-toggle="modal" 
                    data-target="#deleteOrderModal" data-action="{{ route('learner.self-publishing.delete-order', $order->id) }}">
                        Delete
                    </button>
                </td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td class="text-right">
                <b>
                    Total:
                </b>
            </td>
            <td>
                {{ FrontendHelpers::currencyFormat($currentOrderTotal) }}
            </td>
            <td></td>
        </tr>
    </tbody>
</table>