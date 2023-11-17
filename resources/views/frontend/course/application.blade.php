@extends('frontend.layout')

@section('title')
<title>{{$course->title}} &rsaquo; Forfatterskolen</title>
@stop

@section('content')
@php
    $today 	= \Carbon\Carbon::today()->format('Y-m-d');
    $from 	= \Carbon\Carbon::parse($course->packagesIsShow[0]->full_payment_sale_price_from)->format('Y-m-d');
    $to 	= \Carbon\Carbon::parse($course->packagesIsShow[0]->full_payment_sale_price_to)->format('Y-m-d');
    $isBetween = (($today >= $from) && ($today <= $to)) ? 1 : 0;
    $start_date = \Carbon\Carbon::parse($course->start_date);
    $price = \App\Http\FrontendHelpers::currencyFormat($isBetween && $course->packagesIsShow[0]->full_payment_sale_price
            ? $course->packagesIsShow[0]->full_payment_sale_price
            : $course->packagesIsShow[0]->full_payment_price);
@endphp
<div class="course-application-wrapper">
    <div class="header" data-bg="https://www.forfatterskolen.no/images-new/course/application-header.png">
    </div>
    <div class="body">
        <div class="container">
            <div class="col-md-8 col-sm-offset-2">
                <div class="form-wrapper">
                    <h3 class="price">
                        {{ $price }} kroner
                    </h3>

                    <form class="form-theme" method="POST" action="{{ route('front.course.process-application', $course->id) }}"
								  id="place_order_form" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{csrf_field()}}

                        <div class="form-group">
                            <label for="email" class="control-label">
                                {{ trans('site.front.form.email-address') }}
                            </label>
                            <input type="email" id="email" class="form-control" name="email" required
                                   @if(Auth::guest()) value="{{old('email')}}" @else value="{{Auth::user()->email}}"
                                   readonly @endif placeholder="{{ trans('site.front.form.email-address') }}">
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="first_name" class="control-label">
                                    {{ trans('site.front.form.first-name') }}
                                </label>
                                <input type="text" id="first_name" class="form-control" name="first_name" required
                                       @if(Auth::guest()) value="{{old('first_name')}}" @else
                                       value="{{Auth::user()->first_name}}" readonly @endif
                                       placeholder="{{ trans('site.front.form.first-name') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="control-label">
                                    {{ trans('site.front.form.last-name') }}
                                </label>
                                <input type="text" id="last_name" class="form-control" name="last_name" required
                                       @if(Auth::guest()) value="{{old('last_name')}}" @else
                                       value="{{Auth::user()->last_name}}" readonly @endif
                                       placeholder="{{ trans('site.front.form.last-name') }}">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 mb-4">
                                <label for="phone" class="control-label">
                                    {{ trans('site.front.form.phone-number') }}
                                </label>
                                <input type="text" id="phone" class="form-control large-input" name="phone" required
                                       @if(Auth::guest()) value="{{old('phone')}}"
                                       @else value="{{Auth::user()->address['phone']}}" @endif>
                            </div>
                            @if(Auth::guest())
                                <div class="col-md-6 mb-4">
                                    <label for="password" class="control-label">
                                        {{ trans('site.front.form.create-password') }}
                                    </label>
                                    <input type="password" id="password" class="form-control large-input"
                                           name="password" required>
                                </div>
                            @endif
                            <div class="col-md-6 mb-4">
                                <label for="age" class="control-label">
                                    {{ trans('site.front.form.age') }}
                                </label>
                                <input type="number" id="age" class="form-control large-input" name="age"
                                       step="1" value="{{ old('age') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <b>
                                Last opp ett dokument som inneholder:
                            </b>
                            <ul>
                                <li>
                                    en kort redegjørelse for din motivasjon for kurset
                                </li>
                                <li>
                                    en kort beskrivelse av prosjektet ditt (blant annet sjanger og hva du skriver om)
                                </li>
                                <li>
                                    500-1000 ord av prosjektet du planlegger å jobbe med på kurset
                                </li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <div class="file-upload" id="file-upload-application">
                                <i class="fa fa-cloud-upload-alt"></i>
                                <div class="file-upload-text">
                                    Drag and drop files or <a href="javascript:void(0)" class="file-upload-btn">Klikk her</a>
                                </div>
                                <input type="file" class="form-control hidden input-file-upload" name="manuscript" 
                                id="file-upload" accept="application/msword,
                            application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                            application/pdf, application/vnd.oasis.opendocument.text">
                              </div>
                            <label class="file-label">
                                * {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}
                            </label>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn site-btn-global pull-right" id="submitOrder">
                                Lever søknad
                            </button>
                        </div>

                        <div class="clearfix"></div>
                    </form>
                </div> <!-- end form-wrapper -->
            </div> <!-- end col-md-10 col-md-offset-1 -->
        </div> <!-- end container -->
    </div> <!-- end body -->
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script>
    setupGlobalFileUpload('file-upload-application');

    let editor_config = {
        path_absolute: "{{ URL::to('/') }}",
        height: '15em',
        selector: 'textarea',
        menubar:false,
        plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'emoticons template paste textcolor colorpicker textpattern'],
        toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript superscript | forecolor backcolor | link | alignleft aligncenter alignright ' +
        'alignjustify  | removeformat',
        relative_urls: false,

        setup: function(ed) {
            ed.on('keydown', function (e) {
                let body = ed.getBody(), text = tinymce.trim(body.innerText || body.textContent);
                let words = text.split(/[\w\u2019\'-]+/).length - 1;

                // allow delete and f5 keys
                if (words > max_words && e.keyCode !== 8 && e.keyCode !== 116) {
                    return tinymce.dom.Event.cancel(e);
                }
            });

        }
    };
    tinymce.init(editor_config);
</script>
@stop