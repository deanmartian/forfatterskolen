@extends('backend.layout')

@section('title')
<title>{{$course->title}} &rsaquo; Course &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

@include('backend.course.partials.toolbar')
@php
    $applications = \App\Http\AdminHelpers::courseApplications($course->id)->paginate(20);
@endphp
<div class="course-container">
    @include('backend.partials.course_submenu')

	<div class="col-sm-12 col-md-10 sub-right-content">
        <div class="col-sm-12 col-md-12">
            <div class="table-responsive">
                <table class="table table-side-bordered table-white">
                    <thead>
                        <tr>
                            <th>{{ trans_choice('site.learners', 1) }}</th>
                            <th>{{ trans_choice('site.packages', 1) }}</th>
                            <th>Manuscript</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $application)
                            <tr>
                                <td>
                                    <a href="{{route('admin.learner.show', $application->user->id)}}">
                                        {{$application->user->full_name}}
                                    </a>
                                </td>
                                <td>
                                    {{ $application->package->variation }}
                                </td>
                                <td>
                                    {!! $application->file_link !!}
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#viewApplicationModal"
                                        onclick="viewApplication({{ $application->id }})">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <a href="{{ route('admin.course.application.download', $application->id) }}" 
                                        class="btn btn-success btn-sm">
                                        <i class="fa fa-download"></i>
                                    </a>

                                    @if (!$application->approved_date)
                                        <button class="btn btn-warning btn-sm approveBtn" data-toggle="modal" 
                                        data-target="#approveModal" 
                                        data-action="{{ route('admin.course.application.approve',$application->id) }}">
                                            <i class="fa fa-check"></i>
                                        </button>

                                        <button class="btn btn-danger btn-sm deleteBtn" data-toggle="modal" 
                                        data-target="#deleteModal"
                                        data-action="{{ route('admin.course.application.delete',$application->id) }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pull-right">{!! $applications->appends(Request::all())->render() !!}</div>
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="clearfix"></div>
</div>

<div id="viewApplicationModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Application Details</h4>
        </div>
        <div class="modal-body">
            
        </div>
      </div>
  
    </div>
  </div>

  <div id="approveModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
  
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Approve Application</h4>
        </div>
        <div class="modal-body">
            <form action="" method="POST" onsubmit="disableSubmit(this)">
                @csrf

                <p>
                    Are you sure you want to approve this application?
                </p>

                <button type="submit" class="btn btn-primary pull-right">
                    Approve
                </button>
            </form>

            <div class="clearfix"></div>
        </div>
      </div>
  
    </div>
  </div>

  <div id="deleteModal" class="modal new-global-modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3 class="modal-title">
					Delete Application
				</h3>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
                    {{ method_field('DELETE') }}

                    <p>
                        Are you sure you want to delete this application?
                    </p>

					<button type="submit" class="btn btn-danger pull-right">
						{{ trans('site.learner.delete') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<script>
    $(".approveBtn").click(function() {
        let modal = $("#approveModal");
        let action = $(this).data('action');
        
        modal.find('form').attr('action', action);
    });

    $(".deleteBtn").click(function() {
        let modal = $("#deleteModal");
        let action = $(this).data('action');
        
        modal.find('form').attr('action', action);
    });

    function viewApplication(id) {
        $.ajax({
            type: 'GET',
            url: '/course/application/' + id + '/details',
            success: function(response) {
                $("#viewApplicationModal").find('.modal-body').html(response);
            }
        });
    }
</script>
@stop