@extends('backend.layout')

@section('title')
    <title>{{ $contact->fullName() ?: $contact->email }} — CRM</title>
@stop

@section('content')
<div class="container-fluid" style="padding: 20px;">
    <a href="{{ route('admin.crm.contacts.index') }}" class="btn btn-sm btn-outline-secondary mb-3">← Tilbake</a>

    <div class="row">
        <!-- Kontaktinfo -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Kontaktinfo</strong>
                    <button class="btn btn-sm btn-outline-primary" onclick="document.getElementById('editContactForm').style.display = document.getElementById('editContactForm').style.display === 'none' ? 'block' : 'none'">
                        <i class="fa fa-pencil"></i> Rediger
                    </button>
                </div>
                <div class="card-body">
                    <div id="contactInfo">
                        <p><strong>Navn:</strong> {{ $contact->fullName() ?: '—' }}</p>
                        <p><strong>E-post:</strong> {{ $contact->email }}</p>
                        <p><strong>Telefon:</strong> {{ $contact->phone ?: '—' }}</p>
                        <p><strong>Kilde:</strong> {{ $contact->source }}</p>
                        <p><strong>Status:</strong> <span class="badge badge-{{ $contact->status }}">{{ $contact->status }}</span></p>
                        <p><strong>Opprettet:</strong> {{ $contact->created_at?->format('d.m.Y H:i') }}</p>
                        @if($contact->user_id)
                            <p><strong>Bruker-ID:</strong> {{ $contact->user_id }}</p>
                        @endif
                    </div>

                    <form id="editContactForm" method="POST" action="{{ route('admin.crm.contacts.update', $contact->id) }}" style="display:none;" class="mt-2">
                        @csrf @method('PUT')
                        <div class="form-group mb-2">
                            <label><small>Fornavn</small></label>
                            <input type="text" name="first_name" class="form-control form-control-sm" value="{{ $contact->first_name }}">
                        </div>
                        <div class="form-group mb-2">
                            <label><small>Etternavn</small></label>
                            <input type="text" name="last_name" class="form-control form-control-sm" value="{{ $contact->last_name }}">
                        </div>
                        <div class="form-group mb-2">
                            <label><small>E-post</small></label>
                            <input type="email" name="email" class="form-control form-control-sm" value="{{ $contact->email }}" required>
                        </div>
                        <div class="form-group mb-2">
                            <label><small>Telefon</small></label>
                            <input type="text" name="phone" class="form-control form-control-sm" value="{{ $contact->phone }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-success">Lagre</button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('editContactForm').style.display='none'">Avbryt</button>
                    </form>

                    @if($contact->status === 'active')
                    <form method="POST" action="{{ route('admin.crm.contacts.unsubscribe', $contact->id) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Melde av kontakten?')">
                            Meld av
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Tags -->
            <div class="card mb-3">
                <div class="card-header"><strong>Tags</strong></div>
                <div class="card-body">
                    @foreach($contact->tags as $tag)
                        <span class="badge badge-secondary" style="margin: 2px;">
                            {{ $tag->tag }}
                            <form method="POST" action="{{ route('admin.crm.contacts.untag', [$contact->id, $tag->tag]) }}" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none;border:none;color:#fff;cursor:pointer;padding:0 2px;">&times;</button>
                            </form>
                        </span>
                    @endforeach

                    <form method="POST" action="{{ route('admin.crm.contacts.tag', $contact->id) }}" class="mt-3">
                        @csrf
                        <div class="input-group input-group-sm">
                            <input type="text" name="tag" class="form-control" placeholder="Ny tag...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-outline-primary">Legg til</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- E-posthistorikk -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>E-posthistorikk</strong></div>
                <div class="card-body">
                    @if($contact->automationQueue->count())
                    <table class="table table-sm">
                        <thead><tr><th>Sekvens</th><th>Emne</th><th>Status</th><th>Planlagt</th><th>Sendt</th></tr></thead>
                        <tbody>
                        @foreach($contact->automationQueue->sortByDesc('scheduled_at') as $item)
                        <tr>
                            <td><small>{{ $item->sequence?->name }}</small></td>
                            <td>{{ $item->step?->subject }}</td>
                            <td><span class="badge badge-{{ $item->status }}">{{ $item->status }}</span></td>
                            <td><small>{{ $item->scheduled_at?->format('d.m.Y H:i') }}</small></td>
                            <td><small>{{ $item->sent_at?->format('d.m.Y H:i') ?? '—' }}</small></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-muted">Ingen e-poster i historikken.</p>
                    @endif
                </div>
            </div>

            @if($contact->exclusions->count())
            <div class="card mt-3">
                <div class="card-header"><strong>Ekskluderinger</strong></div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>Grunn</th><th>Kurs-ID</th><th>Opprettet</th></tr></thead>
                        <tbody>
                        @foreach($contact->exclusions as $excl)
                        <tr>
                            <td>{{ $excl->reason }}</td>
                            <td>{{ $excl->course_id ?? '—' }}</td>
                            <td>{{ $excl->created_at?->format('d.m.Y') }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
