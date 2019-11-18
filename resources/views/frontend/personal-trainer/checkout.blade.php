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
							{!! nl2br(trans('site.personal-trainer.about-course'))  !!}
							{{--@if(Auth::guest())
								<form id="checkoutLogin" action="{{route('frontend.login.checkout.store')}}" method="POST">
								{{csrf_field()}}
									<div class="row">
										<div class="col-sm-12">
											<span>
												{{ trans('site.front.form.already-registered-text') }}
											</span>
										</div>
									</div>
								<div class="row">
									<div class="form-group col-sm-4">
										<input type="email" name="email" placeholder="{{ trans('site.front.form.email-address') }}"
											   class="form-control" value="{{old('email')}}" required>
										<p style="margin-top: 7px;"><a href="{{ route('auth.login.show') }}?t=passwordreset"
																	   tabindex="-1">
												{{ trans('site.front.form.reset-password') }}?</a></p>
									</div>
									<div class="form-group col-sm-4">
										<input type="password" name="password" placeholder="{{ trans('site.front.form.password')}}"
											   class="form-control" required>
									</div>
									<div class="form-group col-sm-4">
										<button type="submit" class="btn site-btn-global">
											{{ trans('site.front.form.login')}}
										</button>
									</div>
								</div>
							</form>
							@endif--}}
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
							<form class="form-theme" method="POST" action="{{ route('front.personal-trainer.send') }}"
								  id="place_order_form">
								{{csrf_field()}}
							<h2>
								{{ trans('site.personal-trainer.sub-title') }}
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
									<label class="mb-4">
										Skriv en valgfri tekst på 1000 ord (innenfor hvilken som helst sjanger, unntatt sakprosa)
									</label>
									<textarea class="form-control" name="optional_words" rows="12"
											  id="optional_words">{{ old('optional_words') }}</textarea>
								</div>

								<div class="form-group">
									<label class="mb-4">
										Hva er årsaken til at du søker dette kurset (kort begrunnelse)
									</label>
									<textarea class="form-control" name="reason_for_applying" rows="12"
											  id="reason_for_applying">{{ old('reason_for_applying') }}</textarea>
								</div>

								<div class="form-group">
									<label class="mb-4">
										Hva skal til for at du fullfører dette kurset?
									</label>
									<textarea class="form-control" name="need_in_course" rows="12"
											  id="need_in_course">{{ old('need_in_course') }}</textarea>
								</div>

								<div class="form-group">
									<label class="mb-4">
										Hvilke forventninger har du til deg selv – og oss?
									</label>
									<textarea class="form-control" name="expectations" rows="12"
											  id="expectations">{{ old('expectations') }}</textarea>
								</div>

								<div class="form-group">
									<label class="mb-4">
										Hvor gira er du på å klare målet om ferdig manusutkast innen ett år (sett kryss ved det som er mest riktig):
									</label>

									@foreach(\App\Http\FrontendHelpers::howReadyOptions() as $readyOption)
									<div class="custom-radio px-0">
										<input type="radio" name="how_ready" value="{{ $readyOption['id'] }}"
											   id="{{ str_slug($readyOption['text']) }}" required
											{{ old('how_ready') && old('how_ready') == $readyOption['id'] ? 'checked' : ''}}>
										<label for="{{ str_slug($readyOption['text']) }}">{{ $readyOption['text'] }}</label>
									</div>
									@endforeach
								</div>

								@if(Auth::guest())
								<div class="form-group row">
									<div class="col-md-6">
										<a href="{{ route('auth.login.google') }}" class="loginBtn loginBtn--google btn">
											{{ trans('site.front.form.login-with-google') }}
										</a>

										<a href="{{ route('auth.login.facebook') }}" class="loginBtn loginBtn--facebook btn">
											{{ trans('site.front.form.login-with-facebook') }}
										</a>
									</div>
								</div>
								@endif

								<div class="form-group">
									<button type="submit" class="btn site-btn-global-w-arrow pull-right" id="submitOrder">
										Lever søknad
									</button>
								</div>
							</div> <!-- end panel-body -->
						</div> <!-- end panel -->
					</div>
				</form>
			</div>
		</div>
	</div>
@stop

@section('scripts')
	<script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
	<script>
		let max_words = 1000;
        // tinymce
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

        function getStats(id) {
            let body = tinymce.get(id).getBody(), text = tinymce.trim(body.innerText || body.textContent);

            return {
                chars: text.length,
                words: text.split(/[\w\u2019\'-]+/).length
            };
        }

        /*$("#submitOrder").click(function(){
            let inputted_words = getStats('optional_words').words - 1;
            if (inputted_words > max_words) {
                alert("You entered more than the allowed "+max_words+" words.");
                return false;
            }

            //$("#place_order_form").submit();
		});*/
	</script>
@stop
