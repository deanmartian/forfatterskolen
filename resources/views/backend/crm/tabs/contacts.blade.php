<!-- Søk og filter -->
<form method="GET" action="{{ route('admin.crm.contacts.index') }}" class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Søk e-post, navn..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-control">
                <option value="">Alle statuser</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktiv</option>
                <option value="unsubscribed" {{ request('status') === 'unsubscribed' ? 'selected' : '' }}>Avmeldt</option>
                <option value="bounced" {{ request('status') === 'bounced' ? 'selected' : '' }}>Bouncet</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="source" class="form-control">
                <option value="">Alle kilder</option>
                <option value="webinar" {{ request('source') === 'webinar' ? 'selected' : '' }}>Webinar</option>
                <option value="facebook_lead" {{ request('source') === 'facebook_lead' ? 'selected' : '' }}>Facebook</option>
                <option value="ac_import" {{ request('source') === 'ac_import' ? 'selected' : '' }}>AC-import</option>
                <option value="existing_user" {{ request('source') === 'existing_user' ? 'selected' : '' }}>Bruker</option>
                <option value="manual" {{ request('source') === 'manual' ? 'selected' : '' }}>Manuell</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" name="tag" class="form-control" placeholder="Tag..." value="{{ request('tag') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-block">Søk</button>
        </div>
    </div>
</form>

@if(isset($contacts) && $contacts->count())
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Navn</th>
            <th>E-post</th>
            <th>Kilde</th>
            <th>Status</th>
            <th>Tags</th>
            <th>Opprettet</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($contacts as $contact)
        <tr>
            <td>{{ $contact->fullName() ?: '—' }}</td>
            <td>{{ $contact->email }}</td>
            <td><small>{{ $contact->source }}</small></td>
            <td><span class="badge badge-{{ $contact->status }}">{{ $contact->status }}</span></td>
            <td>
                @foreach($contact->tags->take(3) as $tag)
                    <span class="badge badge-secondary">{{ $tag->tag }}</span>
                @endforeach
                @if($contact->tags->count() > 3)
                    <span class="badge badge-light">+{{ $contact->tags->count() - 3 }}</span>
                @endif
            </td>
            <td><small>{{ $contact->created_at?->format('d.m.Y') }}</small></td>
            <td>
                <a href="{{ route('admin.crm.contacts.show', $contact->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-eye"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $contacts->appends(request()->query())->links() }}
@else
<p class="text-muted">Ingen kontakter funnet.</p>
@endif
