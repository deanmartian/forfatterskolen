@extends('frontend.layout')

@section('title')
	<title>Forfatterskolen &rsaquo; Free Manuscripts</title>
@stop

@section('content')
    <div id="opt-in-page">
        <div class="container">
            <div class="row">
                <div class="image-container">
                    {{--<div class="logo-image"></div>--}}

                    <div id="main_desc_container" class="">
                        {!! nl2br($optIn->main_description) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 no-left-right-margin border-bottom">
            <div class="container form-container">
                <div class="row">
                    <div class="col-md-6 left-content">
                        {!! nl2br($optIn->form_description) !!}

                        <form class="margin-bottom" method="POST" action="">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <input type="text" class="form-control" name="name" required value="{{ old('name') }}"
                                placeholder="Fornavn">
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" name="email" required value="{{ old('email') }}"
                                placeholder="E-post">
                            </div>
                            <div class="form-group mb-0">
                                <input type="checkbox" name="terms" required>
                                <label>
                                    {!! strtr(trans('site.front.accept-terms'),
                                        [
                                            '[start_link]' => '<a href="'.route('front.opt-in-terms').'">',
                                            '[end_link]' => '</a>'
                                        ]) !!}
                                </label>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <p class="note">
                                        {{ trans('site.front.opt-in.note') }}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn site-btn-global">
                                        {{ trans('site.front.opt-in.send') }}
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if(Session::has('opt-in-message'))
                            <div class="alert alert-success" role="alert">
                                {{ trans('site.front.opt-in.success-message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if ( $errors->any() )
                            <div class="alert alert-danger bottom-margin">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{$error}}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-5 col-sm-offset-1 description-container">
                        {!! nl2br($optIn->description) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 no-left-right-margin static-details-container">
            <div class="container jumbotron">
                {!! strtr(trans('site.front.opt-in.jumbotron'),
                    ['_start_span_' => '<span class="red">', '_end_span_' => '</span>']) !!}
            </div>
        </div>
<div class="clearfix"></div>
    </div>
@stop

@section('scripts')
    <script>
        $(".btn-submit").click(function(e){
            e.preventDefault();
            $(this).attr('disabled', true).text('Sende...');
            $("form").submit();
        });
    </script>
@stop