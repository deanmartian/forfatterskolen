@extends('frontend.layout')

@section('title')
<title>Forfatterskolen &rsaquo; Free Manuscripts</title>
@stop

@section('content')
<div class="container">
	<div class="courses-hero free-manuscripts-hero text-center">
		<div class="row" style="position: relative; z-index: 10">
			<div class="col-md-12">
				<h2><span class="highlight">PRØV</span> EN GRATIS TEKSTVURDERING</h2>
			</div>
		</div>
	</div>
</div>


<div class="container">
	<div class="row">
		<div class="col-md-10 col-sm-offset-1">
			<p class="text-center courses-description">
			Har du lyst til å få en profesjonell tilbakemelding på din tekst? Skriv inn valgfri tekst i skjemaet under maks 500 ord.
			</p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-sm-offset-3">
			@if($errors->any())
				<div class="alert alert-danger">
					<ul>
						@foreach($errors->all() as $error)
							<li>{!! $error !!}</li>
						@endforeach
					</ul>
				</div>
			@endif
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
					<label>Sjanger</label>
					<select class="form-control" name="genre" required>
						<option value="" disabled="disabled" selected>Velg Sjanger</option>
						@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
							<option value="{{ $type->id }}"> {{ $type->name }} </option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label>Din tekst (for å lime inn må du bruke lim inn funksjon fra tastaturet, ikke mus) CTRL + V</label>
					<textarea class="form-control" name="content" rows="12" placeholder="Maks 500 ord"
					id="editor">{{ old('content') }}</textarea>
					<small>
						*Kun en innsending per person
					</small>
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
	<script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
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

                $.post('/free-manuscript/set-word-count', {wordcount: wordcount}).then(function(response){

				});
            });
        });
    };
})(jQuery);

// tinymce
let editor_config = {
    path_absolute: "{{ URL::to('/') }}",
    height: '15em',
    selector: '#editor',
    menubar:false,
    max_word: 500,
    plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
        'searchreplace wordcount visualblocks visualchars code fullscreen',
        'insertdatetime media nonbreaking save table contextmenu directionality',
        'emoticons template paste textcolor colorpicker textpattern'],
    toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript superscript | forecolor backcolor | link | alignleft aligncenter alignright ' +
    'alignjustify  | removeformat',
    relative_urls: false,
    file_browser_callback : function(field_name, url, type, win) {
        let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
        let y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

        let cmsURL = editor_config.path_absolute + '/laravel-filemanager?field_name=' + field_name;
        if (type == 'image') {
            cmsURL = cmsURL + '&type=Images';
        } else {
            cmsURL = cmsURL + '&type=Files';
        }

        tinyMCE.activeEditor.windowManager.open({
            file : cmsURL,
            title : 'Filemanager',
            width : x * 0.8,
            height : y * 0.8,
            resizable : 'yes',
            close_previous : 'no'
        });
    },

    setup: function(ed) {
        ed.on('keyup', function (e) {
            let event = e;
            setTimeout(function() {
                let writtenWords = $('.mce-wordcount').html();
                writtenWords = parseInt(writtenWords.replace("words", ""));
                let maxWord = ed.settings.max_word;
                let limited = "";
                let content = ed.getContent();
                let limit = maxWord - writtenWords;
                if (writtenWords > maxWord) {
                    //$('.mce-wordcount').css("color", "red");
                    limited = $.trim(content).split(" ", maxWord);
                    limited = limited.join(" ");

                    ed.setContent(limited);
                    limit = 0;
                } else {
                    $("#"+ed.id).next('span').css("color","inherit");
                }

                if (limit <= 0) {
                    limit = 0;
                    $("#"+ed.id).next('span').css("color","red");
                }

                $("#"+ed.id).next('span').text(limit+' ord igjen');

                $.post('/free-manuscript/set-word-count', {wordcount: writtenWords}).then(function(response){

                });
            }, 320);

        });

    },


};
tinymce.init(editor_config);

$("textarea").textareaCounter({ limit: 500 });

$("form").on('submit',function(){
    $("[type=submit]").attr('disabled', 'disabled');
});

</script>
@stop