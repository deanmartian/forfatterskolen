@extends('backend.layout')

@section('title')
    <title>Shareable Courses &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Shareable Courses</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a class="btn btn-success margin-top createShareableBtn" href="#shareModal" data-toggle="modal"
        data-action="{{ route('admin.shareable-course.store') }}">Create</a>
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <td>Id</td>
                        <td>Course</td>
                        <td>Package</td>
                        <td width="400">Shareable Link</td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courseShared as $shared)
                        <tr>
                            <td>{{ $shared->id }}</td>
                            <td>
                                <a href="{{ route('admin.course.show', $shared->course->id) }}">
                                    {{ $shared->course->title }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.course.show', $shared->course->id).'?section=packages' }}">
                                    {{ $shared->package->variation }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('front.home').'/course/share/'.$shared->hash.'/checkout' }}" target="_blank">
                                    {{ route('front.home').'/course/share/'.$shared->hash.'/checkout' }}
                                </a>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-xs editShareableBtn" data-toggle="modal"
                                        data-target="#shareModal" data-action="{{ route('admin.shareable-course.update', [$shared->id]) }}"
                                        data-fields="{{ json_encode($shared) }}"><i class="fa fa-pencil"></i></button>
                                <button type="button" class="btn btn-danger btn-xs deleteDiscountBtn" data-toggle="modal"
                                        data-target="#deleteShareModal" data-action="{{ route('admin.shareable-course.destroy',
                                        [$shared->id]) }}"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="shareModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Course</label>
                            <select name="course_id" class="form-control" required>
                                <option value="" disabled selected>Select Course</option>
                                @foreach(\App\Course::all() as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Package</label>
                            <select name="package_id" class="form-control" required></select>
                        </div>

                        <div class="text-right">
                            <button class="btn btn-default" type="button" data-dismiss="modal">Cancel</button>
                            <button class="btn btn-success" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteShareModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete discount</h4>
                </div>
                <div class="modal-body">
                    Are you sure to delete this shared course? <br>
                    Warning: This cannot be undone.
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        let modal = $("#shareModal");
        let package_id = 0;

        $("[name=course_id]").change(function(){
            let course_id = parseInt($(this).val());
            $("[name=package_id]").empty();
            if (course_id) {
                $.get('/shareable-course/get-package/'+course_id)
                    .done(function(data) {
                        $.each(data, function(k, v){
                            let isSelected = '';
                            if (package_id === v.id) {
                                isSelected = 'selected'
                            }
                            let option = "<option value='"+v.id+"' "+isSelected+">"+v.variation+"</option>";
                            $("[name=package_id]").append(option);
                        });
                    });
            }
        });

        $(".createShareableBtn").click(function(){
            let action = $(this).data('action');
            modal.find('.modal-title').text('Create Shareable Course');
            modal.find('form').attr('action', action);
            modal.find('form').find('[name=_method]').remove();
            $("[name=course_id]").val('').trigger('change');
        });

        $(".editShareableBtn").click(function(){
           let action = $(this).data('action');
           let fields = $(this).data('fields');
            modal.find('form').attr('action', action);
            modal.find('form').prepend("<input type='hidden' name='_method' value='PUT'>");
            $("[name=course_id]").val(fields.course_id).trigger('change');
            package_id = fields.package_id;
        });

        $('.deleteDiscountBtn').click(function(){
            let form = $('#deleteShareModal form');
            let action = $(this).data('action');
            form.attr('action', action)
        });
    </script>
@stop
