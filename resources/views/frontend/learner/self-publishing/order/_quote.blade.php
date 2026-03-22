<table class="table">
    <thead>
        <tr>
            <th>Service</th>
            <th>Word Count</th>
            <th>Beløp</th>
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
                    <button class="btn btn-success btn-sm moveOrderBtn" data-bs-toggle="modal" data-bs-target="#moveOrderModal"
                    data-action="{{ route('learner.self-publishing.move-to-order', $order->id) }}">
                        Move to Order
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>