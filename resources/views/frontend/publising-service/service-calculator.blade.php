@extends('frontend.layout')

@section('title')
<title>Service Calculator</title>
@stop

@section('styles')
<link rel="stylesheet" href="{{asset('css/self-publishing.css?v='.time())}}">
<style>
    body, .navbar-light {
        background-color: #ffffff !important;
    }

    .navbar {
        background-color: #f8f8f8;
    }

    @media (min-width: 768px) {
        .navbar-light .navbar-brand {
            left: 50%;
        }
    }
        
    /* .coaching-timer-page::before {
        position: relative;
    } */

    #app-container {
        position: relative;
    }

    .pretty input {
        position: absolute;
        left: 0;
        top: 0;
        min-width: 1em;
        width: 100%;
        height: 100%;
        z-index: 2;
        opacity: 0;
        margin: 0;
        padding: 0;
    }

    .pretty input:hover {
        cursor: pointer;
    }

    .package-content .form-check-container .form-check label {
        color: #9D9D9D;
        margin-left: 16px;
    }

    .pretty.p-default input:checked~.state label {
        color: #fff;
    }

    .pretty .state label {
        position: initial;
        font-weight: 400;
        margin: 0;
        text-indent: 1.5em;
        min-width: calc(1em + 2px);
    }

    .pretty .state label:after, .pretty .state label:before {
        content: "";
        width: calc(1em + 2px);
        height: calc(1em + 2px);
        display: block;
        box-sizing: border-box;
        border-radius: 0;
        border: 1px solid transparent;
        z-index: 0;
        position: absolute;
        left: 0;
        top: calc((0% - (100% - 1em)));
        background-color: transparent;
    }

    .pretty.p-default input:checked~.state label:after {
        background-color: transparent!important;
        content: "\2713";
        position: absolute;
        top: 0;
        left: -0.75em;
        font-size: 20px;
        font-weight: 700;
        line-height: .8;
        color: #fff;
        transition: .2s;
    }

    .pretty .state label:before {
        border-color: #bdc3c7;
    }
    
    .pretty.p-default .state label:after {
        transform: scale(.6);
    }

    .character-words span:nth-child(2), .editor-type-service .compute-item-price {
        color: #ac0e1d;
        font-weight: 700;
    }
    
</style>
@stop

@section('content')
<div class="coaching-timer-page" data-bg="https://www.forfatterskolen.no/images-new/ctimer-bg.png">
    <div class="container" id="app-container">

        <div class="card card-global">
            <div class="card-body" style="padding: 50px">
                <h1 class="title text-center">
                    {{ trans('site.publishing-service-calculator.title') }}
                </h1>
                
                <service-calculator :service-list="{{ json_encode($serviceList) }}"></service-calculator>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/app.js?v='.time()) }}"></script>
@stop