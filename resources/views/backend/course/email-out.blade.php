@extends('backend.layout')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
@stop

@section('title')
    <title>Email Out &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    @include('backend.course.partials.toolbar')

    <div class="course-container">
        @include('backend.partials.course_submenu')
        <div class="col-sm-12 col-md-10 sub-right-content">
            <div class="col-sm-12 col-md-12">

                <button class="btn btn-primary margin-bottom addEmailBtn" data-target="#emailModal" data-toggle="modal"
                data-action="{{ route('admin.email-out.store', $course->id) }}">
                    + {{ trans('site.add-email') }}
                </button>
                <div class="table-responsive">
                    <table class="table table-side-bordered table-white">
                        <thead>
                        <tr>
                            <th>{{ trans('site.subject') }}</th>
                            <th width="500">{{ trans('site.message') }}</th>
                            <th>{{ trans('site.availability') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($course->emailOut as $email)
                                <tr>
                                    <td>{{ $email->subject }}</td>
                                    <td>{!! str_limit(strip_tags($email->message), 100) !!}</td>
                                    <td>
                                        @if(\App\Http\AdminHelpers::isDate($email->delay))
                                            {{date_format(date_create($email->delay), 'M d, Y')}}
                                        @else
                                            {{$email->delay}} {{ trans('site.days-delay') }}
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-xs editEmailBtn" data-toggle="modal"
                                        data-target="#emailModal" data-fields="{{ json_encode($email) }}"
                                        data-action="{{ route('admin.email-out.update', ['course_id' => $course->id, 'id' => $email->id]) }}"
                                        data-filename="{{ \App\Http\AdminHelpers::extractFileName($email->attachment) }}"
                                        data-fileloc="{{ asset($email->attachment) }}">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <button class="btn btn-danger btn-xs deleteEmailBtn" data-toggle="modal" data-target="#deleteEmailModal"
                                        data-action="{{ route('admin.email-out.destroy', ['course_id' => $course->id, 'id' => $email->id]) }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div> <!-- col-sm-12 col-md-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content -->

        <div class="clearfix"></div>
    </div> <!-- end course-container -->

    <!-- Remove Learner Modal -->
    <div id="emailModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="form-group">
                            <label>{{ trans('site.subject') }}</label>
                            <input type="text" class="form-control" name="subject" required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.message') }}</label>
                            <textarea name="message" cols="30" rows="10" class="form-control editor"></textarea>
                        </div>

                        <div class="form-group">
                            <label style="display: block">{{ trans('site.from') }}</label>
                            <input type="text" class="form-control" placeholder="{{ trans('site.name') }}" style="width: 49%; display: inline;"
                                name="from_name">
                            <input type="email" class="form-control" placeholder="{{ trans('site.front.form.email') }}" style="width: 49%; display: inline;"
                                name="from_email">
                        </div>

                        <div class="form-group">
                            <label>{{ trans_choice('site.attachments', 1) }}</label>
                            <input type="file" class="form-control" name="attachment"
                                   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/msword,
                               application/pdf,
                               application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            <p class="file-display hide text-muted text-center">
                            </p>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.delay-type') }}</label>
                            <select class="form-control" id="lesson-delay-toggle" name="delay_selector">
                                <option value="days" selected>{{ trans('site.days') }}</option>
                                <option value="date">{{ trans('site.date') }}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.delay') }}</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="delay" id="lesson-delay" min="0" required>
                                <span class="input-group-addon lesson-delay-text" id="basic-addon2">
                                    {{ strtolower(trans('site.days')) }}
						  	    </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.send-test-to') }}</label>
                            <input type="email" class="form-control" name="send_to">
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{ trans('site.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div id="deleteEmailModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.delete') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <p>{{ trans('site.delete-item-question') }}</p>
                        <button type="submit" class="btn btn-danger pull-right margin-top">{{ trans('site.delete') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script>
        let translations = {
            add_email: '{{ trans('site.add-email') }}',
            edit_email: '{{ trans('site.edit-email') }}'
        };

        let emailModal = $("#emailModal");
        let emailModalForm = emailModal.find('form');

        // tinymce editor config and intitalization
        let editor_config = {
            path_absolute: "{{ URL::to('/') }}",
            height: '15em',
            selector: '.editor',
            plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern'],
            toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript superscript | forecolor backcolor | ',
            toolbar2: 'link | alignleft aligncenter alignright ' +
            'alignjustify  | removeformat',
            toolbar3:'undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media code | print fullscreen',
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
            }
        };
        tinymce.init(editor_config);

        $(".addEmailBtn").click(function(){
            let action = $(this).data('action');
            emailModal.find('.modal-title').text(translations.add_email);
            emailModalForm.attr('action', action);
            emailModalForm.find('.file-display').addClass('hide').empty();
            let fields = emailModalForm.find('.form-control');
            $("[name=_method]").remove();
            $.each(fields, function (k, v) {
               if ($(v).attr('name') === 'delay_selector') {
                   let input_group = emailModalForm.find(".input-group");
                   prependDelayInput(input_group, 'number', 'days');
               } else {
                   $(v).val('');
                   $(tinymce.get('message').getBody()).html('');
               }
            });
        });

        $(".editEmailBtn").click(function(){
            let fields = $(this).data('fields');
            let action = $(this).data('action');
            let filename = $(this).data('filename');
            let fileloc = $(this).data('fileloc');
            emailModal.find('.modal-title').text(translations.edit_email);
            emailModalForm.attr('action', action);
            emailModalForm.prepend('<input type="hidden" name="_method" value="PUT">');
            emailModalForm.find('.file-display').removeClass('hide').empty().append('<a href="'+fileloc+'" download>'+filename+'</a>');
            $.each(fields, function(field, value) {
                if (field !== 'attachment') {
                    emailModalForm.find('[name='+field+']').val(value);
                }

               if (field === 'delay') {
                   let input_group = emailModalForm.find(".input-group");
                   if (value.indexOf('-') >= 0) {
                       prependDelayInput(input_group, 'date', value);
                   } else {
                       prependDelayInput(input_group, 'number', value);
                   }
               }

               if (field === 'message') {
                   $(tinymce.get('message').getBody()).html(value);
               }
            });
        });

        $(".deleteEmailBtn").click(function(){
            let action = $(this).data('action');
            $("#deleteEmailModal").find('form').attr('action', action);
        });

        function prependDelayInput(parent, type, value) {
            parent.find('.form-control').remove();
            parent.prepend('<input type="'+type+'" class="form-control" name="delay" id="lesson-delay" min="0" required>');
            emailModalForm.find('[name=delay]').val(value);
            $("#lesson-delay-toggle").val(type === 'date' ? 'date' : 'days');
            $("#basic-addon2").text(type === 'date' ? 'date' : 'days');
        }
    </script>

@stop