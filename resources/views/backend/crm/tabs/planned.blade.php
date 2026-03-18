@if(isset($planned) && $planned->count())
<table class="table table-striped">
    <thead>
        <tr>
            <th>Mottaker</th>
            <th>Sekvens</th>
            <th>Emne</th>
            <th>Planlagt</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($planned as $item)
        <tr>
            <td>{{ $item->email }}</td>
            <td><small>{{ $item->sequence?->name }}</small></td>
            <td>{{ $item->step?->subject }}</td>
            <td>{{ $item->scheduled_at?->format('d.m.Y H:i') }}</td>
            <td>
                <form method="POST" action="{{ route('admin.crm.planned.cancel', $item->id) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Kansellere?')">
                        <i class="fa fa-times"></i>
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $planned->links() }}
@else
<p class="text-muted">Ingen planlagte e-poster.</p>
@endif
