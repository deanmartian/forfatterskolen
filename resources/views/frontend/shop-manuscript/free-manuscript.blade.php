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
					<textarea class="form-control" name="content" required rows="12" placeholder="Maks 500 ord">{{ old('content') }}</textarea>
				</div>
				<div class="text-right">
					<button type="submit" class="btn btn-theme">Send inn</button>
				</div>
			</form>
			<br />
		</div>
	</div>
</div>
@stop

@section('scripts')
<script>
(function($){
    $.fn.textareaCounter = function(options) {
        // setting the defaults
        // $("textarea").textareaCounter({ limit: 100 });
        var defaults = {
            limit: 100
        };  
        var options = $.extend(defaults, options);

        // and the plugin begins
        return this.each(function() {
            var obj, text, wordcount, limited;

            obj = $(this);
            obj.after('<span style="font-size: 11px; clear: both; margin-top: 3px; display: block;" id="counter-text">Maks '+options.limit+' ord</span>');

            obj.keyup(function() {
                text = obj.val();
                if(text === "") {
                    wordcount = 0;
                } else {
                    wordcount = $.trim(text).split(" ").length;
                }
                if(wordcount > options.limit) {
                    $("#counter-text").html('<span style="color: #DD0000;">0 ord igjen</span>');
                    limited = $.trim(text).split(" ", options.limit);
                    limited = limited.join(" ");
                    //$(this).val(limited); this would not allow to add word any further
					$(".btn-theme").text("Slett noen ord").attr('disabled', true);
                } else {
                    $("#counter-text").html((options.limit - wordcount)+' ord igjen');
                    $(".btn-theme").text("Send inn").attr('disabled', false);
                } 
            });
        });
    };
})(jQuery);

$("textarea").textareaCounter({ limit: 500 });
</script>
@stop