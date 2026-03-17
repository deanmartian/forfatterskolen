@extends('backend.layout')

@section('title')
    <title>E-postoversikt &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<style>
    .email-stats { display: flex; gap: 1.5rem; margin-bottom: 2rem; }
    .email-stat-card {
        background: #fff; border-radius: 8px; padding: 1.25rem 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.08); flex: 1;
    }
    .email-stat-card h4 { font-size: 2rem; margin: 0; font-weight: 700; }
    .email-stat-card p { margin: 0; color: #888; font-size: 0.85rem; }
    .email-category { margin-bottom: 2rem; }
    .email-category h4 {
        font-size: 1rem; font-weight: 600; color: #555;
        border-bottom: 2px solid #eee; padding-bottom: .5rem; margin-bottom: 1rem;
    }
    .email-table { width: 100%; border-collapse: collapse; }
    .email-table th {
        text-align: left; font-size: 0.75rem; text-transform: uppercase;
        color: #999; padding: .5rem .75rem; border-bottom: 1px solid #eee;
    }
    .email-table td { padding: .65rem .75rem; border-bottom: 1px solid #f5f5f5; font-size: 0.9rem; }
    .email-table tr:hover td { background: #fafafa; }
    .badge-sent { background: #27ae60; color: #fff; border-radius: 10px; padding: 2px 8px; font-size: 0.75rem; }
    .badge-none { background: #ccc; color: #fff; border-radius: 10px; padding: 2px 8px; font-size: 0.75rem; }
    .email-actions a, .email-actions button {
        font-size: 0.8rem; margin-right: .5rem; text-decoration: none;
    }
    .btn-xs { padding: 2px 8px; font-size: 0.75rem; border-radius: 3px; }
</style>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-envelope"></i> E-postoversikt</h3>
        <div class="pull-right">
            <a href="{{ route('admin.emails.log') }}" class="btn btn-default btn-sm">
                <i class="fa fa-list"></i> E-post-logg
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Statistikk --}}
    @php
        $totalSent = collect($categories)->flatten(1)->sum('total_sent');
        $totalTypes = count($registry);
        $todaySent = \App\EmailLog::whereDate('created_at', today())->count();
    @endphp
    <div class="email-stats">
        <div class="email-stat-card">
            <h4>{{ $totalTypes }}</h4>
            <p>E-posttyper</p>
        </div>
        <div class="email-stat-card">
            <h4>{{ number_format($totalSent) }}</h4>
            <p>Totalt sendt</p>
        </div>
        <div class="email-stat-card">
            <h4>{{ $todaySent }}</h4>
            <p>Sendt i dag</p>
        </div>
    </div>

    {{-- Kategorier --}}
    @foreach($categories as $categoryName => $emails)
        <div class="email-category">
            <h4>{{ $categoryName }}</h4>
            <div class="table-responsive" style="background:#fff; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,.08);">
                <table class="email-table">
                    <thead>
                        <tr>
                            <th>Navn</th>
                            <th>Beskrivelse</th>
                            <th>Sendt totalt</th>
                            <th>Sist sendt</th>
                            <th>Handlinger</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($emails as $email)
                            <tr>
                                <td><strong>{{ $email['name'] }}</strong></td>
                                <td style="color:#777;">{{ $email['description'] }}</td>
                                <td>
                                    @if($email['total_sent'] > 0)
                                        <span class="badge-sent">{{ number_format($email['total_sent']) }}</span>
                                    @else
                                        <span class="badge-none">0</span>
                                    @endif
                                </td>
                                <td style="color:#999; font-size:0.85rem;">
                                    {{ $email['last_sent'] ? \Carbon\Carbon::parse($email['last_sent'])->diffForHumans() : '—' }}
                                </td>
                                <td class="email-actions">
                                    <a href="{{ route('admin.emails.edit', $email['type']) }}" class="btn btn-default btn-xs">
                                        <i class="fa fa-pencil"></i> Rediger
                                    </a>
                                    <a href="{{ route('admin.emails.preview', $email['type']) }}" class="btn btn-default btn-xs">
                                        <i class="fa fa-eye"></i> Forhåndsvis
                                    </a>
                                    <button class="btn btn-primary btn-xs sendTestBtn"
                                            data-type="{{ $email['type'] }}"
                                            data-name="{{ $email['name'] }}">
                                        <i class="fa fa-paper-plane"></i> Send test
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    {{-- Send test modal --}}
    <div id="sendTestModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form id="sendTestForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Send test-e-post</h4>
                    </div>
                    <div class="modal-body">
                        <p class="test-email-name" style="color:#888; margin-bottom:1rem;"></p>
                        <div class="form-group">
                            <label>E-postadresse</label>
                            <input type="email" name="test_email" class="form-control" required
                                   placeholder="test@eksempel.no" value="{{ auth()->user()->email ?? '' }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-paper-plane"></i> Send
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script>
    $(document).on('click', '.sendTestBtn', function() {
        var type = $(this).data('type');
        var name = $(this).data('name');
        var form = $('#sendTestForm');
        form.attr('action', '/emails/' + type + '/send-test');
        $('#sendTestModal .test-email-name').text(name);
        $('#sendTestModal').modal('show');
    });
</script>
@stop
