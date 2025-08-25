@if( Route::currentRouteName() != 'front.free-manuscript.index')
	@if (Auth::guest())
		<div class="start-today centered">
			<h5 class="font-regular">Vil du ha profesjonell tilbakemelding på en smakebit av din personlige tekst, helt gratis? Send den inn ved å trykke på knappen under.</h5>
			<div></div>
			<a class="btn" href="/gratis-tekstvurdering">Ja, dette vil jeg ha!</a>
			{{--<div>
				<small>Du får svar innen fem virkedager</small>
			</div>--}}
		</div>
	@endif
@endif

<footer>
	<div class="footer-top">
		<div class="container">
			<div class="row">
				<div class="col-md-4 text-center">
                                        <a href="{{ url('') }}" class="footer-brand"><img src="{{asset('images/logo-footer.png')}}" alt="Forfatterskolen-logo"></a>
				</div>

				<div class="col-md-4">
					<h6>KONTAKT OSS</h6>
					<ul>
						<li><i class="fa fa-map-marker"></i> Postboks 9233, 3028 Drammen</li>
						<li><i class="fa fa-at"></i> post@forfatterskolen.no</li>
						<li><i class="fa fa-phone"></i> +47 411 23 555</li>
					</ul>
				</div>
				<div class="col-md-4">
					<h6>SNARVEIER</h6>
					<div class="row">
						<div class="col-xs-6">
							<ul>
								<li><a href="{{ route('front.contact-us') }}">Om oss</a></li>
								<li><a href="http://www.forfatterblogg.no" target="_blank">Skriveblogg</a></li>
								<li><a href="{{ route('front.contact-us') }}">Kontakt oss</a></li>
							</ul>
						</div>
						<div class="col-xss-6">
							<ul>
								<li><a href="{{ route('auth.login.show') }}">Logg inn</a></li>
								<li><a href="{{ route('auth.login.show') }}?t=register">Register deg her</a></li>
								<li><a href="{{ route('front.course.index') }}">Kjøp kurs</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-bottom">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					Copyright © 2016 Forfatterskolen. All Rights Reserved
					<div class="social pull-right">
						<a href="https://no.pinterest.com/forfatterskolen_norge/" target="_blank"><i class="fa fa-pinterest"></i></a>
					    <a href="https://www.facebook.com/bliforfatter/" target="_blank"><i class="fa fa-facebook"></i></a>
					    <a href="https://twitter.com/Forfatterrektor" target="_blank"><i class="fa fa-twitter"></i></a>
					    <a href="https://www.instagram.com/forfatterskolen_norge/" target="_blank"><i class="fa fa-instagram"></i></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</footer>