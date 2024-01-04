@extends('frontend.layout')

@section('title')
<title>Forfatterskolen &rsaquo; Free Manuscripts</title>
@stop

@section('styles')
    <style>
        .mce-branding {
            display: none !important;
        }
    </style>
@stop

@section('content')
<div class="free-manuscript-page free-manuscript-page-new">
    <div class="header" data-bg="https://www.forfatterskolen.no/images-new/checkout-top.png">
    </div> <!-- end header-->

    <div class="body">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-12 col-md-offset-2">
                    <div class="text-center mb-5">
                        <h1>{{ trans('site.front.free-manuscript.header-title') }}</h1>
                        <p>
                            {{ trans('site.front.free-manuscript.description') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row form-container">
                <div class="col-lg-8 col-md-12 col-md-offset-2">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="title">
                                {{ trans('site.front.free-manuscript.title') }}
                            </h2>

                            <form class="margin-bottom" method="POST" action="{{ route($action) }}">
                                {{ csrf_field() }}

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa at-icon"></i></span>
                                    </div>
                                    <input type="email" name="email" class="form-control no-border-left"
                                           placeholder="{{ trans('site.front.form.email') }}" required value="{{old('email')}}">
                                </div>

                                <div class="input-group mt-5">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa user-icon"></i></span>
                                    </div>
                                    <input type="text" name="name" class="form-control no-border-left"
                                           placeholder="{{ trans('site.front.form.first-name') }}" required value="{{old('name')}}">
                                </div>

                                <div class="input-group mt-5">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa user-icon"></i></span>
                                    </div>
                                    <input type="text" name="last_name" class="form-control no-border-left"
                                           placeholder="{{ trans('site.front.form.last-name') }}" required value="{{old('last_name')}}">
                                </div>

                                <div class="form-group mt-5">
                                    <select class="form-control" name="genre" required>
                                        <option value="" disabled="disabled" selected>{{ ucwords(trans('site.front.select-genre')) }}</option>
                                        @foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
                                            <option value="{{ $type->id }}"> {{ $type->name }} </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="font-quicksand-regular mb-4" style="font-size: 14px">
                                        {{ trans('site.front.free-manuscript.instruction') }}
                                    </label>
                                    <textarea class="form-control" name="manuscript_content" rows="12"
                                              placeholder="{{ trans('site.front.free-manuscript.max-word-text') }}"
                                              id="editor">{{ old('manuscript_content') }}</textarea>
                                    <span class="note-color">
                                        *{{ trans('site.front.free-manuscript.note') }}
                                    </span>
                                </div>

                                <button type="submit" class="btn site-btn-global w-25">
                                    {{ trans('site.front.free-manuscript.send') }}
                                </button>
                            </form>

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{!! $error !!}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div> <!-- end column -->
            </div> <!-- end row -->
        </div> <!-- end container -->
    </div> <!-- end body -->
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
        'insertdatetime media nonbreaking save table directionality',
        'emoticons template paste textcolor colorpicker textpattern'],
    /*toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript superscript | forecolor backcolor | link | alignleft aligncenter alignright ' +
    'alignjustify  | removeformat',*/
    toolbar1: 'undo redo fontselect fontsizeselect bold italic underline strikethrough \
                    alignleft aligncenter alignright alignjustify ',
    toolbar2: 'copy cut bullist numlist outdent indent forecolor backcolor link image searchreplace removeformat fullscreen ' +
    'leftChev rightChev enDash',
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

        // add buttons to toolbar
        ed.addButton('leftChev', {
            text: '<<',
            tooltip: '',
            onclick: function (_) {
                ed.insertContent("&#171;");
            }
        });

        ed.addButton('rightChev', {
            text: '>>',
            tooltip: '',
            onclick: function (_) {
                ed.insertContent("&#187;");
            }
        });

        ed.addButton('enDash', {
            text: '-',
            tooltip: '',
            onclick: function (_) {
                ed.insertContent("&#8211;");
            }
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