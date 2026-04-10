@extends('frontend.layout')

@section('page_title', 'Takk &rsaquo; Forfatterskolen')
@section('meta_desc', 'Takk for din henvendelse til Forfatterskolen.')

@section('styles')
<style>
    .ty-wrapper {
        min-height: 70vh;
        display: flex;
        align-items: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #faf8f5 0%, #fdf5f6 100%);
    }
    .ty-card {
        max-width: 720px;
        margin: 0 auto;
        background: #fff;
        border-radius: 16px;
        padding: 60px 50px;
        box-shadow: 0 10px 40px rgba(134, 39, 54, 0.08);
        text-align: center;
    }
    .ty-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #862736 0%, #5e1a26 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 28px;
        box-shadow: 0 8px 24px rgba(134, 39, 54, 0.2);
    }
    .ty-icon svg { width: 40px; height: 40px; color: #fff; }
    .ty-card h1 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 2.2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 16px;
        line-height: 1.2;
    }
    .ty-card p {
        font-size: 1.05rem;
        color: #5a5550;
        line-height: 1.7;
        margin-bottom: 24px;
    }
    .ty-redirect {
        font-size: 0.85rem;
        color: #8a8580;
        margin-top: 28px;
    }
    .ty-redirect span {
        font-weight: 700;
        color: #862736;
    }
    .ty-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #862736;
        color: #fff !important;
        padding: 14px 32px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.15s;
        box-shadow: 0 4px 12px rgba(134, 39, 54, 0.2);
    }
    .ty-btn:hover {
        background: #9c2e40;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(134, 39, 54, 0.3);
        color: #fff !important;
        text-decoration: none;
    }
    @media (max-width: 600px) {
        .ty-card { padding: 40px 28px; }
        .ty-card h1 { font-size: 1.6rem; }
    }
</style>
@stop

@section('content')
<div class="ty-wrapper">
    <div class="ty-card">
        <div class="ty-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>

        <h1>{{ trans('site.front.thank-you.title') }}</h1>
        <p>{{ trans('site.front.thank-you.description') }}</p>

        <a href="{{ url('/account/dashboard') }}" class="ty-btn">
            <i class="fa fa-arrow-right"></i> Gå til Min side
        </a>

        <p class="ty-redirect">
            Du sendes automatisk videre om <span class="redirect-time">5</span> sekunder...
        </p>
    </div>
</div>
@stop

@section('scripts')
<script>
    let time = 5;
    window.setInterval(function() {
        time--;
        if(time === 0){
            window.location.href = '{{ url('/account/dashboard') }}';
        }
        var el = document.querySelector('.redirect-time');
        if (el) el.textContent = time;
    }, 1000);
</script>
@stop
