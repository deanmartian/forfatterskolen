@extends('editor.layout')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{asset('css/editor.css')}}">
	<style>
		.panel {
			overflow-x: auto;
		}
	</style>
@stop

@section('content')
<div class="col-sm-12 dashboard-left">
	<div class="row">
		<div class="col-sm-12">

            <!-- My assigned manuscripts -->
             <!-- Shop manuscripts -->
			<div class="row">
				<div class="col-sm-6">
					<div class="panel panel-default custom-height-1">
						<div class="panel-heading">
                            <div class="navbar-form navbar-left">
                                <h4 class="dib">
                                    {{ trans('site.personal-assignment') }}
                                </h4>
                            </div>
                            <div class="navbar-form navbar-right">
                                <div class="form-group">
                                    <form role="search" method="get" action="">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="search_personal_assignment" placeholder="{{ trans('site.search-learner-id') }}..">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
						</div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manuscripts', 1) }}</th>
                                <th>{{ trans('site.feedback-sent') }}</th>
								<th>{{ trans('site.learner-id') }}</th>
                                <th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($assignedAssignmentManuscripts as $assignedManuscript)
                                <?php $extension = explode('.', basename($assignedManuscript->filename)); ?>
								<tr>
									<td>
										
										<a href="{{ $assignedManuscript->filename }}"
										   download>
										   <i class="fa fa-download" aria-hidden="true"></i>
										</a>
                                        &nbsp;
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../..{{ $assignedManuscript->filename }}">
												{{ basename($assignedManuscript->filename) }}
											</a>
										@elseif( end($extension) == 'docx' || end($extension) == 'doc' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$assignedManuscript->filename}}">
												{{ basename($assignedManuscript->filename) }}
											</a>
										@endif
									</td>
                                    <td>
                                        @if($assignedManuscript->noGroupFeedbacks->first())
                                            {{$assignedManuscript->noGroupFeedbacks->first()->updated_at}}
                                        @else
                                        -
                                        @endif
                                    </td>
									<td>{{ $assignedManuscript->user->id }}</td>
                                    <td>
                                        @if($assignedManuscript->noGroupFeedbacks->first())
                                            <a href="" class="btn btn-primary btn-xs personalAssignmentShowFeedbackBtn"
                                                data-target="#personalAssignmentShowFeedbackModal"
                                                data-toggle="modal"
                                                data-id = "{{$assignedManuscript->id}}"
                                                data-feedback_file = "{{$assignedManuscript->noGroupFeedbacks->first()->filename}}"
                                                data-feedback_date = "{{$assignedManuscript->noGroupFeedbacks->first()->created_at}}"
                                                data-feedback_grade = "{{$assignedManuscript->grade}}">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;&nbsp;
                                                {{ trans_choice('site.feedbacks',1) }}
                                            </a>
                                        @else
                                            <i class="fa fa-exclamation-triangle" style="color: #dc3545;" aria-hidden="true">&nbsp;&nbsp;{{ trans('site.no-feedback-found') }}</i>
                                        @endif
                                    </td>
								</tr>
							@endforeach
							</tbody>
						</table>
                        <!-- page -->
                        <div class="pull-right">
                            {{$assignedAssignmentManuscripts->render()}}
                        </div>
					</div>
				</div>
                <div class="col-sm-6">
					<div class="panel panel-default custom-height-1">
						<div class="panel-heading">
                            <div class="navbar-form navbar-left">
                                <h4>{{ trans_choice('site.shop-manuscripts', 2) }}</h4>
                            </div>
                            <div class="navbar-form navbar-right">
                                <div class="form-group">
                                    <form role="search" method="get" action="">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="search_shop_manuscript" placeholder="{{ trans('site.search-learner-id') }}..">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manuscripts', 1) }}</th>
                                <th>{{ trans('site.feedback-sent') }}</th>
								<th>{{ trans('site.genre') }}</th>
								<th>{{ trans('site.learner-id') }}</th>
                                <th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($assigned_shop_manuscripts as $shopManuscript)
								@if( $shopManuscript->status != 'Started' & $shopManuscript->status != 'Pending' )
									<tr>
										<td>
                                            <a href="{{ route('editor.backend.download_shop_manuscript', $shopManuscript->id) }}">
                                                <i class="fa fa-download" aria-hidden="true"></i> 
                                            </a>&nbsp;
                                            {{$shopManuscript->shop_manuscript->title}}
                                        </td>
                                        <td>{{$shopManuscript->feedbacks->first()->updated_at}}</td>
										<td>
											@if($shopManuscript->genre > 0)
												{{ \App\Http\FrontendHelpers::assignmentType($shopManuscript->genre) }}
											@endif
										</td>
										<td>{{ $shopManuscript->user->id }}</td>
                                        <td>
                                        <?php

                                            $feedbackFile = implode(",",$shopManuscript->feedbacks->first()->filename);

                                        ?>
                                        <button class="btn btn-primary btn-xs shopManuscriptShowFeedbackBtn"
                                                data-target="#shopManuscriptShowFeedbackModal"
                                                data-toggle="modal"
                                                data-feedback_file = "{{$feedbackFile}}"
                                                data-feedback_notes = "{{$shopManuscript->feedbacks->first()->notes}}"
                                                data-feedback_grade = "{{$shopManuscript->grade}}"
                                                data-feedback_created_at = "{{$shopManuscript->feedbacks->first()->created_at}}"
                                                >
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;&nbsp;{{ trans_choice('site.feedbacks',1) }}
                                        </button>
                                        </td>
									</tr>
								@endif
							@endforeach
							</tbody>
						</table>
                        <!-- page -->
                        <div class="pull-right">
                            {{$assigned_shop_manuscripts->links()}}
                        </div>
					</div>
				</div>
			</div>
            
            
            <!-- My Assignments -->
            <!-- My coaching timer -->
			<div class="row">
				<div class="col-sm-6">
					<div class="panel panel-default custom-height-1">
						<div class="panel-heading">
                            <div class="navbar-form navbar-left">
                                <h4>{{ trans('site.my-assignments') }}</h4>
                            </div>
                            <div class="navbar-form navbar-right">
                                <div class="form-group">
                                    <form role="search" method="get" action="">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="search_my_assignments" placeholder="{{ trans('site.search-learner-id') }}..">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.courses', 1) }}</th>
                                <th>{{ trans('site.feedback-sent') }}</th>
								<th>{{ trans('site.learner-id') }}</th>
                                <th></th>
							</tr>
							</thead>
							<tbody>
							@foreach ($assignedAssignments as $assignedAssignment)
								<tr>
                                    <?php 
                                        $groupDetails = DB::SELECT("SELECT A.id as assignment_group_id, B.id AS assignment_group_learner_id FROM assignment_groups A JOIN assignment_group_learners B ON A.id = B.assignment_group_id AND B.user_id = $assignedAssignment->user_id WHERE A.assignment_id = $assignedAssignment->assignment_id");
                                        if($groupDetails){ // Means the course assignment belongs to a group
                                            $feedback = DB::SELECT("SELECT A.* FROM assignment_feedbacks A JOIN assignment_group_learners B ON A.assignment_group_learner_id = B.id WHERE B.user_id = $assignedAssignment->user_id AND A.assignment_group_learner_id = ".$groupDetails[0]->assignment_group_learner_id . " AND A.is_admin = 1");
                                        }
                                    ?>
									<td>
                                        <a href="{{ route('editor.backend.download_assigned_manuscript', $assignedAssignment->id) }}">
                                            <i class="fa fa-download" aria-hidden="true"></i> 
                                        </a>&nbsp;
										@if($assignedAssignment->assignment->course)
												{{ $assignedAssignment->assignment->course->title }}
										@else
												{{ $assignedAssignment->assignment->title }}
										@endif
									</td>
                                    <td>
                                        @if($groupDetails)
                                            {{$feedback[0]->updated_at}}
                                        @else
                                            {{$assignedAssignment->noGroupFeedbacks->first()->updated_at}}
                                        @endif
                                    </td>
									<td>{{ $assignedAssignment->user_id }}</td>
                                    <td>
                                        <?php 
                                            if($groupDetails){ // Means the course assignment belongs to a group
                                                echo '<button class="btn btn-primary btn-xs courseAssignmentShowFeedbackBtn"
                                                                data-target="#courseAssignmentShowFeedbackModal"
                                                                data-toggle="modal"
                                                                data-feedback_file = "'.$feedback[0]->filename.'"
                                                                data-feedback_grade = "'.$assignedAssignment->grade.'"
                                                                data-feedback_created_at = "'.$feedback[0]->created_at.'"
                                                        >
                                                            <i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;&nbsp;'.trans_choice('site.feedbacks',1).'
                                                        </button>';
                                            }else{
                                                echo '<button class="btn btn-primary btn-xs personalAssignmentShowFeedbackBtn"
															data-target="#personalAssignmentShowFeedbackModal"
															data-toggle="modal"
															data-feedback_file = "'.$assignedAssignment->noGroupFeedbacks->first()->filename.'"
															data-feedback_grade = "'.$assignedAssignment->grade.'"
                                                            data-feedback_date = "'.$assignedAssignment->noGroupFeedbacks->first()->created_at.'">
															<i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;&nbsp;'.trans_choice('site.feedbacks',1).'
													</button>';
                                            }
                                        ?>
                                        
                                    </td>
								</tr>
							@endforeach
							</tbody>
						</table>
                        <!-- page -->
                        <div class="pull-right">
                            {{$assignedAssignments->render()}}
                        </div>
					</div>
				</div>
                <div class="col-sm-6">
					<div class="panel panel-default custom-height-1">
						<div class="panel-heading">
                            <div class="navbar-form navbar-left">
                                <h4>{{ trans('site.my-coaching-timer') }}</h4>
                            </div>
                            <div class="navbar-form navbar-right">
                                <div class="form-group">
                                    <form role="search" method="get" action="">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="search_coaching_timer" placeholder="{{ trans('site.search-learner-id') }}..">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans('site.learner-id') }}</th>
								<th>{{ trans('site.approved-date') }}</th>
								<th>{{ trans('site.session-length') }}</th>
                                <th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($coachingTimers as $coachingTimer)
                                <?php $extension = explode('.', basename($coachingTimer->file)); ?>
								<tr>
									<td>
                                        <a href="{{ $coachingTimer->file }}" download>
                                        <i class="fa fa-download" aria-hidden="true"></i>
                                        </a>&nbsp;
										{{ $coachingTimer->user->id }}

										<!-- @if ($coachingTimer->help_with)
											<br>
											<a href="#viewHelpWithModal" style="color:#eea236" class="viewHelpWithBtn"
											   data-toggle="modal" data-details="{{ $coachingTimer->help_with }}">
                                               {{ $coachingTimer->help_with }}
											</a>
										@endif -->
									</td>
									<td>
										{{ $coachingTimer->approved_date ?
                                        \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($coachingTimer->approved_date)
                                         : ''}}
									</td>
									<td>
										{{ \App\Http\FrontendHelpers::getCoachingTimerPlanType($coachingTimer->plan_type) }}
									</td>
                                    <td>
                                        <button class="btn btn-primary btn-xs coachingTimerFeedbackBtn"
                                                data-target="#coachingTimerFeedbackModal"
                                                data-toggle="modal"
                                                data-replay_link = "{{$coachingTimer->replay_link}}"
                                                data-comment = "{{$coachingTimer->comment}}"
                                                data-document = "{{$coachingTimer->document}}"
                                                >
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;&nbsp;{{trans_choice('site.feedbacks',1)}}
                                        </button>
                                    </td>
								</tr>
							@endforeach
							</tbody>
						</table>
                        <!-- page -->
                        <div class="pull-right">
                            {{$coachingTimers->render()}}
                        </div>
					</div>
				</div>
			</div>

			<!-- My corrections -->
            <!-- My Copy Editing -->
			<div class="row">
				<div class="col-sm-6">
					<div class="panel panel-default custom-height-1">
						<div class="panel-heading">
                            <div class="panel-heading">
                                <div class="navbar-form navbar-left">
                                    <h4>{{ trans('site.my-correction') }}</h4>
                                </div>
                                <div class="navbar-form navbar-right">
                                    <div class="form-group">
                                        <form role="search" method="get" action="">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="search_correction" placeholder="{{ trans('site.search-learner-id') }}..">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                                                </span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manus', 2) }}</th>
                                <th>{{ trans('site.feedback-sent') }}</th>
								<th>{{ trans('site.learner-id') }}</th>
                                <th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($corrections as $correction)
                                <?php $extension = explode('.', basename($correction->file)); ?>
								<tr>
									<td>
                                        <a href="{{ route('editor.other-service.download-doc', ['id' => $correction->id, 'type' => 2]) }}">
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                        </a>&nbsp;
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
										@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">{{ basename($correction->file) }}</a>
										@endif
									</td>
                                    <td>{{$correction->feedback->created_at}}</td>
									<td>{{ $correction->user->id }}</td>
									<td>
                                    @if($correction->feedback)
                                        <a href="#approveOtherServiceFeedbackModal" data-toggle="modal"
                                            class="btn btn-primary btn-xs approveOtherServiceFeedbackBtn " 
                                            data-feedback_file = "{{ $correction->feedback->manuscript }}"
                                            data-created_at = "{{ $correction->feedback->created_at }}"
                                        >
                                            <i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;&nbsp;{{trans_choice('site.feedbacks',1)}}
                                        </a>
                                    @endif
                                    </td>
								</tr>
							@endforeach
							</tbody>
						</table>
                        <!-- page -->
                        <div class="pull-right">
                            {{$corrections->render()}}
                        </div>
					</div>
				</div>
                <div class="col-sm-6">
					<div class="panel panel-default custom-height-1">
						<div class="panel-heading">
                            <div class="navbar-form navbar-left">
                                <h4>{{ trans('site.my-copy-editing') }}</h4>
                            </div>
                            <div class="navbar-form navbar-right">
                                <div class="form-group">
                                    <form role="search" method="get" action="">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="search_copy_editing" placeholder="{{ trans('site.search-learner-id') }}..">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manus', 2) }}</th>
                                <th>{{ trans('site.feedback-sent') }}</th>
								<th>{{ trans('site.learner-id') }}</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($copyEditings as $copyEditing)
                                <?php $extension = explode('.', basename($copyEditing->file)); ?>
								<tr>
									<td>
                                        <a href="{{ route('editor.other-service.download-doc', ['id' => $copyEditing->id, 'type' => 1]) }}">
                                            <i class="fa fa-download" aria-hidden="true"></i>
                                        </a>&nbsp;
										@if( end($extension) == 'pdf' || end($copyEditing) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $copyEditing->file }}">{{ basename($copyEditing->file) }}</a>
										@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$copyEditing->file}}">{{ basename($copyEditing->file) }}</a>
										@endif
									</td>
                                    <td>{{$copyEditing->feedback->created_at}}</td>
									<td>{{ $copyEditing->user->id }}</td>
									<td>
                                        @if($copyEditing->feedback)
                                            <a href="#approveOtherServiceFeedbackModal" data-toggle="modal"
                                                class="btn btn-primary btn-xs approveOtherServiceFeedbackBtn " 
                                                data-feedback_file = "{{ $copyEditing->feedback->manuscript }}"
                                                data-created_at = "{{ $copyEditing->feedback->created_at }}"
                                            >
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;&nbsp;{{trans_choice('site.feedbacks',1)}}
                                            </a>
                                        @endif
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
                        <!-- page -->
                        <div class="pull-right">
                            {{$copyEditings->render()}}
                        </div>
					</div>
				</div>
			</div>
        
		</div>
	</div>

    <!-- Modals  -->
    <div id="personalAssignmentShowFeedbackModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.feedback-details') }}</h4>
                </div>
                <div class="modal-body">

                    <form>
                        <div class="form-group">
                            <label>{{ trans('site.created_at') }}</label><br>
                            <p id="feedback_date"></p>
                        </div>
                        <div class="form-group">
                            <label>{{ trans_choice('site.files', 1) }}</label><br>
                            <div id="feedbackFileAppend"></div>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.grade') }}</label><br>
                            <p id="feedback_grade"></p>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div id="shopManuscriptShowFeedbackModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.feedback-details') }}</h4>
                </div>
                <div class="modal-body">

                    <form>
                        <div class="form-group">
                            <label>{{ trans('site.created_at') }}</label><br>
                            <p id="created_at"></p>
                        </div>
                        <div class="form-group">
                            <label>{{ trans_choice('site.files', 1) }}</label><br>
                            <div id="feedbackFileAppend"></div>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.grade') }}</label><br>
                            <p id="grade"></p>
                        </div>
                        <div class="form-group">
                            <label>{{ trans_choice('site.grade', 1) }}</label><br>
                            <p id="notes"></p>
                        </div>
                        <div></div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div id="courseAssignmentShowFeedbackModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.feedback-details') }}</h4>
                </div>
                <div class="modal-body">

                    <form>
                        <div class="form-group">
                            <label>{{ trans('site.created_at') }}</label><br>
                            <p id="created_at"></p>
                        </div>
                        <div class="form-group">
                            <label>{{ trans_choice('site.files', 1) }}</label><br>
                            <div id="feedbackFileAppend"></div>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.grade') }}</label><br>
                            <p id="grade"></p>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div id="coachingTimerFeedbackModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.feedback-details') }}</h4>
                </div>
                <div class="modal-body">

                    <form>
                        <div class="form-group">
                            <label>{{ trans('site.replay-link') }}</label><br>
                            <a href="" id="replay_link"></a>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.document') }}</label><br>
                            <a href="" name="document" class="" download></a>
                        </div>
                        <div class="form-group">
                            <label>{{ trans_choice('site.comments', 1) }}</label><br>
                            <p id="comment"></p>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div id="approveOtherServiceFeedbackModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.feedback-details') }}</h4>
                </div>
                <div class="modal-body">

                    <form>
                        <div class="form-group">
                            <label>{{ trans('site.created_at') }}</label><br>
                            <p id="created_at"></p>
                        </div>
                        <div class="form-group">
                            <label>{{ trans_choice('site.manuscripts', 1) }}</label><br>
                            <div id="feedbackFileAppend"></div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@stop

