@extends('frontend.layout')

@section('title')
<title>{{$courseTaken->package->course->title}} &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="account-container">
	
	@include('frontend.partials.learner-menu')

	<div class="col-sm-12 col-md-10 sub-right-content">

		<div class="col-sm-12">
			<a href="{{route('learner.course')}}" class="btn btn-default margin-bottom"><i class="fa fa-angle-left"></i>&nbsp;Se på alle kurs</a>
		</div>

	@if( $courseTaken->package->course->lessons->count() > 0 )
		<!-- Lessons -->
			<div class="col-sm-12">
				<h3 class="no-margin-top">Leksjoner</h3>
				<div class="row">
					@foreach($courseTaken->package->course->lessons as $lesson)
						<div class="col-sm-4 learner-course-lesson">
							@if(FrontendHelpers::isLessonAvailable($courseTaken->started_at, $lesson->delay, $lesson->period) ||
                            FrontendHelpers::hasLessonAccess($courseTaken, $lesson))
								<a class="panel panel-default panel-lesson" href="{{route('learner.course.lesson', ['course_id' => $courseTaken->package->course->id, 'id' => $lesson->id])}}">
									<div class="panel-body">
										<h4>{{$lesson->title}}</h4>
										<span class="label label-primary">Tilgjengelig</span>
									</div>
								</a>
							@else
								<div class="panel panel-default panel-lesson inactive">
									<div class="panel-body">
										<h4>{{$lesson->title}}</h4>
										<small>Tilgjengelig på {{FrontendHelpers::lessonAvailability($courseTaken->started_at, $lesson->delay, $lesson->period)}}</small>
									</div>
								</div>
							@endif
						</div>
					@endforeach
				</div>
			</div>
		@endif

		<div class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12 col-md-3">
							<div class="course-list-thumb" style="background-image: url({{$courseTaken->package->course->course_image}})"></div>
						</div>
						<div class="col-sm-12 col-md-9 course-list-details">
							<p class="pull-right">
								<i class="fa fa-calendar"></i>&nbsp; Startet - {{date_format(date_create($courseTaken->started_at), 'M d, Y H.i') }}<br />

								<i class="fa fa-calendar-times-o"></i>&nbsp; Expires on -
								@if ($courseTaken->end_date)
									{{ $courseTaken->end_date }} {{Carbon\Carbon::parse($courseTaken->started_at)->format('H.i') }}
								@else
									{{Carbon\Carbon::parse($courseTaken->started_at)->addyears($courseTaken->years)->format('M d, Y H.i') }}
								@endif
							</p>
							<h3>{{$courseTaken->package->course->title}}</h3>
							<p>
							{!! $courseTaken->package->course->description !!}
							</p>
							<ul class="course-list-meta margin-bottom">
								<li><i class="fa fa-folder-o"></i>&nbsp;{{count($courseTaken->package->course->lessons)}} Lessons</li>
							</ul>
							@if( $courseTaken->package->shop_manuscripts->count() > 0 || 
								$courseTaken->package->included_courses->count() > 0 ||
								$courseTaken->package->workshops > 0
								)
								<strong>Inkluderer</strong><br />
								@if( $courseTaken->package->shop_manuscripts->count() > 0 )
								@foreach( $courseTaken->package->shop_manuscripts as $shop_manuscripts )
								{{ $shop_manuscripts->shop_manuscript->title }} <br />
								@endforeach
								@endif

								@if( $courseTaken->package->workshops )
								{{ $courseTaken->package->workshops }} workshops <br />
								@endif

								@if( $courseTaken->package->included_courses->count() > 0 )
								@foreach( $courseTaken->package->included_courses as $included_course )
								{{ $included_course->included_package->course->title }} ({{ $included_course->included_package->variation }}) <br />
								@endforeach
								@endif
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>


		@if( $courseTaken->package->course->webinars->count() > 0 )
		<!-- Course Webinars -->
		<div class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<a class="btn btn-primary pull-right btn-xs" href="{{ route('learner.course-webinar') }}">Se Alt</a>
					<i class="fa fa-play-circle-o"></i>&nbsp;&nbsp;Webinars
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Webinar</th>
								<th>Dato start</th>
							</tr>
						</thead>
						<tbody>
							@foreach( $courseTaken->package->course->webinars as $webinar )
							<tr>
								<td><strong>{{ $webinar->title }}</strong></td>
								<td>{{ date_format(date_create($webinar->start_date), 'M d, Y H.i') }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
		@endif


		<?php $isHidden = 1?>
		@if( $courseTaken->package->manuscripts_count > 0 && !$isHidden)
		<!-- Manuscripts Uploaded -->
		<div class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					@if( $courseTaken->manuscripts->count() < $courseTaken->package->manuscripts_count  )
					<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addManuscriptModal">+ Last opp manuskript</button>
					@else
					<button class="btn btn-primary disabled pull-right btn-xs">+ Last opp manuskript</button>
					@endif
					<i class="fa fa-file-word-o"></i>&nbsp;&nbsp;Manuskripter opplastet
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Manus</th>
								<th>Ord</th>
								<th>Dato opplastet</th>
								<th>Status</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach( $courseTaken->manuscripts as $manuscript )
							<tr>
								<td><a href="{{ route('learner.manuscript.show', $manuscript->id) }}">{{ basename($manuscript->filename) }}</a></td>
								<td>{{ $manuscript->word_count }}</td>
								<td>{{ date_format(date_create($manuscript->created_at), 'M d, Y H.i') }}</td>
								<td>
									@if( $manuscript->status == 'Finished' )
									<span class="label label-success">Finished</span>
									@elseif( $manuscript->status == 'Started' )
									<span class="label label-primary">Started</span>
									@elseif( $manuscript->status == 'Not started' )
									<span class="label label-warning">Not started</span>
									@endif
								</td>
								<td><a class="btn btn-primary btn-xs pull-right" href="{{ route('learner.manuscript.show', $manuscript->id) }}">Se på manuskript</a></td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
		@endif


	</div>
	<div class="clearfix"></div>
</div>



@if( $courseTaken->manuscripts->count() < $courseTaken->package->manuscripts_count )
<div id="addManuscriptModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Upload Manuscript</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="{{ route('learner.course.uploadManuscript', $courseTaken->id) }}">
      		{{ csrf_field() }}
      		<div class="form-group">
      		* Godkjente fil formater er DOCX, PDF og ODT.</div>
      		<div class="form-group row">
      			<div class="col-sm-6">
      				<input type="file" class="form-control" required name="file" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
      			</div>
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">Upload manuscript</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>

  </div>
</div>
@endif

<div id="submitSuccessModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-body text-center">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <div style="color: green; font-size: 24px"><i class="fa fa-check"></i></div>
		  	Manuset ditt har blitt levert!
		  </div>
		</div>
	</div>
</div>

@stop

@section('scripts')
<script>
	@if (Session::has('success'))
	$('#submitSuccessModal').modal('show');
	@endif
</script>
@stop

