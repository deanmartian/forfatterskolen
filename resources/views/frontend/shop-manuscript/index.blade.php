@extends('frontend.layout')

@section('title')
<title>Shop Manuscripts &rsaquo; Forfatterskolen</title>
@stop

@section('content')

	@if(Auth::user())
		<div class="account-container">
			@include('frontend.partials.learner-menu')
			<div class="col-sm-12 col-md-10 sub-right-content">
				<div class="col-sm-12">
					@endif

					<div class="container">
						<div class="courses-hero text-center">
							<div class="row">
								<div class="col-sm-12">
									<h2><span class="highlight">MA</span>NUSUTVIKLING</h2>
								</div>
							</div>
						</div>
					</div>

					<div class="container text-center">
						<div class="row">
							<div class="col-sm-10 col-sm-offset-1">
								<h3>Har du utkastet til manuset ditt klart og ønsker profesjonell tilbakemelding?</h3>
								<br />
								<p>
								Forfatterskolen tilbyr individuell veiledning på manus, uansett sjanger. Vi er selv forfattere og vet hvor sårbart det er å overlate et manus til andre. Hos oss kan du være sikker på at vi vil det beste for deg og ditt prosjekt. Dessuten behandles alle manus konfidensielt.<br /><br />
								Når vi har mottatt ditt manus gir vi deg en skriftlig tilbakemelding om tekstens sterke og svake sider, samt konkrete råd og innspill på hvordan du kan jobbe videre. Manuset vil bli lest av en av våre profesjonelle konsulenter.
								</p>
							</div>
						</div>
						<br />
						<button class="btn btn-theme" data-toggle="collapse" data-target="#editors">Redaktører</button>
						<div class="collapse margin-top" id="editors">
							{{-- display 3 items per row --}}
							@foreach($editors->chunk(3) as $editor_chunk)
								<div class="row margin-top">
									<div class="margin-top">&nbsp;</div>
									@foreach($editor_chunk as $editor)
										<div class="col-sm-4">
											<div class="feedback-thumb margin-bottom" style="background-image: url({{ asset($editor['editor_image']) }})"></div>
											<p>
												<strong class="text-theme">{{ $editor['name'] }}</strong> {{ $editor['description'] }}
											</p>
										</div>
									@endforeach
								</div>
							@endforeach
						</div>
					</div>




					<div class="shop-manuscripts-container">

						<div class="container text-center">
							<h3 class="no-margin-top">Lurer du på hvilken manusutvikling du trenger? Last opp manuset ditt her, så finner ordtelleren riktig manusutvikling (dette er bare en ordteller, og gir ingen form for kjøp):</h3>
							<div class="row">
								<div class="col-sm-6 col-sm-offset-3" id="testManuscript">
									<form method="POST" enctype="multipart/form-data" action="{{ route('front.shop-manuscript.test_manuscript') }}">
										{{ csrf_field() }}
										<input type="file" class="hidden" name="manuscript" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
										<label>
											<div><small>Dette er bare en ordteller, og gir ingen form for kjøp</small></div>
											<em>Merk: godkjente filformat er DOCX, PDF og ODT.</em>
										</label>
										<div class="input-group">
											<input type="text" readonly class="form-control" required>
											<span class="input-group-btn">
												<button class="btn btn-primary select-manuscript" type="button">Velg dokument</button>
											</span>
										</div>
										<div class="text-center margin-top">
											<button class="btn btn-theme" type="upload">Last opp</button>
										</div>
									</form>
								</div>
							</div>
							<br />
							<br />
							<div class="row">
								@foreach( $shopManuscripts as $shopManuscript )
								<div class="col-sm-4">
									<div class="panel panel-default">
										<div class="panel-heading">
											<h3>{{ $shopManuscript->title }}</h3>
										</div>
										<div class="panel-body">
											<div class="shop-manuscripts-price">{{ $shopManuscript->max_words }} <br />ORD</div>
											<p>{{ $shopManuscript->description }}</p>
											<h3><strong>{{ \App\Http\FrontendHelpers::formatCurrency($shopManuscript->full_payment_price) }} KR</strong></h3>
											<br />
											<a class="btn btn-theme btn-lg btn-block" href="{{ route('front.shop-manuscript.checkout', $shopManuscript->id) }}">Bestill</a>
										</div>
									</div>
								</div>
								@endforeach
							</div>
						</div>
					</div>

					@if(Session::has('manuscript_test'))
					<div id="manuscriptTestModal" class="modal fade" role="dialog">
					  <div class="modal-dialog modal-sm">
						<div class="modal-content">
						  <div class="modal-body text-center">
							{!! Session::get('manuscript_test') !!}
						  </div>
						</div>
					  </div>
					</div>
					@endif

					<div id="testManuscriptModal" class="modal fade" role="dialog">
					  <div class="modal-dialog">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Regn ut for meg</h4>
						  </div>
						  <div class="modal-body">

						  </div>
						</div>

					  </div>
					</div>


					<div class="shop-manuscripts-carousel-container text-center">
						<h2><span class="highlight">TILBAKEMELDINGER</span> ETTER MANUSUTVIKLING</h2>
						<div class="row" style="margin-left: 0; margin-right: 0;">
							<div class="col-sm-8 col-sm-offset-2">
								<div id="myCarousel" class="carousel slide" data-ride="carousel">
								  <div class="carousel-inner">
									<div class="item active">
										<div class="feedback-thumb margin-bottom" style="background-image: url({{ asset('images/shop-manuscript-testimonials/irene.jpg') }})"></div>
										<p>Jeg fikk de første 50 sidene av manuset mitt vurdert som del av skrivekurset jeg tok ved Forfatterskolen. Tilbakemeldingene ga meg gode, konstruktive råd som hjalp meg å se på teksten min med nye øyne slik at redigeringen føltes givende og kreativ. Jeg fikk dessuten en ny giv i skriveprosessen og tro på at prosjektet kunne og burde fullføres. </p>
										<br />
										<br />
										- <em>Irene Zupin</em>
									</div>
									<div class="item">
										<div class="feedback-thumb margin-bottom" style="background-image: url({{ asset('images/shop-manuscript-testimonials/audhild.jpg') }})"></div>
										<p>Da jeg spent åpnet mailen fra forfatterskolen etter å ha sendt mitt manus til vurdering, ble jeg positivt overrasket over lengden og grundigheten i tilbakemeldingene. Først en oppsummering av handling og dramaturgi, som ga meg en god bekreftelse på at jeg hadde fått frem budskapet mitt. Kommentarer på språk og dialoger ga meg litt å jobbe videre med. Oppsummering og konklusjon ga meg motivasjon til ferdigstilling og utgivelse. Jeg vil garantert benytte meg av forfatterskolen igjen, på mitt neste manus.</p>
										<br />
										<br />
										- <em>Audhild Lønne</em>
									</div>
									<div class="item">
										<div class="feedback-thumb margin-bottom" style="background-image: url({{ asset('images/shop-manuscript-testimonials/svanhild.jpg') }})"></div>
										<p>Jeg har benyttet meg av manusutvikling ved Forfatterskolen til to ulike manus. Det er både lærerikt og utfordrende. Man må jobbe videre med teksten etterpå, og er absolutt ikke sikret utgivelse hos et forlag. Likevel får man en fair mulighet til å utvikle seg selv som fremtidig forfatter. Dessuten, å bli vurdert seriøst er en fantastisk følelse. Anbefales!</p>
										<br />
										<br />
										- <em>Svanhild Fosback Larsen</em>
									</div>
									<div class="item">
										<div class="feedback-thumb margin-bottom" style="background-image: url({{ asset('images/shop-manuscript-testimonials/fiske.jpg') }})"></div>
										<p>Jeg fikk manusvurdering på min første barnebok nå i juni. Fra før hadde jeg hatt to pilotlesere. 
										<br /><br />
										Manusvurdering hos Forfatterskolen ble for meg verdt hver eneste en krone. <br />
										Jeg fikk hjelp til å se manuset utenfra, oppdage logiske brister, veiledning angående tema og struktur, konkrete eksempler og forslag til bedre språk, og tilbakemelding på det som funket bra. Et viktig steg mot målet om å bli antatt!</p>
										<br />
										<br />
										- <em>Mary-Ann Foldnes Fiske</em>
									</div>
								   </div>
								  <ol class="carousel-indicators">
									<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
									<li data-target="#myCarousel" data-slide-to="1"></li>
									<li data-target="#myCarousel" data-slide-to="2"></li>
									<li data-target="#myCarousel" data-slide-to="3"></li>
								  </ol>
								</div>
							</div>
						</div>
					</div>

	@if(Auth::user())
				</div>
			</div>

			<div class="clearfix"></div>
		</div>
	@endif
@stop

@section('scripts')
<script>
	$(document).ready(function(){
		@if(Session::has('manuscript_test'))
		$('#manuscriptTestModal').modal('show');
		@endif
		var form = $('#testManuscript form');
		$('.select-manuscript').click(function(){
			form.find('input[type=file]').click();
		});
		form.find('input[type=text]').click(function(){
			form.find('input[type=file]').click();
		});
		form.find('input[type=file]').on('change', function(){
			var file = $(this).val().split('\\').pop();
			form.find('input[type=text]').val(file);
		});
		form.on('submit', function(e){
			var file = form.find('input[type=file]').val().split('\\').pop();
			if( file == '' ){
				alert('Please select a document file.');
				e.preventDefault();
			}
		});
	});
</script>
@stop