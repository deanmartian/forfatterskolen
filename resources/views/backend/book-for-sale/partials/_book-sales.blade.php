<div class="sp-card">
    <div class="sp-card__header">
        <h2>
            <i class="fa fa-shopping-cart" style="color:var(--brand-primary);margin-right:6px"></i>
            {{ trans('site.author-portal.book-sales') }}
        </h2>
    </div>
    <div class="sp-card__body">
        <div class="table-responsive">
            <table class="sp-table dt-table">
                <thead>
                    <tr>
                        <th>Dato</th>
                        <th>Kundenavn</th>
                        <th>Antall</th>
                        <th>Pris</th>
                        <th>Rabatt</th>
                        <th>Beløp</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projectBook->sales as $bookSale)
                        <tr>
                            <td>{{ $bookSale->date }}</td>
                            <td>{{ $bookSale->customer_name }}</td>
                            <td>{{ $bookSale->quantity }}</td>
                            <td>{{ $bookSale->price_formatted }}</td>
                            <td>{{ $bookSale->discount_formatted }}</td>
                            <td>{{ $bookSale->total_amount_formatted }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
