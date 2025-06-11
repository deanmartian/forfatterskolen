@extends('backend.layout')

@section('title')
    <title>Translations &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
    <style>
        a.status-1{
            font-weight: bold;
        }

        .editable-click, a.editable-click, a.editable-click:hover {
            text-decoration: none;
            border-bottom: dashed 1px #0088cc;
        }

        .editable-pre-wrapped {
            white-space: pre-wrap;
        }

        .editable-empty, .editable-empty:hover, .editable-empty:focus {
            font-style: italic;
            color: #DD1144;
            text-decoration: none;
        }

        .delete-key {
            color: #f00;
        }
    </style>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-language"></i> Translations</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <div class="row margin-top">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h1 style="margin-top: 0">Translation Manager</h1>
                        <p class="text-muted d-block" style="margin-bottom: 0"><i class="fa fa-exclamation-triangle"></i> Warning, translations are not visible until they are exported back using the publish button.</p>
                        <p class="text-muted d-block">
                            <i class="fa fa-exclamation-circle"></i>
                            Note, Please be guided that those words which start with <strong>colon(:)</strong> like <strong>:attribute</strong> and words which surrounded with <strong>underscore(_)</strong> like <strong>_MAX_, _PAGE_, _MIN_, etc</strong> must not be translated to avoid conflicts on the system functionality.
                        </p>
                        <div class="alert alert-success success-import" style="display:none;">
                            <p>Done importing, processed <strong class="counter">N</strong> items! Reload this page to refresh the groups!</p>
                        </div>
                        <div class="alert alert-success success-find" style="display:none;">
                            <p>Done searching for translations, found <strong class="counter">N</strong> items!</p>
                        </div>
                        <div class="alert alert-success success-publish" style="display:none;">
                            <p>Done publishing the translations!</p>
                        </div>

                        @if(Session::has('successPublish'))
                            <div class="alert alert-info">
                                {{ Session::get('successPublish') }}
                            </div>
                        @endif
                        <p>
                        @if(!isset($group))
                            <form class="form-inline form-import" method="POST" action="{{ action([\Barryvdh\TranslationManager\Controller::class, 'postImport']) }}" data-remote="true" role="form">
                                {{ csrf_field() }}
                                <select name="replace" class="form-control">
                                    <option value="0">Append new translations</option>
                                    <option value="1">Replace existing translations</option>
                                </select>
                                <button type="submit" class="btn btn-success"  data-disable-with="Loading..">Import groups</button>
                            </form>
                            <form class="form-inline form-find" method="POST" action="{{ action([\Barryvdh\TranslationManager\Controller::class, 'postFind']) }}" data-remote="true" role="form" data-confirm="Are you sure you want to scan you app folder? All found translation keys will be added to the database.">
                                {{ csrf_field() }}
                                <p></p>
                                <button type="submit" class="btn btn-info" data-disable-with="Searching.." >Find translations in files</button>
                            </form>
                        @endif

                        @if(isset($group))
                            {{--<form class="form-inline form-publish" method="POST" action="{{ action([\Barryvdh\TranslationManager\Controller::class, 'postPublish'], $group) }}" data-remote="true" role="form" data-confirm="Are you sure you want to publish the translations? This will overwrite existing language files.">
                                {{ csrf_field() }}--}}
                            @if(Session::has('success'))
                                <div class="alert alert-success">
                                    {{ Session::get('success') }}
                                </div>
                            @endif
                            <button type="button" class="btn btn-info" data-disable-with="Publishing.."
                                    onclick="publishTranslations(this)">Publish translations</button>

                            <a href="{{ route('admin.clear.cache') }}" class="btn btn-warning">Clear Cache</a>
                            {{--</form>--}}
                            @endif
                            </p>
                            <form role="form">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <select name="group" id="group" class="form-control group-select">
                                        @foreach($groups as $key => $value)
                                            @if ($key)
                                                <option value="{{ $key }}"{{ $key == $group ? ' selected':'' }}>{{ ucfirst($value) }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                            @if($group)
                                <form action="{{ action([\Barryvdh\TranslationManager\Controller::class, 'postAdd'], array($group)) }}" method="POST"  role="form">
                                    {{ csrf_field() }}
                                    <textarea class="form-control" rows="3" name="keys" placeholder="Add 1 key per line"></textarea>
                                    <p></p>
                                    <input type="submit" value="Add keys" class="btn btn-primary">
                                </form>
                                <hr style="margin: 20px 0;">
                                <h4 style="margin-bottom: 20px">Total: {{ $numTranslations }}, changed: <span id="numChanged">{{ $numChanged }}</span></h4>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th width="15%">Key</th>
                                        @foreach($locales as $locale)
                                            <th>{{ $locale }}</th>
                                        @endforeach

                                        @if($deleteEnabled)
                                            <th>&nbsp;</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($translations as $key => $translation)
                                        <tr id="{{ $key }}">
                                            <td>{{ $key }}</td>
                                            @foreach($locales as $locale)
                                                <?php $t = isset($translation[$locale]) ? $translation[$locale] : null?>

                                                <td>
                                                    <a href="#edit" class="editable status-{{ $t ? $t->status : 0 }} locale-{{ $locale }}" data-locale="{{ $locale }}" data-name="{{ $locale . "|" . $key }}" id="username" data-type="textarea" data-pk="{{ $t ? $t->id : 0 }}" data-url="{{ $editUrl }}" data-title="Enter translation">{{ $t ? htmlentities($t->value, ENT_QUOTES, 'UTF-8', false) : '' }}</a>
                                                </td>
                                            @endforeach

                                            @if($deleteEnabled)
                                                <td>
                                                    <a href="{{ action([\Barryvdh\TranslationManager\Controller::class, 'postDelete'], [$group, $key]) }}" class="delete-key" rel="nofollow" data-confirm="Are you sure you want to delete the translations for '{{ $key }}'?"><span class="glyphicon glyphicon-trash"></span></a>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            @else
                                <p>Choose a group to display the group translations. If no groups are visible, make sure you have run the migrations and imported the translations.</p>
                            @endif
                    </div> <!-- end panel-body -->
                </div> <!--end panel -->
            </div> <!-- end col-sm-12 -->
        </div>
    </div>
@stop

@section('scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
    <script>//https://github.com/rails/jquery-ujs/blob/master/src/rails.js
        (function(e,t){if(e.rails!==t){e.error("jquery-ujs has already been loaded!")}var n;var r=e(document);e.rails=n={linkClickSelector:"a[data-confirm], a[data-method], a[data-remote], a[data-disable-with]",buttonClickSelector:"button[data-remote], button[data-confirm]",inputChangeSelector:"select[data-remote], input[data-remote], textarea[data-remote]",formSubmitSelector:"form",formInputClickSelector:"form input[type=submit], form input[type=image], form button[type=submit], form button:not([type])",disableSelector:"input[data-disable-with], button[data-disable-with], textarea[data-disable-with]",enableSelector:"input[data-disable-with]:disabled, button[data-disable-with]:disabled, textarea[data-disable-with]:disabled",requiredInputSelector:"input[name][required]:not([disabled]),textarea[name][required]:not([disabled])",fileInputSelector:"input[type=file]",linkDisableSelector:"a[data-disable-with]",buttonDisableSelector:"button[data-remote][data-disable-with]",CSRFProtection:function(t){var n=e('meta[name="csrf-token"]').attr("content");if(n)t.setRequestHeader("X-CSRF-Token",n)},refreshCSRFTokens:function(){var t=e("meta[name=csrf-token]").attr("content");var n=e("meta[name=csrf-param]").attr("content");e('form input[name="'+n+'"]').val(t)},fire:function(t,n,r){var i=e.Event(n);t.trigger(i,r);return i.result!==false},confirm:function(e){return confirm(e)},ajax:function(t){return e.ajax(t)},href:function(e){return e.attr("href")},handleRemote:function(r){var i,s,o,u,a,f,l,c;if(n.fire(r,"ajax:before")){u=r.data("cross-domain");a=u===t?null:u;f=r.data("with-credentials")||null;l=r.data("type")||e.ajaxSettings&&e.ajaxSettings.dataType;if(r.is("form")){i=r.attr("method");s=r.attr("action");o=r.serializeArray();var h=r.data("ujs:submit-button");if(h){o.push(h);r.data("ujs:submit-button",null)}}else if(r.is(n.inputChangeSelector)){i=r.data("method");s=r.data("url");o=r.serialize();if(r.data("params"))o=o+"&"+r.data("params")}else if(r.is(n.buttonClickSelector)){i=r.data("method")||"get";s=r.data("url");o=r.serialize();if(r.data("params"))o=o+"&"+r.data("params")}else{i=r.data("method");s=n.href(r);o=r.data("params")||null}c={type:i||"GET",data:o,dataType:l,beforeSend:function(e,i){if(i.dataType===t){e.setRequestHeader("accept","*/*;q=0.5, "+i.accepts.script)}if(n.fire(r,"ajax:beforeSend",[e,i])){r.trigger("ajax:send",e)}else{return false}},success:function(e,t,n){r.trigger("ajax:success",[e,t,n])},complete:function(e,t){r.trigger("ajax:complete",[e,t])},error:function(e,t,n){r.trigger("ajax:error",[e,t,n])},crossDomain:a};if(f){c.xhrFields={withCredentials:f}}if(s){c.url=s}return n.ajax(c)}else{return false}},handleMethod:function(r){var i=n.href(r),s=r.data("method"),o=r.attr("target"),u=e("meta[name=csrf-token]").attr("content"),a=e("meta[name=csrf-param]").attr("content"),f=e('<form method="post" action="'+i+'"></form>'),l='<input name="_method" value="'+s+'" type="hidden" />';if(a!==t&&u!==t){l+='<input name="'+a+'" value="'+u+'" type="hidden" />'}if(o){f.attr("target",o)}f.hide().append(l).appendTo("body");f.submit()},formElements:function(t,n){return t.is("form")?e(t[0].elements).filter(n):t.find(n)},disableFormElements:function(t){n.formElements(t,n.disableSelector).each(function(){n.disableFormElement(e(this))})},disableFormElement:function(e){var t=e.is("button")?"html":"val";e.data("ujs:enable-with",e[t]());e[t](e.data("disable-with"));e.prop("disabled",true)},enableFormElements:function(t){n.formElements(t,n.enableSelector).each(function(){n.enableFormElement(e(this))})},enableFormElement:function(e){var t=e.is("button")?"html":"val";if(e.data("ujs:enable-with"))e[t](e.data("ujs:enable-with"));e.prop("disabled",false)},allowAction:function(e){var t=e.data("confirm"),r=false,i;if(!t){return true}if(n.fire(e,"confirm")){r=n.confirm(t);i=n.fire(e,"confirm:complete",[r])}return r&&i},blankInputs:function(t,n,r){var i=e(),s,o,u=n||"input,textarea",a=t.find(u);a.each(function(){s=e(this);o=s.is("input[type=checkbox],input[type=radio]")?s.is(":checked"):s.val();if(!o===!r){if(s.is("input[type=radio]")&&a.filter('input[type=radio]:checked[name="'+s.attr("name")+'"]').length){return true}i=i.add(s)}});return i.length?i:false},nonBlankInputs:function(e,t){return n.blankInputs(e,t,true)},stopEverything:function(t){e(t.target).trigger("ujs:everythingStopped");t.stopImmediatePropagation();return false},disableElement:function(e){e.data("ujs:enable-with",e.html());e.html(e.data("disable-with"));e.bind("click.railsDisable",function(e){return n.stopEverything(e)})},enableElement:function(e){if(e.data("ujs:enable-with")!==t){e.html(e.data("ujs:enable-with"));e.removeData("ujs:enable-with")}e.unbind("click.railsDisable")}};if(n.fire(r,"rails:attachBindings")){e.ajaxPrefilter(function(e,t,r){if(!e.crossDomain){n.CSRFProtection(r)}});r.delegate(n.linkDisableSelector,"ajax:complete",function(){n.enableElement(e(this))});r.delegate(n.buttonDisableSelector,"ajax:complete",function(){n.enableFormElement(e(this))});r.delegate(n.linkClickSelector,"click.rails",function(r){var i=e(this),s=i.data("method"),o=i.data("params"),u=r.metaKey||r.ctrlKey;if(!n.allowAction(i))return n.stopEverything(r);if(!u&&i.is(n.linkDisableSelector))n.disableElement(i);if(i.data("remote")!==t){if(u&&(!s||s==="GET")&&!o){return true}var a=n.handleRemote(i);if(a===false){n.enableElement(i)}else{a.error(function(){n.enableElement(i)})}return false}else if(i.data("method")){n.handleMethod(i);return false}});r.delegate(n.buttonClickSelector,"click.rails",function(t){var r=e(this);if(!n.allowAction(r))return n.stopEverything(t);if(r.is(n.buttonDisableSelector))n.disableFormElement(r);var i=n.handleRemote(r);if(i===false){n.enableFormElement(r)}else{i.error(function(){n.enableFormElement(r)})}return false});r.delegate(n.inputChangeSelector,"change.rails",function(t){var r=e(this);if(!n.allowAction(r))return n.stopEverything(t);n.handleRemote(r);return false});r.delegate(n.formSubmitSelector,"submit.rails",function(r){var i=e(this),s=i.data("remote")!==t,o,u;if(!n.allowAction(i))return n.stopEverything(r);if(i.attr("novalidate")==t){o=n.blankInputs(i,n.requiredInputSelector);if(o&&n.fire(i,"ajax:aborted:required",[o])){return n.stopEverything(r)}}if(s){u=n.nonBlankInputs(i,n.fileInputSelector);if(u){setTimeout(function(){n.disableFormElements(i)},13);var a=n.fire(i,"ajax:aborted:file",[u]);if(!a){setTimeout(function(){n.enableFormElements(i)},13)}return a}n.handleRemote(i);return false}else{setTimeout(function(){n.disableFormElements(i)},13)}});r.delegate(n.formInputClickSelector,"click.rails",function(t){var r=e(this);if(!n.allowAction(r))return n.stopEverything(t);var i=r.attr("name"),s=i?{name:i,value:r.val()}:null;r.closest("form").data("ujs:submit-button",s)});r.delegate(n.formSubmitSelector,"ajax:send.rails",function(t){if(this==t.target)n.disableFormElements(e(this))});r.delegate(n.formSubmitSelector,"ajax:complete.rails",function(t){if(this==t.target)n.enableFormElements(e(this))});e(function(){n.refreshCSRFTokens()})}})(jQuery)
    </script>
    <script>

        jQuery(document).ready(function($){

            let table = $("table").DataTable({
                pageLength: 25,
                createdRow: function(row, data, index) {
                    $('.editable').editable();
                    triggerEditableHidden();
                }
            });

            setTimeout(function(){
                $(".alert").hide();
            }, 3000);

            $.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    settings.data += "&_token=<?= csrf_token() ?>";
                }
            });

            triggerEditableHidden();

            $('.group-select').on('change', function(){
                var group = $(this).val();
                if (group) {
                    window.location.href = '<?= action([\Barryvdh\TranslationManager\Controller::class, 'getView']) ?>/'+$(this).val();
                } else {
                    window.location.href = '<?= action([\Barryvdh\TranslationManager\Controller::class, 'getIndex']) ?>';
                }
            });

            $("a.delete-key").click(function(event){
                event.preventDefault();
                var row = $(this).closest('tr');
                var url = $(this).attr('href');
                var id = row.attr('id');
                if ($.rails.allowAction($(this))) {
                    $.post( url, {id: id}, function(){
                        row.remove();
                    } );
                }

                return $.rails.stopEverything(event);
            });

            $('.form-import').on('ajax:success', function (e, data) {
                $('div.success-import strong.counter').text(data.counter);
                $('div.success-import').slideDown();
            });

            $('.form-find').on('ajax:success', function (e, data) {
                $('div.success-find strong.counter').text(data.counter);
                $('div.success-find').slideDown();
            });

            $('.form-publish').on('ajax:success', function (e, data) {
                $('div.success-publish').slideDown();
                $('a').removeClass('status-1');
            });

        })

        function triggerEditableHidden() {
            $('.editable').editable().on('hidden', function(e, reason){
                var locale = $(this).data('locale');
                if(reason === 'save'){
                    $(this).removeClass('status-0').addClass('status-1');
                }
                if(reason === 'save' || reason === 'nochange') {
                    var $next = $(this).closest('tr').next().find('.editable.locale-'+locale);
                    setTimeout(function() {
                        $next.editable('show');
                    }, 300);
                }

                let changedStatus = $(".status-1").length;
                $("#numChanged").text(changedStatus);

            });
        }

        function publishTranslations(t) {

            let publish_translation_link = '{{ action([\Barryvdh\TranslationManager\Controller::class, 'postPublish'], $group) }}';
            console.log(publish_translation_link);
            let self = $(t);

            $.confirm({
                title: 'Publish Translations',
                content: 'Are you sure you want to publish the translations? This will overwrite existing language files.',
                type: 'blue',
                typeAnimated: true,
                buttons: {
                    tryAgain: {
                        text: 'Ok',
                        btnClass: 'btn-blue',
                        action: function(){
                            self.attr('disabled', true).text('Publishing..');
                            $.post(publish_translation_link)
                                .then(function(response){
                                    $(".success-publish").show();
                                    self.attr('disabled', false).text('Publish translations');
                                    $(".editable").removeClass('status-1').addClass('status-0');

                                    setTimeout(function(){
                                        $(".alert").hide();
                                    }, 3000);
                                })
                        }
                    },
                    close: function () {
                    }
                }
            })
        }

        // check every second
        setInterval(function() {
            if ($(".success-publish").is(":visible")) {
                $("#numChanged").text(0);
            }
        }, 1000);
    </script>
@stop