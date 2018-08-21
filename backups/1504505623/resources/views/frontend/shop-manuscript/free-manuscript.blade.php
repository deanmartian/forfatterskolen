@extends('frontend.layout')

@section('title')
<title>Forfatterskolen &rsaquo; Free Manuscripts</title>
@stop

@section('content')
<div class="container">
	<div class="courses-hero free-manuscripts-hero text-center">
		<div class="row" style="position: relative; z-index: 10">
			<div class="col-sm-12">
				<h2><span class="highlight">PRØV</span> EN GRATIS TEKSTVURDERING</h2>
			</div>
		</div>
	</div>
</div>


<div class="container">
	<div class="row">
		<div class="col-sm-10 col-sm-offset-1">
			<p class="text-center courses-description">
			Har du lyst til å få en profesjonell tilbakemelding på din tekst? Skriv inn valgfri tekst i skjemaet under maks 500 ord.
			</p>
			<br /><br />
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
			<form class="margin-bottom" method="POST" action="{{ route('front.free-manuscript.send') }}">
				{{ csrf_field() }}
				<div class="form-group">
					<label>Ditt navn</label>
					<input type="text" class="form-control" name="name" required value="{{ old('name') }}">
				</div>
				<div class="form-group">
					<label>E-post</label>
					<input type="email" class="form-control" name="email" required value="{{ old('email') }}">
				</div>
				<div class="form-group">
					<label>Din tekst</label>
					<textarea class="form-control" name="content" required rows="12" placeholder="Maks 500 tegn">{{ old('content') }}</textarea>
					<div class="text-right">
						<div class="word_count"><span>0</span>/500 words</div>
					</div>
				</div>
				<div class="text-right">
					<button type="submit" class="btn btn-theme">Send</button>
				</div>
			</form>
			<br />
		</div>
	</div>
</div>

@stop

@section('scripts')
<script>
	var maxWords = 500;
	jQuery('textarea').on('keypress input propertychange', null, function() {
	    var $this, wordcount;
	    $this = $(this);
	    wordcount = $this.val().split(/\b[\s,\.-:;]*/).length;
	    if (wordcount > maxWords) {
	        jQuery(".word_count span").text("" + maxWords);
	        return false;
	    } else {
	        return jQuery(".word_count span").text(wordcount);
	    }
	});
</script>
@stop