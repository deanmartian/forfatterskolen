@extends('backend.layout')

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
                                    <td>{!! str_limit($email->message, 100) !!}</td>
                                    <td>
                                        @if(\App\Http\AdminHelpers::isDate($email->delay))
                                            {{date_format(date_create($email->delay), 'M d, Y')}}
                                        @else
                                            {{$email->delay}} days delay
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-xs editEmailBtn" data-toggle="modal"
                                        data-target="#emailModal" data-fields="{{ json_encode($email) }}"
                                        data-action="{{ route('admin.email-out.update', ['course_id' => $course->id, 'id' => $email->id]) }}">
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
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{csrf_field()}}

                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" class="form-control" name="subject" required>
                        </div>

                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" cols="30" rows="10" class="form-control" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.delay-type') }}</label>
                            <select class="form-control" id="lesson-delay-toggle" name="delay_selector">
                                <option value="days" selected>Days</option>
                                <option value="date">Date</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.delay') }}</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="delay" id="lesson-delay" min="0" required>
                                <span class="input-group-addon lesson-delay-text" id="basic-addon2">
                                    days
						  	    </span>
                            </div>
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
            let fields = emailModalForm.find('.form-control');
            $("[name=_method]").remove();
            $.each(fields, function (k, v) {
               if ($(v).attr('name') === 'delay_selector') {
                   let input_group = emailModalForm.find(".input-group");
                   prependDelayInput(input_group, 'number', 'days');
               } else {
                   $(v).val('');
               }
            });
        });

        $(".editEmailBtn").click(function(){
            let fields = $(this).data('fields');
            let action = $(this).data('action');
            emailModal.find('.modal-title').text(translations.edit_email);
            emailModalForm.attr('action', action);
            emailModalForm.prepend('<input type="hidden" name="_method" value="PUT">');
            $.each(fields, function(field, value) {
               emailModalForm.find('[name='+field+']').val(value);
               if (field === 'delay') {
                   let input_group = emailModalForm.find(".input-group");
                   if (value.indexOf('-') >= 0) {
                       prependDelayInput(input_group, 'date', value);
                   } else {
                       prependDelayInput(input_group, 'number', value);
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