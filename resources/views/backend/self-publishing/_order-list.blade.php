<table class="table table-responsive">
    <thead>
        <tr>
            <th>Learner</th>
            <th>Service</th>
            <th>Word Count</th>
            <th>Amount</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>
                    <a href="{{ route('admin.learner.show', $order->user_id) }}" target="_blank">
                        {{ $order->user->full_name }}
                    </a>
                </td>
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