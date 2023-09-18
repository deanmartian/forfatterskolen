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
                    <button class="btn btn-success btn-xs saveQuoteBtn" data-toggle="modal" data-target="#saveQuoteModal"
                    data-action="{{ route('learner.self-publishing.save-quote', $order->id) }}">
                        Save Quote
                    </button>
                    
                    <button class="btn btn-danger btn-xs deleteOrderBtn" data-toggle="modal" 
                    data-target="#deleteOrderModal" data-action="{{ route('learner.self-publishing.delete-order', $order->id) }}">
                        Delete
                    </button>
                </td>
            </tr>
        @endforeach
        @if($orders->count())
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
        @endif
    </tbody>
</table>

@if($orders->count())
    <a href="{{ route('learner.self-publishing.checkout') }}" class="btn btn-dark pull-right" style="margin-top: 20px">
        Proceed Checkout
    </a>
@endif

<div class="clearfix"></div>