@extends('backend.layout')

@section('title')
    <title>Bokbestillinger &rsaquo; Admin</title>
@stop

@section('content')
<div class="container-fluid" style="max-width:1200px;padding:2rem;">
    <h2>📦 Indiemoon — Bokbestillinger</h2>

    {{-- Stats --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h3>{{ $stats['total_orders'] }}</h3>
                <small class="text-muted">Totalt bestillinger</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h3>{{ $stats['pending_payment'] }}</h3>
                <small class="text-muted">Venter betaling</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h3>{{ $stats['paid_not_shipped'] }}</h3>
                <small class="text-muted">Betalt, ikke sendt</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h3>kr {{ number_format($stats['total_revenue'], 0, ',', ' ') }}</h3>
                <small class="text-muted">Total omsetning</small>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="row mb-3 g-2">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Søk navn, e-post, ordrenr..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-control">
                <option value="">Alle statuser</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Venter</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Betalt</option>
                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refundert</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="fulfillment" class="form-control">
                <option value="">Alle leveringer</option>
                <option value="pending" {{ request('fulfillment') == 'pending' ? 'selected' : '' }}>Ikke sendt</option>
                <option value="shipped" {{ request('fulfillment') == 'shipped' ? 'selected' : '' }}>Sendt</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-secondary w-100">Filtrer</button>
        </div>
    </form>

    {{-- Bestillingstabell --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Ordrenr</th>
                        <th>Kunde</th>
                        <th>Totalt</th>
                        <th>Betaling</th>
                        <th>Levering</th>
                        <th>Dato</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>
                                {{ $order->customer_name }}<br>
                                <small class="text-muted">{{ $order->customer_email }}</small>
                            </td>
                            <td>kr {{ number_format($order->total, 0, ',', ' ') }}</td>
                            <td>{!! $order->status_badge !!}</td>
                            <td>{!! $order->fulfillment_badge !!}</td>
                            <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.shop-orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">Vis</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Ingen bestillinger ennå. Når noen bestiller i nettbutikken vises de her.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $orders->appends(request()->query())->links() }}
</div>
@endsection
