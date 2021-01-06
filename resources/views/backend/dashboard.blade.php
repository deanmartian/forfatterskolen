@extends('backend.layout')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<style>
		.panel {
			overflow-x: auto;
		}
	</style>
@stop

@section('content')
<div class="col-sm-12 col-md-10 dashboard-left">
	<div class="row">
		<div class="col-sm-12 @if (!Auth::user()->is_editor) col-md-5 @endif">
			<!-- Summary  -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default text-center">
						<div class="panel-body">
							<h3>{{count(App\User::where('role', 2)->get())}}</h3>
							{{ trans('site.total-learners') }}
						</div>
					</div>
				</div>
			</div>


			<!-- My assigned manuscripts -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4>
								Assignments/Personal assignments
							</h4>
						</div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manuscripts', 1) }}</th>
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th>{{ trans('site.assigned-to') }}</th>
								<th>Expected Finish</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($assignedAssignmentManuscripts as $assignedManuscript)
                                <?php $extension = explode('.', basename($assignedManuscript->filename)); ?>
								<tr>
									<td>
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
										<a href="{{ route('admin.learner.show',$assignedManuscript->user->id) }}">
											{{ $assignedManuscript->user->fullname }}
										</a>
									</td>
									<td>
										{{ $assignedManuscript->editor->full_name }}
									</td>
									<td>
										{{ $assignedManuscript->expected_finish }}
									</td>
									<td>
										<a href="{{ $assignedManuscript->filename }}" class="btn btn-primary btn-xs"
										   download>
											{{ trans('site.download') }}
										</a>
										<div style="margin-top: 5px">
											<input type="checkbox" data-toggle="toggle" data-on="{{ trans('site.locked') }}"
												   class="lock-toggle" data-off="{{ trans('site.unlocked') }}"
												   data-id="{{$assignedManuscript->id}}" data-size="mini"
											@if($assignedManuscript->locked) {{ 'checked' }} @endif>

                                            <button class="btn btn-warning btn-xs d-block
                                            submitPersonalAssignmentFeedbackBtn" style="margin-top: 5px"
                                                    data-target="#submitPersonalAssignmentFeedbackModal"
                                                    data-toggle="modal"
													data-name="{{ $assignedManuscript->user->full_name }}"
                                                    data-action="{{ route('assignment.group.manuscript-feedback-no-group',
																['id' => $assignedManuscript->id,
																'learner_id' => $assignedManuscript->user->id]) }}">
                                                + {{ trans('site.add-feedback') }}
                                            </button>
										</div>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>


		@if (!Auth::user()->is_editor)

			<!-- My assigned free manuscripts -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.my-assigned-free-manuscripts') }}</h4></div>
						<table class="table">
						    <thead>
						      <tr>
						        <th>{{ trans_choice('site.users', 1) }}</th>
						        <th>{{ trans_choice('site.emails', 1) }}</th>
						        <th></th>
						      </tr>
						    </thead>
						    <tbody>
						    	@foreach( $assigned_free_manuscripts as $freeManuscript )
						    	<tr>
						    		<td>{{ $freeManuscript->name }}</td>
						    		<td>{{ $freeManuscript->email }}</td>
						    		<td><button class="btn btn-xs btn-primary viewManuscriptBtn" data-toggle="modal" data-fields="{{ json_encode($freeManuscript) }}" data-target="#viewManuscriptModal">{{ trans('site.view') }}</button></td>
						    	</tr>
							    @endforeach
						    </tbody>
						</table>
					</div>
				</div>
			</div>



			<!-- Upcoming Webinars -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans_choice('site.shop-manuscripts', 2) }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manuscripts', 1) }}</th>
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th>Locked</th>
								<th>{{ trans('site.assigned-to') }}</th>
								<th>Expected Finish</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($shopManuscripts as $shopManuscript)
								@if( $shopManuscript->status == 'Started' )
									<tr>
										<td>@if($shopManuscript->is_active)
												<a href="{{ route('shop_manuscript_taken', ['id' => $shopManuscript->user->id, 'shop_manuscript_taken_id' => $shopManuscript->id]) }}">{{$shopManuscript->shop_manuscript->title}}</a>
											@else
												{{$shopManuscript->shop_manuscript->title}}
											@endif
										</td>
										<td><a href="{{ route('admin.learner.show', $shopManuscript->user->id) }}">{{ $shopManuscript->user->full_name }}</a></td>
										<td>
											@if ($shopManuscript->file)
												<input type="checkbox" data-toggle="toggle" data-on="Locked"
													   class="is-manuscript-locked-toggle" data-off="Unlocked"
													   data-id="{{$shopManuscript->id}}" data-size="mini"
												@if($shopManuscript->is_manuscript_locked) {{ 'checked' }} @endif>
											@endif
										</td>
										<td>
											@if( $shopManuscript->admin )
												{{ $shopManuscript->admin->full_name }}
											@else
												<em>Not set</em>
											@endif
										</td>
										<td>{{ $shopManuscript->expected_finish }}</td>
										<td>
											<a href="{{ route('backend.download_shop_manuscript', $shopManuscript->id) }}"
											   class="btn btn-primary btn-xs">{{ trans('site.download') }}</a> <br>

											<button type="button" class="btn btn-warning btn-xs margin-top addShopManuscriptFeedback" data-toggle="modal"
													data-target="#addFeedbackModal"
												data-action="{{ route('admin.shop-manuscript-taken-feedback.store',
												$shopManuscript->id) }}">+ {{ trans('site.add-feedback') }}</button>
										</td>
									</tr>
								@endif
							@endforeach
							</tbody>
						</table>
						{{--<table class="table">
						    <tbody>
						      <tr>
						        <td>
						        	<strong>Gorgeous Literary Group Writing</strong><br />
									<i class="fa fa-file-text-o" aria-hidden="true"></i> Children Courses
						        </td>
						        <td class="align-right">
						        	Webinar Hosts
						        	<div class="dashboard-webinar-hosts">
						        		<div></div>
						        		<div></div>
						        		<div></div>
						        	</div>
						        </td>
						      </tr>
						      <tr>
						        <td>
						        	<strong>Gorgeous Literary Group Writing</strong><br />
									<i class="fa fa-file-text-o" aria-hidden="true"></i> Children Courses
						        </td>
						        <td class="align-right">
						        	Webinar Hosts
						        	<div class="dashboard-webinar-hosts">
						        		<div></div>
						        		<div></div>
						        		<div></div>
						        	</div>
						        </td>
						      </tr>
						      <tr>
						        <td>
						        	<strong>Gorgeous Literary Group Writing</strong><br />
									<i class="fa fa-file-text-o" aria-hidden="true"></i> Children Courses
						        </td>
						        <td class="align-right">
						        	Webinar Hosts
						        	<div class="dashboard-webinar-hosts">
						        		<div></div>
						        		<div></div>
						        		<div></div>
						        	</div>
						        </td>
						      </tr>
						    </tbody>
						</table>--}}
					</div>
				</div>
			</div>

		@endif

			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.my-assignments') }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.courses', 1) }}</th>
								<th>{{ trans('site.learner-id') }}</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach ($assignedAssignments as $assignedAssignment)
								<tr>
									<td>
										@if($assignedAssignment->assignment->course)
											<a href="{{ route('admin.course.show', $assignedAssignment->assignment->course->id) }}">
												{{ $assignedAssignment->assignment->course->title }}
											</a>
										@else
											<a href="{{ route('admin.learner.assignment',
												[$assignedAssignment->assignment->parent_id, $assignedAssignment->assignment->id]) }}">
												{{ $assignedAssignment->assignment->title }}
											</a>
										@endif
									</td>
									<td>{{ $assignedAssignment->user_id }}</td>
									<td>
										<a href="{{ route('backend.download_assigned_manuscript', $assignedAssignment->id) }}"
										   class="btn btn-primary btn-xs">{{ trans('site.download') }}</a>
                                        <?php
                                        $learnerExist 	= \App\AssignmentGroupLearner::where('user_id', $assignedAssignment->user_id)->get();

                                        if ($learnerExist) {
                                            foreach ($learnerExist as $l) {
                                                $assignment_group_id = $l->assignment_group_id;
                                                $learner_id = $l->id;
                                                $assignment_group = \App\AssignmentGroup::where('id', $assignment_group_id)->where('assignment_id', $assignedAssignment->assignment->id)->first();
                                                if ($assignment_group) {
                                                    echo '<button type="button" class="btn btn-warning btn-xs submitFeedbackBtn"
															data-toggle="modal" data-target="#submitFeedbackModal"
															data-name="'.$assignedAssignment->user->full_name.'"
															data-action="'.route('admin.assignment.group.submit_feedback',
                                                            ['group_id' => $assignment_group_id, 'id' => $learner_id]).'"
                                                            data-manuscript="'.$assignedAssignment->id.'">'.
														trans('site.give-feedback').'</button>';
                                                } else {
                                                    echo '<button type="button" class="btn btn-warning btn-xs submitFeedbackBtn"
															data-toggle="modal" data-target="#submitFeedbackModal"
															data-name="'.$assignedAssignment->user->full_name.'"
															data-action="'.route('assignment.group.manuscript-feedback-no-group',
                                                            ['id' => $assignedAssignment->id, 'learner_id' => $assignedAssignment->user_id]).'"
                                                            data-manuscript="'.$assignedAssignment->id.'">'.
                                                        trans('site.give-feedback').'</button>';
												}
                                            }
                                        }
                                        ?>
										<button class="btn btn-success btn-xs finishAssignmentBtn" data-toggle="modal"
										data-target="#finishAssignmentModal" data-action="{{ route('backend.assignment.finish', $assignedAssignment->id) }}">{{ trans('site.finish') }}</button>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>


			<!-- My coaching timer -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.my-coaching-timer') }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th>{{ trans('site.approved-date') }}</th>
								<th>{{ trans('site.session-length') }}</th>
							</tr>
							</thead>
							<tbody>
							@foreach($coachingTimers as $coachingTimer)
                                <?php $extension = explode('.', basename($coachingTimer->file)); ?>
								<tr>
									<td>
										<a href="{{ route('admin.learner.show', $coachingTimer->user->id) }}">
											{{ $coachingTimer->user->full_name }}
										</a>

										@if ($coachingTimer->help_with)
											<br>
											<a href="#viewHelpWithModal" style="color:#eea236" class="viewHelpWithBtn"
											   data-toggle="modal" data-details="{{ $coachingTimer->help_with }}">
												{{ trans('site.view-help-with') }}
											</a>
										@endif
									</td>
									<td>
										{{ $coachingTimer->approved_date ?
                                        \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($coachingTimer->approved_date)
                                         : ''}}
									</td>
									<td>
										{{ \App\Http\FrontendHelpers::getCoachingTimerPlanType($coachingTimer->plan_type) }}
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- end My coaching timer -->

			<!-- My corrections -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.my-correction') }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manus', 2) }}</th>
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th>{{ trans('site.expected-finish') }}</th>
								<th>{{ trans('site.status') }}</th>
								<th></th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($corrections as $correction)
                                <?php $extension = explode('.', basename($correction->file)); ?>
								<tr>
									<td>
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
										@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">{{ basename($correction->file) }}</a>
										@endif
									</td>
									<td>
										<a href="{{ route('admin.learner.show', $correction->user->id) }}">
											{{ $correction->user->full_name }}
										</a>
									</td>
									<td>
										@if ($correction->expected_finish)
											{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($correction->expected_finish) }}
											<br>
										@endif

										@if ($correction->status !== 2)
											<a href="#setOtherServiceFinishDateModal" data-toggle="modal"
											   class="setOtherServiceFinishDateBtn"
											   data-action="{{ route('admin.other-service.update-expected-finish',
										   ['id' => $correction->id, 'type' => 2]) }}"
											   data-finish="{{ $correction->expected_finish ?
										strftime('%Y-%m-%dT%H:%M:%S', strtotime($correction->expected_finish)) : '' }}">
												{{ trans('site.set-date') }}
											</a>
										@endif
									</td>
									<td>
										@if( $correction->status == 2 )
											<span class="label label-success">Finished</span>
										@elseif( $correction->status == 1 )
											<span class="label label-primary">Started</span>
										@elseif( $correction->status == 0 )
											<span class="label label-warning">Not started</span>
										@endif
									</td>
									<td>
                                        <?php
                                        $btnColor = $correction->status == 1 ? 'primary' : 'warning';
                                        ?>

										@if ($correction->status !== 2)
											<button class="btn btn-{{ $btnColor }} btn-xs updateOtherServiceStatusBtn" type="button"
													data-toggle="modal" data-target="#updateOtherServiceStatusModal"
													data-service="2"
													data-action="{{ route('admin.other-service.update-status', ['id' => $correction->id, 'type' => 2]) }}"><i class="fa fa-check"></i></button>
										@endif
									</td>
									<td>
										<a href="{{ route('admin.other-service.download-doc',
										   ['id' => $correction->id, 'type' => 2]) }}">{{ trans('site.download') }}</a>
										<!-- show only if no feedback is given yet for this correction -->
										@if (!$correction->feedback)
											<a href="#addOtherServiceFeedbackModal" data-toggle="modal" style="color:#eea236"
											class="addOtherServiceFeedbackBtn" data-service="2"
											data-action="{{ route('admin.other-service.add-feedback',
											['id' => $correction->id, 'type' => 2]) }}">+ {{ trans('site.add-feedback') }}</a>
										@endif
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- end My corrections -->

			<!-- My Copy Editing -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.my-copy-editing') }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manus', 2) }}</th>
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th>{{ trans('site.expected-finish') }}</th>
								<th>{{ trans('site.status') }}</th>
								<th></th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($copyEditings as $copyEditing)
                                <?php $extension = explode('.', basename($copyEditing->file)); ?>
								<tr>
									<td>
										@if( end($extension) == 'pdf' || end($copyEditing) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $copyEditing->file }}">{{ basename($copyEditing->file) }}</a>
										@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$copyEditing->file}}">{{ basename($copyEditing->file) }}</a>
										@endif
									</td>
									<td>
										<a href="{{ route('admin.learner.show', $copyEditing->user->id) }}">
											{{ $copyEditing->user->full_name }}
										</a>
									</td>
									<td>
										@if ($copyEditing->expected_finish)
											{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($copyEditing->expected_finish) }}
											<br>
										@endif

										@if ($copyEditing->status !== 2)
											<a href="#setOtherServiceFinishDateModal" data-toggle="modal"
											   class="setOtherServiceFinishDateBtn"
											   data-action="{{ route('admin.other-service.update-expected-finish',
										   ['id' => $copyEditing->id, 'type' => 2]) }}"
											   data-finish="{{ $copyEditing->expected_finish ?
										strftime('%Y-%m-%dT%H:%M:%S', strtotime($copyEditing->expected_finish)) : '' }}">
												{{ trans('site.set-date') }}
											</a>
										@endif
									</td>
									<td>
										@if( $copyEditing->status == 2 )
											<span class="label label-success">Finished</span>
										@elseif( $copyEditing->status == 1 )
											<span class="label label-primary">Started</span>
										@elseif( $copyEditing->status == 0 )
											<span class="label label-warning">Not started</span>
										@endif
									</td>
									<td>
                                        <?php
                                        $btnColor = $copyEditing->status == 1 ? 'primary' : 'warning';
                                        ?>

										@if ($copyEditing->status !== 2)
											<button class="btn btn-{{ $btnColor }} btn-xs updateOtherServiceStatusBtn" type="button"
													data-toggle="modal" data-target="#updateOtherServiceStatusModal"
													data-service="1"
													data-action="{{ route('admin.other-service.update-status', ['id' => $copyEditing->id, 'type' => 1]) }}"><i class="fa fa-check"></i></button>
										@endif
									</td>

									<td>
										<a href="{{ route('admin.other-service.download-doc',
										   ['id' => $copyEditing->id, 'type' => 1]) }}">{{ trans('site.download') }}</a>

										<!-- show only if no feedback is given yet for this copyEditing -->
										@if (!$copyEditing->feedback)
											<a href="#addOtherServiceFeedbackModal" data-toggle="modal" style="color:#eea236"
											   class="addOtherServiceFeedbackBtn" data-service="1"
											   data-action="{{ route('admin.other-service.add-feedback',
											['id' => $copyEditing->id, 'type' => 1]) }}">+ {{ trans('site.add-feedback') }}</a>
										@endif
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- end My Copy Editing -->

		</div>

		@if (!Auth::user()->is_editor)
		<div class="col-sm-12 col-md-7">
			<!-- Pending Courses -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.pending-courses') }}</h4></div>
						<div class="table-responsive">
							<table class="table">
							    <thead>
							      <tr>
							        <th>{{ trans_choice('site.courses', 1) }}</th>
							        <th>{{ trans_choice('site.learners', 1) }}</th>
							        <th>{{ trans('site.date-ordered') }}</th>
							        <th></th>
							      </tr>
							    </thead>
							    <tbody>
							    	@foreach( $pending_courses as $pending_course )
							      	<tr>
								        <td>{{ $pending_course->package->course->title }}</td>
								        <td>
											<a href="{{ route('admin.learner.show', $pending_course->user->id) }}">
												{{ $pending_course->user->full_name }}
											</a>
										</td>
								        <td>{{ $pending_course->created_at }}</td>
								        <td>
								        	<form method="POST" action="{{ route('activate_course_taken') }}" class="inline-block">
												{{ csrf_field() }}
												<input type="hidden" name="coursetaken_id" value="{{ $pending_course->id }}">
												<button class="btn btn-warning btn-xs" type="submit"><i class="fa fa-check"></i></button>
											</form>
								        	<form method="POST" action="{{ route('delete_course_taken') }}" class="inline-block">
												{{ csrf_field() }}
												<input type="hidden" name="coursetaken_id" value="{{ $pending_course->id }}">
												<button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
											</form>
								        </td>
							      	</tr>
							      	@endforeach
							    </tbody>
							</table>
						</div>
					</div>
				</div>
			</div>


			<!-- Pending Shop Manuscripts -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.pending-shop-manuscripts') }}</h4></div>
						<table class="table">
						    <thead>
						      <tr>
						        <th>{{ trans_choice('site.manuscripts', 1) }}</th>
						        <th>{{ trans_choice('site.learners', 1) }}</th>
						        <th>{{ trans('site.date-ordered') }}</th>
						        <th></th>
						      </tr>
						    </thead>
						    <tbody>
						    	@foreach( $pending_shop_manuscripts as $pending_shop_manuscript )
						      	<tr>
							        <td>
										<a href="{{ route('shop_manuscript_taken',
										['id' => $pending_shop_manuscript->user->id,
										'shop_manuscript_taken_id' => $pending_shop_manuscript->id]) }}">
											{{$pending_shop_manuscript->shop_manuscript->title}}
										</a>
									</td>
							        <td>
										<a href="{{ route('admin.learner.show', $pending_shop_manuscript->user->id) }}">
											{{ $pending_shop_manuscript->user->full_name }}
										</a>
									</td>
							        <td>{{ $pending_shop_manuscript->created_at }}</td>
							        <td>
							        	<form method="POST" action="{{ route('activate_shop_manuscript_taken') }}" class="inline-block">
											{{ csrf_field() }}
											<input type="hidden" name="shop_manuscript_id" value="{{ $pending_shop_manuscript->id }}">
											<button class="btn btn-warning btn-xs" type="submit"><i class="fa fa-check"></i></button>
										</form>
							        	<form method="POST" action="{{ route('delete_shop_manuscript_taken') }}" class="inline-block">
											{{ csrf_field() }}
											<input type="hidden" name="shop_manuscript_id" value="{{ $pending_shop_manuscript->id }}">
											<button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
										</form>
							        </td>
						      	</tr>
						      	@endforeach
						    </tbody>
						</table>
					</div>
				</div>
			</div>
				


			<!-- Pending Workshops -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.pending-workshops') }}</h4></div>
						<table class="table">
						    <thead>
						      <tr>
								  <th>{{ trans_choice('site.manuscripts', 1) }}</th>
								  <th>{{ trans_choice('site.learners', 1) }}</th>
								  <th>{{ trans('site.date-ordered') }}</th>
						        <th></th>
						      </tr>
						    </thead>
						    <tbody>
						    	@foreach( $pending_workshops as $pending_workshop )
						      	<tr>
							        <td>{{ $pending_workshop->workshop->title }}</td>
							        <td>{{ $pending_workshop->user->full_name }}</td>
							        <td>{{ $pending_workshop->created_at }}</td>
							        <td>
							        	<form method="POST" action="{{ route('admin.package_workshop.approve', $pending_workshop->id) }}" class="inline-block">
											{{ csrf_field() }}
											<input type="hidden" name="workshop_user_id" value="{{ $pending_workshop->user_id }}">
											<input type="hidden" name="workshop_id" value="{{ $pending_workshop->workshop_id }}">
											<button class="btn btn-warning btn-xs" type="submit"><i class="fa fa-check"></i></button>
										</form>
							        	<form method="POST" action="{{ route('admin.package_workshop.disapprove', $pending_workshop->id) }}" class="inline-block">
											{{ csrf_field() }}
											<button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
										</form>
							        </td>
						      	</tr>
						      	@endforeach
						    </tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4>Pending Assignment</h4>
						</div>
						<table class="table">
							<thead>
								<tr>
									<th>{{ trans_choice('site.manuscripts', 1) }}</th>
									<th>{{ trans_choice('site.learners', 1) }}</th>
									<th>{{ trans_choice('site.courses', 1) }}</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach($pendingAssignments as $pendingAssignment)
                                    <?php $extension = explode('.', basename($pendingAssignment->filename)); ?>
									<tr>
										<td>
											@if( end($extension) == 'pdf' || end($extension) == 'odt' )
												<a href="/js/ViewerJS/#../..{{ $pendingAssignment->filename }}">
													{{ basename($pendingAssignment->filename) }}
												</a>
											@elseif( end($extension) == 'docx' || end($extension) == 'doc' )
												<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$pendingAssignment->filename}}">
													{{ basename($pendingAssignment->filename) }}
												</a>
											@endif
										</td>
										<td>
											<a href="{{ route('admin.learner.show',$pendingAssignment->user->id) }}">
												{{ $pendingAssignment->user->fullname }}
											</a>
										</td>
										<td>
											@if($pendingAssignment->assignment->course)
												<a href="{{ route('admin.course.show', $pendingAssignment->assignment->course->id) }}">
													{{ $pendingAssignment->assignment->course->title }}
												</a>
											@endif
										</td>
										<td>
											<button class="btn btn-xs btn-warning pendingAssignmentEditorBtn" data-toggle="modal"
													data-target="#pendingAssignmentEditorModal"
													data-action="{{ route('assignment.group.assign_manu_editor', $pendingAssignment->id) }}"
													data-preferred-editor="{{ $pendingAssignment->user->preferredEditor
								? $pendingAssignment->user->preferredEditor->editor_id : "" }}"
													data-preferred-editor-name="{{ $pendingAssignment->user->preferredEditor
								? $pendingAssignment->user->preferredEditor->editor->full_name : "" }}"
											>
												{{ trans('site.assign-editor') }}
											</button>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>


			<!-- Pending Assignment Feedbacks -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.pending-assignment-feedbacks') }}</h4></div>
						<table class="table">
						    <thead>
						      <tr>
						        <th>{{ trans_choice('site.manuscripts', 1) }}</th>
						        <th>{{ trans('site.submitted-by') }}</th>
						        <th>{{ trans('site.submitted-to') }}</th>
						        <th>{{ trans_choice('site.assignments', 1) }}</th>
						        <th></th>
						      </tr>
						    </thead>
						    <tbody>
						    	@foreach( $pending_assignment_feedbacks as $assignment_feedback )
						    	<?php $extension = explode('.', basename($assignment_feedback->filename)); ?>
						      	<tr>
							        <td>
							        	
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
										<a href="/js/ViewerJS/#../..{{ $assignment_feedback->filename }}">{{ basename($assignment_feedback->filename) }}</a>
										@elseif( end($extension) == 'docx' )
										<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$assignment_feedback->filename}}">{{ basename($assignment_feedback->filename) }}</a>
										@endif
							        </td>
							        <td>{{ $assignment_feedback->user->full_name }}</td>
							        <td>{{ $assignment_feedback->assignment_group_learner->user->full_name }}</td>
							        <td><a href="{{ route('admin.assignment.show', ['course_id' => $assignment_feedback->assignment_group_learner->group->assignment->course->id, 'id' => $assignment_feedback->assignment_group_learner->group->assignment->id]) }}">{{ $assignment_feedback->assignment_group_learner->group->assignment->title }}</a></td>
							        <td>
										<button type="button" class="btn btn-warning btn-xs approveFeedbackAdminBtn" data-toggle="modal" data-target="#approveFeedbackAdminModal" data-action="{{ route('admin.assignment.group.approve', $assignment_feedback->id) }}"><i class="fa fa-check"></i></button>
										<button type="button" class="btn btn-xs btn-danger removeFeedbackAdminBtn" data-toggle="modal" data-target="#removeFeedbackAdminModal" data-action="{{ route('admin.assignment.group.remove_feedback', $assignment_feedback->id) }}"><i class="fa fa-trash"></i></button>
							        </td>
						      	</tr>
						      	@endforeach
						    </tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Pending Coaching Timer -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.pending-coaching-timer') }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th>{{ trans('site.learner-suggested-date') }}</th>
								<th>{{ trans('site.session-length') }}</th>
								<th>{{ trans_choice('site.editors', 1) }}</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($pendingCoachingTimers as $coachingTimer)
								<tr>
									<td>
										<a href="{{ route('admin.learner.show', $coachingTimer->user->id) }}">
											{{ $coachingTimer->user->full_name }}
										</a>

										@if ($coachingTimer->help_with)
											<br>
											<a href="#viewHelpWithModal" style="color:#eea236" class="viewHelpWithBtn"
											data-toggle="modal" data-details="{{ $coachingTimer->help_with }}">
												{{ trans('site.view-help-with') }}
											</a>
										@endif
									</td>
									<td>
                                        <?php
                                        $suggested_dates = json_decode($coachingTimer->suggested_date);
                                        ?>
										@if($suggested_dates)
											@for($i =0; $i <= 2; $i++)
												<div style="margin-top: 5px">
													{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($suggested_dates[$i]) }}
													@if (!$coachingTimer->approved_date)
														<button class="btn btn-success btn-xs approveDateBtn"
																data-toggle="modal" data-target="#approveDateModal"
																data-date="{{ $suggested_dates[$i] }}"
																data-action="{{ route('admin.other-service.coaching-timer.approve_date', $coachingTimer->id) }}">
															<i class="fa fa-check"></i>
														</button>
													@endif
												</div>
											@endfor
										@endif
									</td>
									<td>
										{{ \App\Http\FrontendHelpers::getCoachingTimerPlanType($coachingTimer->plan_type) }}
									</td>
									<td>
										@if ($coachingTimer->editor_id)
											{{ $coachingTimer->editor->full_name }}
										@else
											<button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.other-service.assign-editor', ['id' => $coachingTimer->id, 'type' => 3]) }}">{{ trans('site.assign-editor') }}</button>
										@endif
									</td>
									<td>
										<button class="btn btn-primary btn-xs approveCoachingSessionBtn" data-toggle="modal"
												data-target="#approveCoachingSessionModal" data-action="{{ route('admin.coaching-timer.approve', $coachingTimer->id) }}">
											{{ trans('site.approve') }}
										</button>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- end Pending Coaching Timer -->

			<!-- Pending Proofing -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.pending-correction') }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manus', 2) }}</th>
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($pendingCorrections as $correction)
                                <?php $extension = explode('.', basename($correction->file)); ?>
								<tr>
									<td>
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
										@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">{{ basename($correction->file) }}</a>
										@endif
									</td>
									<td>
										<a href="{{ route('admin.learner.show', $correction->user->id) }}">
											{{ $correction->user->full_name }}
										</a>
									</td>
									<td>
										<button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.other-service.assign-editor', ['id' => $correction->id, 'type' => 2]) }}">{{ trans('site.assign-editor') }}</button>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- end Pending Proofing -->

			<!-- Pending Copy Editing -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>{{ trans('site.pending-copy-editing') }}</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.manus', 2) }}</th>
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach($pendingCopyEditings as $copyEditing)
                                <?php $extension = explode('.', basename($copyEditing->file)); ?>
								<tr>
									<td>
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $copyEditing->file }}">{{ basename($copyEditing->file) }}</a>
										@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$copyEditing->file}}">{{ basename($copyEditing->file) }}</a>
										@endif
									</td>
									<td>
										<a href="{{ route('admin.learner.show', $copyEditing->user->id) }}">
											{{ $copyEditing->user->full_name }}
										</a>
									</td>
									<td>
										<button class="btn btn-xs btn-warning assignEditorBtn" data-toggle="modal" data-target="#assignEditorModal" data-action="{{ route('admin.other-service.assign-editor', ['id' => $copyEditing->id, 'type' => 1]) }}">{{ trans('site.assign-editor') }}</button>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- end Pending Copy Editing -->

			<!-- Pending tasks -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>Pending Tasks</h4></div>
						<table class="table">
							<thead>
							<tr>
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th>Task</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
								@foreach($pendingTasks as $task)
									<tr>
										<td>
											<a href="{{ route('admin.learner.show', $task->user->id) }}">
												{{ $task->user->full_name }}
											</a>
										</td>
										<td>{!! nl2br($task->task) !!}</td>
										<td>
											<button class="btn btn-success btn-xs finishTaskBtn" data-toggle="modal"
													data-target="#finishTaskModal"
													data-action="{{ route('admin.task.finish', $task->id)}}">
												<i class="fa fa-check"></i>
											</button>
											<button class="btn btn-primary btn-xs editTaskBtn" data-toggle="modal"
													data-target="#editTaskModal"
													data-fields="{{ json_encode($task) }}"
													data-action="{{ route('admin.task.update', $task->id) }}">
												<i class="fa fa-edit"></i>
											</button>
											<button class="btn btn-danger btn-xs deleteTaskBtn" data-toggle="modal"
													data-target="#deleteTaskModal"
													data-action="{{ route('admin.task.destroy', $task->id) }}">
												<i class="fa fa-trash"></i>
											</button>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		@endif
	</div>
