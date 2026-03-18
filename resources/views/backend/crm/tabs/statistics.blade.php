@if(isset($stats))
<div class="row">
    <!-- Kontakter -->
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header"><strong>Kontakter</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><td>Totalt</td><td class="text-right"><strong>{{ number_format($stats['total_contacts']) }}</strong></td></tr>
                    <tr><td>Aktive</td><td class="text-right text-success">{{ number_format($stats['active_contacts']) }}</td></tr>
                    <tr><td>Avmeldte</td><td class="text-right text-danger">{{ number_format($stats['unsubscribed']) }}</td></tr>
                    <tr><td>Bouncet</td><td class="text-right text-muted">{{ number_format($stats['bounced']) }}</td></tr>
                </table>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><strong>Kontakter per kilde</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    @foreach($stats['contacts_by_source'] as $source => $count)
                    <tr><td>{{ $source }}</td><td class="text-right">{{ number_format($count) }}</td></tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    <!-- E-poster -->
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header"><strong>E-poster sendt</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><td>I dag</td><td class="text-right"><strong>{{ number_format($stats['emails_sent_today']) }}</strong></td></tr>
                    <tr><td>Siste 7 dager</td><td class="text-right">{{ number_format($stats['emails_sent_week']) }}</td></tr>
                    <tr><td>Siste 30 dager</td><td class="text-right">{{ number_format($stats['emails_sent_month']) }}</td></tr>
                    <tr><td>Ventende</td><td class="text-right text-warning">{{ number_format($stats['emails_pending']) }}</td></tr>
                    <tr><td>Kansellerte</td><td class="text-right text-muted">{{ number_format($stats['emails_cancelled']) }}</td></tr>
                </table>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><strong>Sekvenser</strong></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Navn</th><th>Steg</th><th>Aktiv</th></tr></thead>
                    <tbody>
                    @foreach($stats['sequences'] as $seq)
                    <tr>
                        <td>{{ $seq->name }}</td>
                        <td>{{ $seq->steps_count }}</td>
                        <td>{!! $seq->is_active ? '<span class="badge badge-success">Ja</span>' : '<span class="badge badge-secondary">Nei</span>' !!}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><strong>Nyhetsbrev</strong></div>
            <div class="card-body">
                <p>{{ number_format($stats['newsletters_sent']) }} nyhetsbrev sendt totalt</p>
                <a href="{{ route('admin.newsletter.index') }}" class="btn btn-sm btn-outline-primary">Se alle nyhetsbrev</a>
            </div>
        </div>
    </div>
</div>
@endif
