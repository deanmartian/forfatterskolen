@extends('backend.layout')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Email Out &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    @include('backend.course.partials.toolbar')
    <?php 
        $currentEmails = $course->emailOutActive()->get();
        $archives = $course->emailOutArchive()->get();
    ?>

    <div class="course-container">
        @include('backend.partials.course_submenu')
        <div class="col-sm-12 col-md-10 sub-right-content">
            <div class="col-sm-12 col-md-12">

                <ul class="nav nav-tabs margin-top">
                    <li class="active"><a href="#current" data-toggle="tab">Active</a></li>
                    <li><a href="#archive" data-toggle="tab">Archive</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade in active margin-top" id="current" role="tabpanel">
                        <button class="btn btn-primary margin-bottom addEmailBtn loadScriptButton" 
                        data-target="#emailModal" data-toggle="modal"
                        data-action="{{ route('admin.email-out.store', $course->id) }}">
                            + {{ trans('site.add-email') }}
                        </button>
                        
                        @include('backend.course.partials.email-out-table', with(['emails' => $currentEmails]))
                    </div> <!-- end tab-pane -->

                    <div class="tab-pane fade margin-top" id="archive" role="tabpanel">
                        @include('backend.course.partials.email-out-table', with(['emails' => $archives]))
                    </div>
                </div>

            </div> <!-- col-sm-12 col-md-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content -->

        <div class="clearfix"></div>
    </div> <!-- end course-container -->

    <!-- send email to learners-->
    <div id="sendEmailModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Send Email</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <p>Do you want to send email to learners?</p>
                        <button type="submit" class="btn btn-success pull-right margin-top">
                            {{ trans('site.send') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
                            <textarea name="message" cols="30" rows="10" class="form-control tinymce" id="message"></textarea>
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
                            <label>For Free Course</label>
                            <input type="checkbox" name="for_free_course" style="top: 4px; position: relative">
                        </div>

                        <div class="form-group">
                            <label>Welcome Email</label> <br>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes"
                                   data-off="No"
                                   name="send_immediately" data-width="84">
                        </div>

                        <div class="form-group">
                            <label>Send to learners with no course</label> <br>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes"
                                   data-off="No"
                                   name="send_to_learners_no_course" data-width="84">
                        </div>

                        <div class="form-group">
                            <label>Send to learners with unpaid pay later</label> <br>
                            <input type="checkbox" data-toggle="toggle" data-on="Yes"
                                   data-off="No"
                                   name="send_to_learners_with_unpaid_pay_later" data-width="84">
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.allowed-package') }}</label>
                            @foreach($course->packages as $package)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{ $package->id }}" name="allowed_package[]">
                                    <label class="form-check-label" {{-- for="{{ $package->variation }}" --}}>
                                        {{ $package->variation }}
                                    </label>
                                </div>
                            @endforeach
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
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>
        let translations = {
            add_email: '{{ trans('site.add-email') }}',
            edit_email: '{{ trans('site.edit-email') }}'
        };

        let emailModal = $("#emailModal");
        let emailModalForm = emailModal.find('form');

        $(".addEmailBtn").click(function(){
            let action = $(this).data('action');
            emailModal.find('.modal-title').text(translations.add_email);
            emailModalForm.attr('action', action);
            emailModalForm.find('.file-display').addClass('hide').empty();
            emailModalForm.find('input[name="allowed_package[]"]').prop('checked', true);
            let fields = emailModalForm.find('.form-control');
            $("[name=_method]").remove();
            $.each(fields, function (k, v) {
               if ($(v).attr('name') === 'delay_selector') {
                   let input_group = emailModalForm.find(".input-group");
                   prependDelayInput(input_group, 'number', 'days');
               } else {
                   $(v).val('');
                   $(tinymce.get('message').getBody()).html('');
                   /* setTimeout(() => {
                        setEditorContent('message', '');
                   }, 500); */
               }
            });

            $("[name=for_free_course]").attr('checked', false);
        });

        $(".sendEmailBtn").click(function(){
            let action = $(this).data('action');
            let modal = $("#sendEmailModal");
            let form = modal.find('form');
            form.attr('action', action);
        });

        $(".editEmailBtn").click(function(){
            let fields = $(this).data('fields');
            let action = $(this).data('action');
            let filename = $(this).data('filename');
            let fileloc = $(this).data('fileloc');
            let allowed_package = JSON.parse(fields.allowed_package);
            if (!allowed_package) {
                emailModalForm.find('input[name="allowed_package[]"]').prop('checked', true);
            } else {
                $.each(emailModalForm.find('input[name="allowed_package[]"]'), function(k, v) {
                    if (allowed_package.includes($(v).val())) {
                        $("input:checkbox[value='"+ $(v).val() +"']").prop("checked", true);
                    } else {
                        $("input:checkbox[value='"+ $(v).val() +"']").prop("checked", false);
                    }
                });
            }

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
                   /* setTimeout(() => {
                        setEditorContent('message', value);
                   }, 500); */
               }

               if (field === 'for_free_course') {
                   emailModalForm.find('[name='+field+']').attr('checked', false);
                    if (value === 1) {
                        emailModalForm.find('[name='+field+']').attr('checked', true);
                    }
               }

               if (field === 'send_immediately') {
                    emailModalForm.find('[name='+field+']').bootstrapToggle('off');
                if (value) {
                    emailModalForm.find('[name='+field+']').bootstrapToggle('on');
                }
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