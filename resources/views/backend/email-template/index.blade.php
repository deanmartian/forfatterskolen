@extends('backend.layout')

@section('title')
    <title>Email Template &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> {{ trans('site.email-template') }}</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12 margin-top">
        <button class="btn btn-success loadScriptButton addTemplateBtn" data-toggle="modal" data-target="#templateModal" 
        data-courses = "{{ json_encode($courses) }}"
                data-action="{{ route('admin.manuscript.add_email_template') }}">
            Add Template
        </button>

        <div class="table-users table-responsive margin-top">
            <table class="table">
                <thead>
                    <tr>
                        <th>Identifier</th>
                        <th>{{ trans('site.subject') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                        <tr>
                            <td>
                                {{ $template->page_name }}
                            </td>
                            <td>
                                {{ $template->subject }}
                            </td>
                            <td>
                                <button class="btn btn-primary btn-xs loadScriptButton editTemplateBtn"
                                        data-toggle="modal"
                                        data-target="#templateModal"
                                        data-action="{{ route('admin.manuscript.edit_email_template', $template->id) }}"
                                        data-fields="{{ json_encode($template) }}"
                                        data-courses = "{{ json_encode($courses) }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="templateModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Email Template
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>
                                Identifier
                            </label>
                            <input type="text" name="page_name" class="form-control">
                        </div>

                        <div class="form-group" id="is_course_for_sale_div">
                            <input type="checkbox" name="is_course_for_sale" id="is_course_for_sale"> {{ trans('site.course-for-sale') }}</input>
                        </div>

                        <div class="form-group" id="courses">

                            <label>Course</label>
                            <select class="form-control select2" id="course-drpdwn" name="course_id">
                            </select>
                            <br>
                            <!-- <div id="group-course-multi-invioce-email-div"> 
                                <input type="checkbox" name="group-course-multi-invioce-email" id="group-course-multi-invioce-email"> {{ trans('site.group-course-multi-invioce-email') }}</input>
                            </div> -->
                        </div>

                        <div class="form-group">
                            <input type="checkbox" name="is_assignment_manu_feedback" /> 
                            Is Assignmet Feedback?
                        </div>

                        <div class="form-group">
                            <label>
                                {{ trans('site.from') }}
                            </label>
                            <input type="email" name="from_email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>
                                {{ trans('site.subject') }}
                            </label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.body') }}</label>
                            <textarea name="email_content" cols="30" rows="10" class="form-control tinymce"></textarea>
                        </div>
                        <div class="clearfix"></div>
                        <button type="submit" class="btn btn-primary pull-right margin-top">
                            {{ trans('site.save') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $('#courses').hide()
        $(".addTemplateBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#templateModal');
            modal.find('form').attr('action', action);
            modal.find('form').find('[name=_method]').remove();

            modal.find('[name=page_name]').attr('disabled', false);
            modal.find('.form-control').val('');
            tinyMCE.activeEditor.setContent('');

            let courses = $(this).data('courses');

            $('#course-drpdwn').append('<option>-- select course --</option>');
            courses.forEach(function (item, index){
                $('#course-drpdwn').append('<option data-type=' + item.type + ' value=' + item.id + '>' + item.title + '</option>');
            })

            $('#is_course_for_sale').prop('checked', false)
            $('#is_course_for_sale_div').show()
        });

        $(".editTemplateBtn").click(function() {
            let action = $(this).data('action');
            let modal = $('#templateModal');
            let fields = $(this).data('fields');

            modal.find('form').prepend('<input type="hidden" name="_method" value="PUT">');

            modal.find('form').attr('action', action);
            modal.find('[name=page_name]').val(fields.page_name).attr('disabled', true);
            modal.find('[name=from_email]').val(fields.from_email);
            modal.find('[name=subject]').val(fields.subject);
            tinyMCE.activeEditor.setContent(fields.email_content);

            $('#is_course_for_sale').prop('checked', false)
            $('#is_course_for_sale_div').hide()
            $('#courses').hide();

            modal.find('[name=is_assignment_manu_feedback]').prop('checked', false);
            if (fields.is_assignment_manu_feedback) {
                modal.find('[name=is_assignment_manu_feedback]').prop('checked', true);
            }
        });

        $('#is_course_for_sale').change(function() {
            if(this.checked) {
                $('#courses').show()
                $('#course_id').prop('required', true)
                $('[name=page_name]').prop('readonly', true)
            }else{
                $('#courses').hide()
                $('#course_id').prop('required', false)
                $('[name=page_name]').prop('readonly', false)

                let modal = $('#templateModal');
                modal.find('[name=page_name]').val('')
            }
        });

        $('#course-drpdwn').change(function(){
            let modal = $('#templateModal');
            let type = null;
            if($('[name=course_id] option:selected').data('type') == 'Group'){
                $('#group-course-multi-invioce-email-div').show()
                type = ':GROUP'
            }else{
                $('#group-course-multi-invioce-email').prop('checked', false)
                $('#group-course-multi-invioce-email-div').hide()
                type = ':SINGLE'
            }
            modal.find('[name=page_name]').val('COURSE-FOR-SALE:' + $('[name=course_id] option:selected').text() + type)
        });

        $('#group-course-multi-invioce-email').change(function(){
            let modal = $('#templateModal');
            if(this.checked){
                modal.find('[name=page_name]').val('COURSE-FOR-SALE:' + $('[name=course_id] option:selected').text() + ':GROUP-MULTI-INVOICE')
            }else{
                modal.find('[name=page_name]').val('COURSE-FOR-SALE:' + $('[name=course_id] option:selected').text() + ':GROUP')
            }
        });

    </script>
@stop