<!-- Søk -->
<form method="GET" action="{{ route('admin.crm.history') }}" class="mb-3">
    <div class="row">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Søk e-post, emne..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Søk</button>
        </div>
    </div>
</form>

@if(isset($history) && $history->count())
<table class="table table-striped">
    <thead>
        <tr>
            <th>Mottaker</th>
            <th>Sekvens</th>
            <th>Emne</th>
            <th>Sendt</th>
        </tr>
    </thead>
    <tbody>
        @foreach($history as $item)
        <tr>
            <td>{{ $item->email }}</td>
            <td><small>{{ $item->sequence?->name }}</small></td>
            <td>{{ $item->step?->subject }}</td>
            <td>{{ $item->sent_at?->format('d.m.Y H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $history->appends(request()->query())->links() }}
@else
<p class="text-muted">Ingen sendte e-poster.</p>
@endif
