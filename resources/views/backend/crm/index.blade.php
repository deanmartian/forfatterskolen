@extends('backend.layout')

@section('page_title', 'CRM — Forfatterskolen Admin')

@section('styles')
<style>
.crm-stats { display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
.crm-stat { background: #fff; border-radius: 8px; padding: 16px 24px; min-width: 140px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.crm-stat__number { font-size: 28px; font-weight: 700; color: #862736; }
.crm-stat__label { font-size: 13px; color: #666; margin-top: 4px; }
.nav-tabs .nav-link.active { color: #862736; border-bottom: 2px solid #862736; font-weight: 600; }
.badge-active { background: #28a745; }
.badge-unsubscribed { background: #dc3545; }
.badge-bounced { background: #6c757d; }
.badge-pending { background: #ffc107; color: #333; }
.badge-sent { background: #28a745; }
.badge-cancelled { background: #6c757d; }
.badge-failed { background: #dc3545; }
</style>
@stop

@section('content')

<div class="container-fluid" style="padding: 20px;">
    <h2><i class="fa fa-address-book" style="margin-right: 8px; opacity: 0.6;"></i>CRM</h2>

    <!-- Stats -->
    <div class="crm-stats">
        <div class="crm-stat">
            <div class="crm-stat__number">{{ number_format($totalContacts) }}</div>
            <div class="crm-stat__label">Kontakter</div>
        </div>
        <div class="crm-stat">
            <div class="crm-stat__number">{{ number_format($activeContacts) }}</div>
            <div class="crm-stat__label">Aktive</div>
        </div>
        <div class="crm-stat">
            <div class="crm-stat__number">{{ $sequences->count() }}</div>
            <div class="crm-stat__label">Sekvenser</div>
        </div>
        <div class="crm-stat">
            <div class="crm-stat__number">{{ number_format($pendingEmails) }}</div>
            <div class="crm-stat__label">Ventende e-poster</div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'contacts' ? 'active' : '' }}" href="{{ route('admin.crm.contacts.index') }}">
                <i class="fa fa-users"></i> Kontakter
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'sequences' ? 'active' : '' }}" href="{{ route('admin.crm.sequences.index') }}">
                <i class="fa fa-list-ol"></i> Sekvenser
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'planned' ? 'active' : '' }}" href="{{ route('admin.crm.planned') }}">
                <i class="fa fa-clock-o"></i> Planlagte
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'history' ? 'active' : '' }}" href="{{ route('admin.crm.history') }}">
                <i class="fa fa-history"></i> Historikk
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.newsletter.index') }}">
                <i class="fa fa-newspaper-o"></i> Nyhetsbrev
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'statistics' ? 'active' : '' }}" href="{{ route('admin.crm.statistics') }}">
                <i class="fa fa-bar-chart"></i> Statistikk
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" style="margin-top: 20px;">
        @if($tab === 'contacts')
            @include('backend.crm.tabs.contacts', ['contacts' => $contacts ?? collect()])
        @elseif($tab === 'sequences')
            @include('backend.crm.tabs.sequences', ['sequences' => $sequences ?? collect()])
        @elseif($tab === 'planned')
            @include('backend.crm.tabs.planned', ['planned' => $planned ?? collect()])
        @elseif($tab === 'history')
            @include('backend.crm.tabs.history', ['history' => $history ?? collect()])
        @elseif($tab === 'statistics')
            @include('backend.crm.tabs.statistics', ['stats' => $stats ?? []])
        @endif
    </div>
</div>

@endsection