@section('scripts')
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>
        $('.personalAssignmentShowFeedbackBtn').click(function(){
            var feedbackFileName =  $(this).data('feedback_file');
            var feedbackGrade =  $(this).data('feedback_grade');
            var feedbackDate = $(this).data('feedback_date');
            let modal = $('#personalAssignmentShowFeedbackModal');
            let action = $(this).data('action');
            var feedbackNotes = $(this).data('feedback_notes');
            
            var feedbackArray = feedbackFileName.split(",");
            modal.find('#feedbackFileAppend').html('');
            feedbackArray.forEach(function (item, index){
                modal.find('#feedbackFileAppend').append('<a href="'+ item +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
            })

            modal.find('#feedback_date').text(feedbackDate);
            modal.find('#feedback_grade').text(feedbackGrade);
            modal.find('[name=created_at]').val(feedbackDate);
        });

        $('.shopManuscriptShowFeedbackBtn').click(function(){
            var feedbackFileName =  $(this).data('feedback_file');
            var feedbackNotes =  $(this).data('feedback_notes');
            var feedbackGrade =  $(this).data('feedback_grade');
            var feedbackCreatedAt = $(this).data('feedback_created_at');
            let modal = $('#shopManuscriptShowFeedbackModal');
            
            var feedbackArray = feedbackFileName.split(",");
            modal.find('#feedbackFileAppend').html('');
            feedbackArray.forEach(function (item, index){
                modal.find('#feedbackFileAppend').append('<a href="'+ item +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
            })

            modal.find('#notes').text(feedbackNotes);
            modal.find('#grade').text(feedbackGrade);
            modal.find('#created_at').text(feedbackCreatedAt);
	    });

        $('.courseAssignmentShowFeedbackBtn').click(function(){
            var feedbackFileName =  $(this).data('feedback_file');
            var feedbackGrade =  $(this).data('feedback_grade');
            var feedbackDate = $(this).data('feedback_created_at');
            let modal = $('#courseAssignmentShowFeedbackModal');
            
            var feedbackArray = feedbackFileName.split(",");
            modal.find('#feedbackFileAppend').html('');
            feedbackArray.forEach(function (item, index){
                modal.find('#feedbackFileAppend').append('<a href="'+ item +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
            })

            modal.find('#grade').text(feedbackGrade);
            modal.find('#created_at').text(feedbackDate);
        });

        $('.coachingTimerFeedbackBtn').click(function(){
            var replayLink = $(this).data('replay_link');
            var comment = $(this).data('comment');
            var Document = $(this).data('document');

            let modal = $('#coachingTimerFeedbackModal');
            modal.find('[name=document]').attr("href", Document);
            modal.find('[name=document]').text(Document);
            modal.find('#comment').text(comment);
            modal.find('#replay_link').text(replayLink);
            modal.find('#replay_link').attr("href", replayLink);
        })

        $('.approveOtherServiceFeedbackBtn').click(function(){
            var feedbackFileName =  $(this).data('feedback_file');
            var created_at = $(this).data('created_at');
            let modal = $('#approveOtherServiceFeedbackModal');
            
            var feedbackArray = feedbackFileName.split(",");
            modal.find('#feedbackFileAppend').html('');
            feedbackArray.forEach(function (item, index){
                modal.find('#feedbackFileAppend').append('<a href="'+ item +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
            })

            modal.find('#created_at').text(created_at);
        });
    </script>
@stop