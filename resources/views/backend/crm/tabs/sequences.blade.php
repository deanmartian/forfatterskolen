@if(isset($sequences) && $sequences->count())
<table class="table table-striped">
    <thead>
        <tr>
            <th>Navn</th>
            <th>Trigger</th>
            <th>Fra-type</th>
            <th>Steg</th>
            <th>Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($sequences as $sequence)
        <tr>
            <td><a href="{{ route('admin.crm.sequences.show', $sequence->id) }}">{{ $sequence->name }}</a></td>
            <td><code>{{ $sequence->trigger_event }}</code></td>
            <td>{{ $sequence->from_type }}</td>
            <td>{{ $sequence->steps_count }}</td>
            <td>
                <form method="POST" action="{{ route('admin.crm.sequences.toggle', $sequence->id) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $sequence->is_active ? 'btn-success' : 'btn-secondary' }}">
                        {{ $sequence->is_active ? 'Aktiv' : 'Inaktiv' }}
                    </button>
                </form>
            </td>
            <td>
                <a href="{{ route('admin.crm.sequences.show', $sequence->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-pencil"></i> Rediger
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p class="text-muted">Ingen sekvenser opprettet ennå. Kjør <code>php artisan email:seed-sequences</code> for å opprette standardsekvensene.</p>
@endif