</div>

<div class="col-sm-12 col-md-2 dashboard-right">
	<h3 class="actitities-header">{{ trans('site.recent-activities') }}</h3>
	@foreach( $logs as $log )
	<div class="dashboard-activity" style="color: green">
		<p>
			<span class="activ-time">{{ Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</span>
			{!! $log->activity !!}
		</p>
	</div>
	@endforeach
</div>
<div id="approveFeedbackAdminModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.approve-feedback') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="">
		      {{ csrf_field() }}
				{{ trans('site.approve-feedback-question') }}
		      <div class="text-right margin-top">
		      	<button type="submit" class="btn btn-warning">{{ trans('site.approve') }}</button>
		      </div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="removeFeedbackAdminModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.delete-feedback') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="">
		      {{ csrf_field() }}
				{{ trans('site.delete-feedback-question') }}
		      <div class="text-right margin-top">
		      	<button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
		      </div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="viewManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-body">
		  	<p>
		  		<strong>{{ trans('site.name') }}:</strong><br />
		  		<span id="name"></span><br />
		  		<br />
		  		<strong>{{ trans_choice('site.emails', 1) }}:</strong><br />
		  		<span id="email"></span><br />
		  		<br />
		  		<strong>{{ trans_choice('site.manuscripts', 1) }}:</strong><br />
		  		<span id="content"></span>
		  	</p>
		  </div>
		</div>
	</div>
</div>

<div id="submitFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.submit-feedback-to') }} <em></em></h4>
			</div>
			<div class="modal-body">
				<form method="POST" action=""  enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans_choice('site.manuscripts', 1) }}</label>
						<input type="file" class="form-control" required multiple name="filename[]" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
						* Accepted file formats are DOCX, PDF, ODT.
					</div>
					<div class="form-group">
						<label>{{ trans('site.available-date') }}</label>
						<input type="date" class="form-control" name="availability">
					</div>
					<div class="form-group">
						<label>{{ trans('site.grade') }}</label>
						<input type="number" class="form-control" step="0.01" name="grade">
					</div>
					<input type="hidden" name="manuscript_id">
					<button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="addFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.add-feedback') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data">
					<?php
						$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Shop Manuscript Feedback');
					?>
					{{csrf_field()}}
					<div class="form-group">
						<label>{{ trans_choice('site.files', 2) }}</label>
						<input type="file" class="form-control" name="files[]" multiple
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
							   application/pdf, application/vnd.oasis.opendocument.text" required>
					</div>
					<div class="form-group">
						<label>{{ trans_choice('site.notes', 2) }}</label>
						<textarea class="form-control" name="notes" rows="6"></textarea>
					</div>
					<div class="form-group">
						<label>{{ trans('site.subject') }}</label>
						<input type="text" class="form-control" name="subject" value="{{ $emailTemplate->subject }}"
							   required>
					</div>
						<div class="form-group">
							<label>{{ trans('site.from') }}</label>
							<input type="text" class="form-control" name="from_email"
								   value="{{ $emailTemplate->from_email }}" required>
						</div>
					<div class="form-group">
						<label>{{ trans('site.message') }}</label>
						<textarea class="form-control tinymce" name="message" rows="6"
								  required>{!! $emailTemplate->email_content !!}</textarea>
					</div>
					{{ trans('site.add-feedback-note') }}
					<button type="submit" class="btn btn-primary pull-right">{{ trans('site.add-feedback') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="finishAssignmentModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.finish-assignment') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{csrf_field()}}
					{{ trans('site.finish-assignment-question') }}
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">{{ trans('site.submit') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="assignEditorModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.assign-editor') }}</label>
						<select name="editor_id" class="form-control select2" required>
							<option value="" disabled="" selected>-- Select Editor --</option>
							@foreach( App\User::where('role', 1)->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="pendingAssignmentEditorModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.assign-editor') }}</label>
						<select name="editor_id" class="form-control select2" required>
							<option value="" disabled="" selected>-- Select Editor --</option>
							@foreach( App\User::where('role', 1)->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>

						<div class="hidden-container">
							<label>
							</label>
							<a href="javascript:void(0)" onclick="enableSelect('pendingAssignmentEditorModal')">Edit</a>
						</div>
					</div>
					<div class="form-group">
						<label>Expected Finish</label>
						<input type="date" class="form-control" name="expected_finish">
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>



<div id="updateOtherServiceStatusModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{!! str_replace('_SERVICE_','<span></span>',trans('site.update-service-status')) !!}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>
						{{ trans('site.update-service-status-question') }}
					</p>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="setOtherServiceFinishDateModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><span></span> {{ trans('site.expected-finish') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.expected-finish-date') }}</label>
						<input type="datetime-local" name="expected_finish" class="form-control" required>
					</div>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="addOtherServiceFeedbackModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span></span> {{ trans('site.add-feedback') }}</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                        <input type="file" class="form-control" name="manuscript" multiple accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf" required>
                    </div>
                    <button type="submit" class="btn btn-primary pull-right">{{ trans('site.add-feedback') }}</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>

    </div>
</div>

<div id="approveCoachingSessionModal" class="modal fade" role="dialog"  data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.approve-coaching-timer') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>{{ trans('site.approve-coaching-timer-question') }}</p>
					<div class="text-right">
						<button class="btn btn-primary" type="submit">{{ trans('site.approve') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div id="viewHelpWithModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Help With</h4>
			</div>
			<div class="modal-body">
				<pre></pre>
			</div>
		</div>
	</div>
</div>

<div id="finishTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Finish Task</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}

					<p>Are you sure to finish this task?</p>

					<button type="submit" class="btn btn-success pull-right">Finish</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="editTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit Task</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('PUT') }}
					<input type="hidden" name="user_id" value="">

					<div class="form-group">
						<label>
							Task
						</label>
						<textarea name="task" cols="30" rows="10" class="form-control" required></textarea>
					</div>

					<div class="form-group">
						<label>
							{{ trans('site.assign-to') }}
						</label>
						<select name="assigned_to" class="form-control select2" required>
							<option value="" disabled="" selected>-- Select Assignee --</option>
							@foreach( App\User::where('role', 1)->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
					</div>

					<button type="submit" class="btn btn-primary pull-right">Update Task</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Delete Task</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action=""
					  onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}

					<p>Are you sure to delete this task?</p>

					<button type="submit" class="btn btn-danger pull-right">Delete</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="submitPersonalAssignmentFeedbackModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{ trans('site.submit-feedback-to') }} <em></em></h4>
            </div>
            <div class="modal-body">

                <form method="POST" action=""  enctype="multipart/form-data">
                    <?php
                    	$emailTemplate = \App\Http\AdminHelpers::emailTemplate('Assignment Manuscript Feedback');
                    ?>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>{{ trans_choice('site.manuscripts', 1) }}</label>
                        <input type="file" class="form-control" required multiple name="filename[]"
                               accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/pdf, application/vnd.oasis.opendocument.text">
                        * Accepted file formats are DOCX, PDF, ODT.
                    </div>
                    <div class="form-group">
                        <label>{{ trans('site.available-date') }}</label>
                        <input type="date" class="form-control" name="availability">
                    </div>
                    <div class="form-group">
                        <label>{{ trans('site.grade') }}</label>
                        <input type="number" class="form-control" step="0.01" name="grade">
                    </div>
					<div class="form-group">
						<label>{{ trans('site.subject') }}</label>
						<input type="text" class="form-control" name="subject" value="{{ $emailTemplate->subject }}"
							   required>
					</div>
					<div class="form-group">
						<label>{{ trans('site.from') }}</label>
						<input type="text" class="form-control" name="from_email"
							   value="{{ $emailTemplate->from_email }}" required>
					</div>
					<div class="form-group">
						<label>{{ trans('site.message') }}</label>
						<textarea class="form-control tinymce" name="message" rows="6"
								  required>{!! $emailTemplate->email_content !!}</textarea>
					</div>
                    <button type="submit" class="btn btn-primary pull-right margin-top">{{ trans('site.submit') }}</button>
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
	$('.viewManuscriptBtn').click(function(){
		var fields = $(this).data('fields');
		var modal = $('#viewManuscriptModal');
		modal.find('#name').text(fields.name);
		modal.find('#email').text(fields.email);
		modal.find('#content').text(fields.content);
	});
	$('.approveFeedbackAdminBtn').click(function(){
		var modal = $('#approveFeedbackAdminModal');
		var action = $(this).data('action');
		modal.find('form').attr('action', action);
	});
	$('.removeFeedbackAdminBtn').click(function(){
		var modal = $('#removeFeedbackAdminModal');
		var action = $(this).data('action');
		modal.find('form').attr('action', action);
	});

    $('.submitFeedbackBtn').click(function(){
        var modal = $('#submitFeedbackModal');
        var name = $(this).data('name');
        var action = $(this).data('action');
        var manuscript_id = $(this).data('manuscript');
        modal.find('em').text(name);
        modal.find('form').attr('action', action);
        modal.find('form').find('input[name=manuscript_id]').val(manuscript_id);
    });

    $(".addShopManuscriptFeedback").click(function(){
        var modal = $('#addFeedbackModal');
        var action = $(this).data('action');
        modal.find('form').attr('action', action);
	});

    $(".finishAssignmentBtn").click(function(){
        let modal = $('#finishAssignmentModal');
        let action = $(this).data('action');
        modal.find('form').attr('action', action);
	});

    $('.assignEditorBtn').click(function(){
        let action = $(this).data('action');
        let editor = $(this).data('editor');
        let modal = $('#assignEditorModal');
        modal.find('select').val(editor);
        modal.find('form').attr('action', action);
    });

    $(".pendingAssignmentEditorBtn").click(function(){
        let action = $(this).data('action');
        let editor = $(this).data('editor');
        let preferred_editor = $(this).data('preferred-editor');
        let preferred_editor_name = $(this).data('preferred-editor-name');
        let modal = $('#pendingAssignmentEditorModal');
        modal.find('select').val(preferred_editor).trigger('change');
        modal.find('form').attr('action', action);

        if (preferred_editor) {
            modal.find('.select2').hide();
            modal.find('.hidden-container').show();
            modal.find('.hidden-container').find('label').empty().text(preferred_editor_name);
        } else {
            modal.find('.select2').show();
            modal.find('.hidden-container').hide();
        }
	});

    $(".updateOtherServiceStatusBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#updateOtherServiceStatusModal');
        let service = $(this).data('service');
        let title = 'Korrektur';

        if (service === 1) {
            title = 'Språkvask';
        }
        modal.find('form').attr('action', action);
        modal.find('.modal-title').find('span').text(title);
    });

    $(".setOtherServiceFinishDateBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#setOtherServiceFinishDateModal');
        let finish = $(this).data('finish');

        modal.find('form').attr('action', action);
        modal.find('form').find('[name=expected_finish]').val(finish);
    });

    $(".addOtherServiceFeedbackBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#addOtherServiceFeedbackModal');
        let service = $(this).data('service');
        let title = 'Korrektur';

        if (service === 1) {
            title = 'Språkvask';
        }
        modal.find('form').attr('action', action);
        modal.find('.modal-title').find('span').text(title);
    });

    $(".approveCoachingSessionBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#approveCoachingSessionModal');
        modal.find('form').attr('action', action);
	});

    $(".viewHelpWithBtn").click(function(){
       let details = $(this).data('details');
       let modal = $("#viewHelpWithModal");

       modal.find('.modal-body').find('pre').text(details);
	});

    $(".is-manuscript-locked-toggle").change(function(){
        let shopManuscriptTakenId = $(this).attr('data-id');
        let is_checked = $(this).prop('checked');
        let check_val = is_checked ? 1 : 0;
        $.ajax({
            type:'POST',
            url:'/is-manuscript-locked-status',
            data: { "shop_manuscript_taken_id" : shopManuscriptTakenId, 'is_manuscript_locked' : check_val },
            success: function(data){
            }
        });
    });

    $(".finishTaskBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#finishTaskModal');
        modal.find('form').attr('action', action);
    });

    $(".editTaskBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#editTaskModal');
        let fields = $(this).data('fields');
        modal.find('form').attr('action', action);
        modal.find('[name=task]').text(fields.task);
        modal.find('[name=user_id]').val(fields.user_id);
        modal.find('form').find('[name=assigned_to]').val(fields.assigned_to).trigger('change');
    });

    $(".lock-toggle").change(function(){
        let course_id = $(this).attr('data-id');
        let is_checked = $(this).prop('checked');
        let check_val = is_checked ? 1 : 0;
        $.ajax({
            type:'POST',
            url:'/assignment_manuscript/lock-status',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { "manuscript_id" : course_id, 'locked' : check_val },
            success: function(data){
            }
        });
    });

    $(".deleteTaskBtn").click(function(){
        let action = $(this).data('action');
        let modal = $('#deleteTaskModal');
        modal.find('form').attr('action', action);
    });

    $('.submitPersonalAssignmentFeedbackBtn').click(function(){
        let modal = $('#submitPersonalAssignmentFeedbackModal');
        let name = $(this).data('name');
        let action = $(this).data('action');
        let is_edit = $(this).data('edit');

        modal.find('em').text(name);
        modal.find('form').attr('action', action);
        if (is_edit) {
            modal.find('form').find('input[type=file]').removeAttr('required');
        } else {
            modal.find('form').find('input[type=file]').attr('required', 'required');
        }
    });

    function disableSubmit(t) {
        let submit_btn = $(t).find('[type=submit]');
        submit_btn.text('');
        submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
        submit_btn.attr('disabled', 'disabled');
    }

</script>
@stop