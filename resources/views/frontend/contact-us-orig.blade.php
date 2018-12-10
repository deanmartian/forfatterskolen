@extends('frontend.layout')

@section('title')
<title>Forfatterskolen &rsaquo; Kontakt Oss</title>
@stop

@section('content')
	@if(Auth::user())
		<div class="account-container">
			@include('frontend.partials.learner-menu')
			<div class="col-sm-12 col-md-10 sub-right-content">
				<div class="col-sm-12">
	@endif

					<div class="container">
						<div class="courses-hero contact-hero text-center">
							<div class="row">
								<div class="col-sm-12">
									<h2><span class="highlight">KONTAKT</span> OSS</h2>
								</div>
							</div>
						</div>
					</div>

					<div class="container margin-bottom">
						<div class="row">
							<div class="col-sm-4">
								<img src="{{ asset('images/kristine.png') }}" style="width: 100%">
								<div class="margin-bottom"><em><small>Foto: Vibeke Montero</small></em></div>
							</div>
							<div class="col-sm-8">
								<p>
									Forfatterskolen er en nettbasert skriveskole, med kurs innenfor flere sjangre. Se våre kurs her: Forfatterskolens varierte og skreddersydde tilbud.
								</p>
								<p>
									Grunnlegger og rektor for skolen er Kristine Storli Henningsen, som er en suksessfull skjønnlitterær-, seriebok-, sakprosa-, spennings- og selvhjelpsbokforfatter på henholdsvis Gyldendal Forlag, Cappelen Damm, Schibsted, Juritzen og Flux. Til sammen har hun utgitt 26 bøker, og debutromanen, I skyggen av store trær, er solgt til flere land.
								</p>
								<p>
									Kristine har også jobbet som journalist i 15 år, og som redaktør i fem av dem. Hun var redaktør for småbarnsmagasinet PlussTid i fem år.
								</p>
								<p>
									Skolens rektor står bak den populære bloggen <a href="http://www.antisupermamma.no" class="text-theme">Antisupermamma</a>, som har over 20 000 følgere hver uke og har vært i en rekke medier de siste årene. Hun er også fast familieblogger for VG.
								</p>
								<p>
									Ved siden av skrivingen er Kristine utdannet gestaltpsykoterapeut ved NGI.
								</p>
							</div>
							<div class="col-sm-12">
								<p>
									Kristine er ikke alene om å administrere skolen, som stadig vokser i omfang og har flere hundre elever. Med seg på laget har hun flere dyktige manuskonsulenter og to faste ansatte.
									Forfatterskolen har fulgt flere elever tett frem mot utgivelse, og ønsker å hjelpe frem mange flere. De utgir også en elev i året, som ekslusivt blir plukket blant elevene og får hedersbetegnelsen Årets Drømmeforfatter. Utgivelsen fullfinansieres av Forfatterskolen, og boken vil bli utgitt av forlaget Forfatterdrøm, som er under paraplyen Forfatterskolen. Les mer om forlaget her: <a href="http://www.forfatterdrom.no" class="text-theme">Forfatterdrøm</a>
								</p>
								<p>
									Interessert i kurs eller noe annet skriverelatert? Send oss en mail: <a href="mailto:post@forfatterskolen.no" class="text-theme">post@forfatterskolen.no</a>.

								</p>
							</div>
						</div>
					</div>


					<div class="contact-feedbacks text-center">
						<div class="container padding-top padding-bottom">
							<div class="row">
								<div class="col-sm-12 margin-bottom">
									<h2><span class="highlight">VÅR</span> EMINENTE STAB</h2> <br />
								</div>
								<div class="col-sm-4 margin-bottom">
									<div class="backbar"></div>
									<div class="contact-feedback-image" style="background-image: url({{ asset('images/hanne.png')  }})"></div>
									<h3>Hanne Einang</h3>
									<p>
										Hanne er vår grundige, dyktige og løsningsorienterte sekretær, assistent og mye annet. Hanne brenner for at elevene skal ha det bra på skolen, og er inne på det lukkede skriveforumet langt mer enn hun får betalt for.
									</p>
									<i class="fa fa-envelope"></i> <a href="mailto:Hanne@forfatterskolen.no">Hanne@forfatterskolen.no</a>
									<br />
									<br />
								</div>
								<div class="col-sm-4 margin-bottom">
									<div class="backbar darker"></div>
									<div class="contact-feedback-image" style="background-image: url({{ asset('images/elin.png')  }})"></div>
									<h3>Elin S Rotevatn</h3>
									<p>
										Elin S Rotevatn er vår faste redaktør. Hun har en Cand.mag-grad i allmenn litteraturvitenskap, og har i tillegg studert skriveteori gjennom en årrekke. Elin har jobbet som konsulent, blant annet for Riksantikvaren, og elsker å gå inn i andres tekst og finne forbedringspotensial - både på det strukturelle og det språklige plan.
									</p>
									<i class="fa fa-envelope"></i> <a href="mailto:Elin@forfatterskolen.no">Elin@forfatterskolen.no</a>
									<br />
									<br />
								</div>
								<div class="col-sm-4">
									<div class="backbar"></div>
									<div class="contact-feedback-image" style="background-image: url({{ asset('images/sven.png')  }})"></div>
									<h3>Sven Inge Henningsen</h3>
									<p>
										Hva skulle vi gjort uten supportavdelingen? Sven Inge tar seg av alt det tekniske på skolen, ordner med betalinger og delbetalinger. tar seg av logistikken og altfor mye annet.
									</p>
									<i class="fa fa-envelope"></i> <a href="mailto:support@forfatterskolen.no">support@forfatterskolen.no</a>
                                    <p>
                                        <a href="https://get.teamviewer.com/g2nvg34">Fjernsupport</a>
                                    </p>
									<br />
									<br />
								</div>
							</div>
						</div>
					</div>


					<div class="container padding-top padding-bottom contact-container">
						<div class="row">
							<div class="col-sm-12 margin-bottom text-center">
								<h2><span class="highlight">KONTAKT</span>  OSS I DAG</h2> <br /><br /><br />
							</div>
							<div class="col-sm-5">
								<form method="POST" action="" onsubmit="disableSubmit(this)">
									{{ csrf_field() }}
									<div class="form-group">
										<input type="text" class="form-control" name="fullname" placeholder="Navn" required
										value="{{ old('fullname') }}">
									</div>
									<div class="form-group">
										<input type="email" class="form-control" name="email" placeholder="E-postadresse" required
											   value="{{ old('email') }}">
									</div>
									<div class="form-group">
										<textarea class="form-control" rows="8" name="message" placeholder="Skriv inn meldingen din" required>{{ old('message') }}</textarea>
									</div>

									{!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJS() !!}
									{!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display() !!}

									<div class="text-right margin-bottom">
										<button type="submit" class="btn btn-theme">Send</button>
									</div>
								</form>

								@if ( $errors->any() )
									<div class="alert alert-danger no-bottom-margin">
										<ul>
											@foreach($errors->all() as $error)
												<li>{{$error}}</li>
											@endforeach
										</ul>
									</div>
								@endif
							</div>
							<div class="col-sm-1"></div>
							<div class="col-sm-6">
								<div class="contact-feedback-image" style="background-image: url({{ asset('images/kristine2.png') }}); margin-right: 20px"></div>
								<div class="rektor">
									<h4 class="no-margin-bottom">KRISTINE STORLI HENNINGSEN</h4>
									Rektor
								</div>
								<br />
								<br />
								<br />
								<div class="margin-top">
									<p><i class="fa fa-map-marker"></i>Postboks 9233, 3028 Drammen</p>
									<p><i class="fa fa-at"></i>post@forfatterskolen.no</p>
									<p><i class="fa fa-phone"></i>+47 411 23 555</p>
								</div>
							</div>
						</div>
						<br />
					</div>

	@if(Auth::user())
				</div>
			</div>

			<div class="clearfix"></div>
		</div>
	@endif
@stop