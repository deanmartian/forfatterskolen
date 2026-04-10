@extends('backend.layout')

@section('page_title', 'Annonsepanel')

@section('content')
<div class="container-fluid" style="padding: 20px;">
    <h3><i class="fa fa-bullhorn"></i> Annonsepanel</h3>

    {{-- Statistikk-kort --}}
    <div class="row" style="margin: 20px 0;">
        <div class="col-md-2">
            <div style="padding: 15px; background: #d4edda; border-radius: 8px; text-align: center;">
                <div style="font-size: 28px; font-weight: bold; color: #155724;">{{ $stats['active_count'] }}</div>
                <div style="font-size: 12px; color: #155724;">Aktive kampanjer</div>
            </div>
        </div>
        <div class="col-md-2">
            <div style="padding: 15px; background: #cce5ff; border-radius: 8px; text-align: center;">
                <div style="font-size: 28px; font-weight: bold; color: #004085;">kr {{ number_format($stats['total_spend'], 0, ',', ' ') }}</div>
                <div style="font-size: 12px; color: #004085;">Totalt brukt</div>
            </div>
        </div>
        <div class="col-md-2">
            <div style="padding: 15px; background: #fff3cd; border-radius: 8px; text-align: center;">
                <div style="font-size: 28px; font-weight: bold; color: #856404;">{{ number_format($stats['total_leads']) }}</div>
                <div style="font-size: 12px; color: #856404;">Totalt leads</div>
            </div>
        </div>
        <div class="col-md-2">
            <div style="padding: 15px; background: #e2e3e5; border-radius: 8px; text-align: center;">
                <div style="font-size: 28px; font-weight: bold; color: #383d41;">{{ number_format($stats['total_clicks']) }}</div>
                <div style="font-size: 12px; color: #383d41;">Totalt klikk</div>
            </div>
        </div>
        <div class="col-md-2">
            <div style="padding: 15px; background: #1877F2; border-radius: 8px; text-align: center;">
                <div style="font-size: 28px; font-weight: bold; color: #fff;">{{ $stats['fb_active'] }}</div>
                <div style="font-size: 12px; color: #fff;"><i class="fa fa-facebook"></i> Facebook</div>
            </div>
        </div>
        <div class="col-md-2">
            <div style="padding: 15px; background: #4285F4; border-radius: 8px; text-align: center;">
                <div style="font-size: 28px; font-weight: bold; color: #fff;">{{ $stats['google_active'] }}</div>
                <div style="font-size: 12px; color: #fff;"><i class="fa fa-google"></i> Google</div>
            </div>
        </div>
    </div>

    {{-- Faner --}}
    <ul class="nav nav-tabs" style="margin-top: 20px;">
        <li class="{{ $tab === 'overview' ? 'active' : '' }}">
            <a href="{{ route('admin.ads.index', ['tab' => 'overview']) }}">Oversikt</a>
        </li>
        <li class="{{ $tab === 'create' ? 'active' : '' }}">
            <a href="{{ route('admin.ads.index', ['tab' => 'create']) }}">Opprett annonse</a>
        </li>
        <li class="{{ $tab === 'stats' ? 'active' : '' }}">
            <a href="{{ route('admin.ads.index', ['tab' => 'stats']) }}">Statistikk</a>
        </li>
    </ul>

    <div class="tab-content" style="margin-top: 20px;">
        @if($tab === 'overview')
            @include('backend.ads.tabs.overview')
        @elseif($tab === 'create')
            @include('backend.ads.tabs.create')
        @elseif($tab === 'stats')
            @include('backend.ads.tabs.statistics')
        @endif
    </div>
</div>
@endsection
