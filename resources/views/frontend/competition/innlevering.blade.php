@extends('frontend.layout')

@section('title')
    <title>Checkout &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="checkout-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">

                    <div class="panel panel-default">
                        @if ( $errors->any() )
                            <div class="col-sm-12">
                                <div class="alert alert-danger mb-0">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{!! $error !!}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <br />
                            </div>
                        @endif

                        <form class="form-theme" method="POST" action="{{ route('front.innlevering.send') }}" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <input type="file" class="hidden" name="manuscript"
                                   accept=".doc, .docx, .pdf, .odt">
                            <h2>
                                {{ trans('site.innlevering.title') }}
                            </h2>
                            <div class="panel-heading">{{ trans('site.front.form.user-information') }}</div>

                            <div class="panel-body px-0">

                                <div class="form-group">
                                    <label for="email" class="control-label">
                                        {{ trans('site.front.form.email-address') }}
                                    </label>
                                    <input type="email" id="email" class="form-control large-input" name="email" required
                                           @if(Auth::guest()) value="{{old('email')}}" @else value="{{Auth::user()->email}}"
                                           readonly @endif placeholder="{{ trans('site.front.form.email-address') }}">
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label for="first_name" class="control-label">
                                            {{ trans('site.front.form.first-name') }}
                                        </label>
                                        <input type="text" id="first_name" class="form-control large-input" name="first_name" required
                                               @if(Auth::guest()) value="{{old('first_name')}}" @else
                                               value="{{Auth::user()->first_name}}" readonly @endif
                                               placeholder="{{ trans('site.front.form.first-name') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="control-label">
                                            {{ trans('site.front.form.last-name') }}
                                        </label>
                                        <input type="text" id="last_name" class="form-control large-input" name="last_name" required
                                               @if(Auth::guest()) value="{{old('last_name')}}" @else
                                               value="{{Auth::user()->last_name}}" readonly @endif
                                               placeholder="{{ trans('site.front.form.last-name') }}">
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    @if(Auth::guest())
                                        <div class="col-md-6 mb-4">
                                            <label for="password" class="control-label">
                                                {{ trans('site.front.form.create-password') }}
                                            </label>
                                            <input type="password" id="password" class="form-control large-input"
                                                   name="password" required>
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <div class="form-group">
                                        <div id="manuscript-file">
                                            <label for="manuscript" class="control-label">
                                                {{ trans('site.front.form.upload-manuscript') }}
                                            </label>
                                            <input type="text" readonly class="form-control"
                                                   placeholder="Velg fil"
                                                   id="select-document">
                                        </div>
                                    </div>
                                </div>

                                {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJS() !!}
                                {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display() !!}

                                <div class="form-group">
                                    <button type="submit" class="btn site-btn-global-w-arrow pull-right" id="submitOrder"
                                    disabled>
                                        {{ trans('site.innlevering.button-text') }}
                                    </button>
                                </div>

                            </div> <!-- end panel-body -->
                        </form>
                    </div> <!-- end panel-default -->

                </div> <!-- end col-lg-12 -->
            </div> <!-- end row -->
        </div> <!-- end container -->
    </div> <!-- end checkout page -->
@stop

@section('scripts')
    <script>
        $(document).ready(function(){
            let form = $('.form-theme');
            $("#select-document").click(function(){
                form.find('input[type=file]').click();
            });
            form.find('input[type=file]').on('change', function(){
                let file = $(this).val().split('\\').pop();
                $("#select-document").val(file);
            });
        });
    </script>
@stop