@extends('backend.layout')

@section('title')
<title>Learners &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

@include('backend.course.partials.toolbar')


<div class="course-container">
	
	@include('backend.partials.course_submenu')

    <?php
    $search = Request::input('search');
    if( $search ) :
        $learners = $course->learners
            ->whereHas('user', function($query) use ($search){
                $query->where('first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                ;
            })
            ->paginate(25);

    	if (is_numeric($search)) :
            $emailOutLearnerSearch = $course->emailOutLog()->where('id', $search)->first();
            if ($emailOutLearnerSearch) {
                $emailOutLearnerSearch = json_decode($emailOutLearnerSearch->learners);
                $learners = $course->learners->whereIn('user_id', $emailOutLearnerSearch)->paginate(25);
            }
		endif;

    else :
        $learners = $course->learners->paginate(25);
    endif;

    $packageIdsOfCourse = $course->packages()->pluck('id')->toArray();
    $packageCourses = \App\PackageCourse::whereIn('included_package_id', $packageIdsOfCourse)->get()
        ->pluck('package_id')
        ->toArray();

    $learnerWithCourse = \App\CoursesTaken::whereIn('package_id', $packageCourses)
        ->where('is_active', true)
        ->orderBy('updated_at', 'desc')
        ->get();

    $hasActiveUsers = 0;
    if ($learnerWithCourse->count()) {
        $hasActiveUsers = 1;
	}

    $emailOutLog = $course->emailOutLog()->paginate(20);
    ?>

	<div class="col-sm-12 col-md-10 sub-right-content">
		<div class="col-sm-12 col-md-12">
			<form class="pull-right form-inline" method="GET">
				<div class="input-group">
					<input type="hidden" name="section"  value="{{ Request::input('section') }}">
				    <input type="search" class="form-control" name="search" placeholder="{{ trans('site.search') }}" value="{{ is_numeric(Request::input('search'))
				    ?'': Request::input('search') }}">
				    <div class="input-group-btn">
				      <button class="btn btn-default" type="submit">
				        <i class="glyphicon glyphicon-search"></i>
				      </button>
				    </div>
				  </div> 
			</form>
			<button type="button" class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#addLearnerModal">+ {{ trans('site.add-learner') }}</button>
			@if(count($learners) > 0)
				<button type="button" class="btn btn-success margin-bottom" data-toggle="modal" data-target="#sendEmailModal">{{ trans('site.send-email') }}</button>
				<a href="{{ route('learner.course.learner-list-excel', $course->id) }}" class="btn btn-default margin-bottom">{{ trans('site.export-learners') }}</a>
			@endif

			{{-- for webinar pakke only --}}
			@if (/*$hasActiveUsers*/ $course->id == 17)
				<a href="{{ route('learner.course.learner-active-list-excel', $course->id) }}" class="btn btn-info margin-bottom">{{ trans('site.export-active-learners') }}</a>
			@endif

			<ul class="nav nav-tabs margin-top">
				<li class="active"><a href="#learners" data-toggle="tab">Learners</a></li>
				<li><a href="#logs" data-toggle="tab">Email Out Log</a></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane fade in active margin-top" id="learners" role="tabpanel">
					<div class="table-responsive">
						<table class="table table-side-bordered table-white">
							<thead>
							<tr>
								<th>{{ trans_choice('site.learners', 1) }}</th>
								<th>{{ trans('site.learner-id') }}</th>
								<th>{{ trans_choice('site.packages', 1) }}</th>
								<th>{{ trans('site.progress') }}</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@if(count($learners) > 0)
								@foreach( $learners as $learner)
									<tr>
										<td><a href="{{route('admin.learner.show', $learner->user->id)}}">{{$learner->user->full_name}}</a></td>
										<td>{{ $learner->user->id }}</td>
										<td>{{$learner->package->variation}}</td>
										<td>
											<div class="progress learner-progress">
												<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="70"
													 aria-valuemin="0" aria-valuemax="100" style="width:70%">
													70% Complete
												</div>
											</div>
										</td>
										<td>
											<button type="submit" data-toggle="modal" data-target="#removeLearnerModal" class="btn btn-danger btn-xs pull-right btn-remove-learner" data-learner="{{$learner->user->full_name}}" data-package="{{$learner->package->id}}" data-learner-id="{{$learner->user->id}}">{{ trans('site.remove-learner') }}</button>
										</td>
									</tr>
								@endforeach
							@endif
							</tbody>
						</table>
					</div>

					@if(count($course->learners) > 0)
						<div class="pull-right">{!! $learners->appends(Request::all())->render() !!}</div>
						<div class="clearfix"></div>
					@endif
				</div> <!-- end learner-tab -->

				<div class="tab-pane fade margin-top" id="logs" role="tabpanel">
					<div class="table-responsive">
						<table class="table table-side-bordered table-white">
							<thead>
							<tr>
								<th>Subject</th>
								<th>Message</th>
								<th width="200">Date Sent</th>
								<th>From</th>
								<th>Attachment</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
								@foreach($emailOutLog as $log)
									<tr>
										<td>{{ $log->subject }}</td>
										<td>{!!  $log->message !!}</td>
										<td>{{ $log->date_sent }}</td>
										<td>
											{{ $log->from_name ?: 'Forfatterskolen' }} <br>
											{{ $log->from_email ?: 'post@forfaterskolen.no' }}
										</td>
										<td>
											<a href="{{ asset($log->attachment) }}" download>
												{{ $log->attachment
													? \App\Http\AdminHelpers::extractFileName($log->attachment)
													: '' }}
											</a>
										</td>
										<td>
											@if($log->learners)
												<form class="" method="GET">
													<div class="input-group">
														<input type="hidden" name="section" value="{{ Request::input('section') }}">
														<input type="hidden" class="form-control" name="search" value="{{ $log->id }}">
														<button class="btn btn-primary" type="submit">
															Filter Learners
														</button>
													</div>
												</form>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>

					@if(count($emailOutLog) > 0)
						<div class="pull-right">{!! $emailOutLog->appends(Request::all())->render() !!}</div>
						<div class="clearfix"></div>
					@endif
				</div> <!-- end send email out log -->
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>


