@extends('backend.layout')

@section('page_title'){{ $order->order_number }} &rsaquo; Admin@endsection

@section('content')
<div class="container-fluid" style="max-width:900px;padding:2rem;">
    <a href="{{ route('admin.shop-orders') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Tilbake</a>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>{{ $order->order_number }}</h2>
        <div>
            {!! $order->status_badge !!}
            {!! $order->fulfillment_badge !!}
        </div>
    </div>

    <div class="row">
        {{-- Kunde --}}
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><strong>Kunde</strong></div>
                <div class="card-body">
                    <p><strong>{{ $order->customer_name }}</strong></p>
                    <p>{{ $order->customer_email }}</p>
                    @if($order->customer_phone)<p>{{ $order->customer_phone }}</p>@endif
                    @if($order->shipping_address)
                        <hr>
                        <p class="mb-0">{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_zip }} {{ $order->shipping_city }}, {{ $order->shipping_country }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Betaling --}}
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><strong>Betaling</strong></div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><td class="text-muted">Metode</td><td>{{ $order->payment_method ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Referanse</td><td>{{ $order->payment_reference ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Betalt</td><td>{{ $order->paid_at?->format('d.m.Y H:i') ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Fiken</td><td>{{ $order->fiken_invoice_number ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Produkter --}}
    <div class="card mb-4">
        <div class="card-header"><strong>Produkter</strong></div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr><th>Bok</th><th>Format</th><th>Antall</th><th class="text-end">Pris</th></tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item['title'] ?? 'Ukjent' }}</td>
                            <td>{{ ucfirst($item['format'] ?? '-') }}</td>
                            <td>{{ $item['quantity'] ?? 1 }}</td>
                            <td class="text-end">kr {{ number_format($item['price'] ?? 0, 0, ',', ' ') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="3" class="text-end text-muted">Delsum</td><td class="text-end">kr {{ number_format($order->subtotal, 0, ',', ' ') }}</td></tr>
                    <tr><td colspan="3" class="text-end text-muted">Frakt</td><td class="text-end">kr {{ number_format($order->shipping_cost, 0, ',', ' ') }}</td></tr>
                    <tr><td colspan="3" class="text-end"><strong>Totalt</strong></td><td class="text-end"><strong>kr {{ number_format($order->total, 0, ',', ' ') }}</strong></td></tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Handlinger --}}
    <div class="row">
        @if($order->isPaid() && $order->fulfillment_status === 'pending')
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header"><strong>Merk som sendt</strong></div>
                    <div class="card-body">
                        <form action="{{ route('admin.shop-orders.ship', $order) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label>Sporingsnummer (valgfritt)</label>
                                <input type="text" name="tracking_number" class="form-control" placeholder="f.eks. Posten-sporingsnr">
                            </div>
                            <button type="submit" class="btn btn-success">Merk som sendt</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if($order->fulfillment_status === 'shipped')
            <div class="col-md-6">
                <div class="alert alert-success">
                    Sendt {{ $order->shipped_at?->format('d.m.Y H:i') }}
                    @if($order->tracking_number)
                        — Sporing: {{ $order->tracking_number }}
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Admin-notater --}}
    <div class="card mb-4">
        <div class="card-header"><strong>Notater</strong></div>
        <div class="card-body">
            <p>{{ $order->admin_notes ?? 'Ingen notater.' }}</p>
        </div>
    </div>
</div>
@endsection
