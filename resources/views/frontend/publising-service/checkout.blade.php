@extends('frontend.layout-self-publishing')

@section('page_title', 'Bestill publiseringstjeneste &rsaquo; Forfatterskolen')
@section('robots')<meta name="robots" content="noindex, follow">@endsection
@section('meta_desc', 'Bestill publiseringstjenester fra Indiemoon Publishing.')

@section('content')

    <div class="container" style="margin-top: 50px">
        <publishing-service-checkout :active-service="{{ json_encode($service) }}" 
        :user="{{ json_encode(Auth::user()) }}"></publishing-service-checkout>
    </div>

@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/app.js?v='.filemtime(public_path('js/app.js'))) }}"></script>
@stop