<!-- Remove Learner Modal -->
<div id="removeLearnerModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{!! str_replace('_LEARNER_', '<strong id="learner_name"></strong>',trans('site.remove-learner-question')) !!}  <strong>{{$course->title}}</strong>?</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('learner.course.remove.learner')}}">
      		{{csrf_field()}}
      		<input type="hidden" name="learner_id">
      		<input type="hidden" name="package_id">
      		<button type="submit" class="btn btn-danger btn-block">{{ trans('site.remove-learner') }}</button>
      	</form>
      </div>
    </div>

  </div>
</div>


<!-- Add Learner Modal -->
<div id="addLearnerModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.add-learner-to') }} {{$course->title}}</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{route('learner.course.add.learner')}}">
      		{{csrf_field()}}
      		<div class="form-group">
      			<select class="form-control select2" name="learner_id" required>
      				<option value="" selected disabled>- Search Learner -</option>
					@if(count($course->learners) > 0)
	      				@foreach(AdminHelpers::courseAddLearners($course->learners->pluck('user_id')->toArray()) as $learner)
	      				<option value="{{$learner->id}}">{{$learner->full_name}} ({{ $learner->email }})</option>
	      				@endforeach
      				@else
	      				@foreach(App\User::where('role', 2)->orderBy('first_name', 'asc')->get() as $learner)
	      				<option value="{{$learner->id}}">{{$learner->full_name}}</option>
	      				@endforeach
      				@endif
      			</select>
      		</div>
      		<div class="form-group">
      			<select class="form-control" name="package_id" required>
      				<option value="" selected disabled>- Select Package -</option>
      				@foreach($course->packages as $package)
      				<option value="{{$package->id}}">{{$package->variation}}</option>
      				@endforeach
      			</select>
      		</div>
      		<div class="text-right">
      			<button type="submit" class="btn btn-primary">{{ trans('site.add-learner') }}</button>
      		</div>
      	</form>
      </div>
    </div>

  </div>
</div>

	<!--send email modal-->

<div id="sendEmailModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.send-email') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{route('learner.course.send-email-to-learners', $course->id)}}"
					  onsubmit="formSubmitted()" enctype="multipart/form-data">
				{{csrf_field()}}

					<div class="form-group">
						<label>{{ trans('site.subject') }}</label>
						<input type="text" class="form-control" name="subject" required>
					</div>
					
					<div class="form-group">
						<label>{{ trans('site.message') }}</label>
						<textarea name="message" id="" cols="30" rows="10" class="form-control editor"></textarea>
					</div>

					<div class="form-group">
						<label style="display: block">From</label>
						<input type="text" class="form-control" placeholder="Name" style="width: 49%; display: inline;"
							   name="from_name">
						<input type="email" class="form-control" placeholder="Email" style="width: 49%; display: inline;"
							   name="from_email">
					</div>

					<div class="form-group">
						<label>Attachment</label>
						<input type="file" class="form-control" name="attachment"
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/msword,
                               application/pdf,
                               application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
					</div>

					<label>Learners</label> <br>
					<input type="checkbox" name="check_all"> <label for="">Check/Uncheck All</label>
					<div class="form-group" style="max-height: 300px; overflow-y: scroll; margin-top: 10px">
						@if(count($course->learners->get()) > 0)
							@foreach( $course->learners->get() as $learner)
								<input type="checkbox" name="learners[]" value="{{ $learner->user->id }}">
								<label>{{ $learner->user->full_name }}</label> <br>
							@endforeach
						@endif
					</div>

					<div class="text-right">
						<input type="submit" class="btn btn-primary" value="{{ trans('site.send') }}" id="send_email_btn">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!--end email modal-->

@stop

@section('scripts')
	<script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
	<script>
		function formSubmitted() {
		    var send_email = $("#send_email_btn");
            send_email.val('Sending....').attr('disabled', true);
		}

		$("[name=check_all]").click(function(){
		   if ($(this).prop('checked')) {
		    	$("[type=checkbox]").prop('checked', true);
        	} else {
               $("[type=checkbox]").prop('checked', false);
		   }
		});

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
	</script>
@stop