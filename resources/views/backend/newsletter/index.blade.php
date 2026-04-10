@extends('backend.layout')

@section('page_title', 'Nyhetsbrev — Forfatterskolen Admin')

@section('content')
<div class="container-fluid" style="padding: 20px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fa fa-newspaper-o" style="margin-right: 8px; opacity: 0.6;"></i>Nyhetsbrev</h2>
        <div>
            <a href="{{ route('admin.crm.index') }}" class="btn btn-outline-secondary">← CRM</a>
            <a href="{{ route('admin.newsletter.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Nytt nyhetsbrev</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Emne</th>
                <th>Segment</th>
                <th>Status</th>
                <th>Mottakere</th>
                <th>Sendt</th>
                <th>Dato</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @forelse($newsletters as $nl)
            <tr>
                <td>{{ $nl->subject }}</td>
                <td><small>{{ $nl->segment }}</small></td>
                <td>
                    @php
                        $statusColors = ['draft' => 'secondary', 'scheduled' => 'warning', 'sending' => 'info', 'sent' => 'success', 'cancelled' => 'danger'];
                    @endphp
                    <span class="badge badge-{{ $statusColors[$nl->status] ?? 'secondary' }}">{{ $nl->status }}</span>
                </td>
                <td>{{ $nl->total_recipients > 0 ? number_format($nl->total_recipients) : '—' }}</td>
                <td>{{ $nl->total_sent > 0 ? number_format($nl->total_sent) : '—' }}</td>
                <td><small>{{ $nl->sent_at?->format('d.m.Y H:i') ?? $nl->scheduled_at?->format('d.m.Y H:i') ?? $nl->created_at?->format('d.m.Y') }}</small></td>
                <td>
                    @if($nl->isDraft() || $nl->isScheduled())
                        <a href="{{ route('admin.newsletter.edit', $nl->id) }}" class="btn btn-sm btn-outline-primary"><i class="fa fa-pencil"></i></a>
                    @endif
                    <a href="{{ route('admin.newsletter.preview', $nl->id) }}" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="fa fa-eye"></i></a>
                    @if($nl->isSent())
                        <a href="{{ route('admin.newsletter.stats', $nl->id) }}" class="btn btn-sm btn-outline-info"><i class="fa fa-bar-chart"></i></a>
                    @endif
                    <form method="POST" action="{{ route('admin.newsletter.duplicate', $nl->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fa fa-clone"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-muted text-center">Ingen nyhetsbrev ennå.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{ $newsletters->links() }}
</div>
@endsection
