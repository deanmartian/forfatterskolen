{{-- @extends('frontend.layout') --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title>{{$courseTaken->package->course->title}} &rsaquo; Forfatterskolen</title>
@stop


@section('content')
<div class="learner-container course-show-page">
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<h1>
					{{ trans('site.learner.lesson') }}
					<a href="{{route('learner.course')}}" class="color-black font-barlow-regular ml-3">
						{{ trans('site.learner.view-all-course') }}
					</a>
				</h1>

				<div class="theme-tabs">
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item">
							<a data-toggle="tab" href="#lessons" class="nav-link active" role="tab">
								<span>{{ trans('site.learner.lesson') }}</span> <!-- check if webinar-pakke -->
							</a>
						</li>
						<li class="nav-item">
							<a data-toggle="tab" href="#course-details" class="nav-link" role="tab">
								<span>
									{{ trans('site.learner.course-plan') }}
								</span>
							</a>
						</li>
						<li class="nav-item">
							<a data-toggle="tab" href="#webinars" class="nav-link" role="tab">
								<span>
									{{ trans('site.learner.course-webinar') }}
								</span> <!-- check if webinar-pakke -->
							</a>
						</li>
					</ul>

					<div class="tab-content">
						@if( $courseTaken->package->course->lessons->count() > 0 )
							<div id="lessons" class="tab-pane fade in active" role="tabpanel">
								<div class="row">
									@foreach($courseTaken->package->course->lessons as $lesson)
										<div class="col-md-4 learner-course-lesson mb-5">
											@if(\App\Http\FrontendHelpers::isLessonAvailable($courseTaken->started_at, $lesson->delay, $lesson->period) ||
											\App\Http\FrontendHelpers::hasLessonAccess($courseTaken, $lesson))
												<a href="{{route('learner.course.lesson', ['course_id' => $courseTaken->package->course->id, 'id' => $lesson->id])}}">
													<div class="panel panel-default global-panel">
														<div class="panel-body">
															<span class="label label-red font-weight-normal mb-3 d-inline-block">
																{{ trans('site.learner.available') }}
															</span>
															<h3 class="color-black font-weight-normal font-barlow-regular">{{$lesson->title}}</h3>
														</div>
														<div class="bottom-line"></div>
													</div>
												</a>
											@else
												<div class="panel panel-default global-panel inactive">
													<div class="panel-body">
														<h3 class="font-weight-normal font-barlow-regular">{{$lesson->title}}</h3>
														<small>
															{{ trans('site.learner.available-at') }}
															{{\App\Http\FrontendHelpers::lessonAvailability($courseTaken->started_at, $lesson->delay, $lesson->period)}}
														</small>
													</div>
													<div class="bottom-line"></div>
												</div>
											@endif
										</div>
									@endforeach
								</div>
							</div>

							<div id="course-details" class="tab-pane fade" role="tabpanel">
								<div class="panel panel-default">
									<div class="panel-body">
										<div class="row">
											<div class="col-sm-12 col-lg-3">
												<div class="course-list-thumb" data-bg="https://www.forfatterskolen.no/{{$courseTaken->package->course->course_image}}"></div>
											</div>
											<div class="col-sm-12 col-lg-9 course-list-details">
												<p class="pull-right">
													<i class="fa fa-calendar"></i>&nbsp;
													{{ trans('site.learner.started') }} - {{date_format(date_create($courseTaken->started_at), 'M d, Y H.i') }}<br />

													<i class="fa fa-calendar-times-o"></i>&nbsp;
													{{ trans('site.learner.expires-on') }} -
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
													<li><i class="fa fa-folder-o"></i>&nbsp;{{count($courseTaken->package->course->lessons)}}
														{{ trans('site.learner.lesson') }}
													</li>
												</ul>
												@if( $courseTaken->package->shop_manuscripts->count() > 0 ||
                                                    $courseTaken->package->included_courses->count() > 0 ||
                                                    $courseTaken->package->workshops > 0
                                                    )
													<strong>
														{{ trans('site.front.our-course.show.includes') }}
													</strong><br />
													@if( $courseTaken->package->shop_manuscripts->count() > 0 )
														@foreach( $courseTaken->package->shop_manuscripts as $shop_manuscripts )
															{{ $shop_manuscripts->shop_manuscript->title }} <br />
														@endforeach
													@endif

													@if( $courseTaken->package->workshops )
														{{ $courseTaken->package->workshops }} {{ trans('site.learner.workshops') }} <br />
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

							<div id="webinars" class="tab-pane fade" role="tabpanel">
								<div class="panel panel-default">
									<div class="panel-heading">
										<a class="btn btn-primary pull-right btn-xs no-after" href="{{ route('learner.course-webinar') }}">
											{{ trans('site.learner.see-everything') }}
										</a>
										<i class="fa fa-play-circle-o"></i>&nbsp;&nbsp;{{ trans('site.learner.webinars') }}
									</div>
									<div class="table-responsive">
										<table class="table table-global">
											<thead>
											<tr>
												<th>
													{{ trans('site.learner.webinar') }}
												</th>
												<th>
													{{ trans('site.learner.date-start') }}
												</th>
											</tr>
											</thead>
											<tbody>
											@foreach( $courseTaken->package->course->activeWebinars as $webinar )
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

                            <?php $isHidden = 1?>
							@if( $courseTaken->package->manuscripts_count > 0 && !$isHidden)
							<!-- Manuscripts Uploaded -->
								<div class="col-sm-12">
									<div class="panel panel-default">
										<div class="panel-heading">
											@if( $courseTaken->manuscripts->count() < $courseTaken->package->manuscripts_count  )
												<button class="btn btn-primary pull-right btn-xs" data-toggle="modal" data-target="#addManuscriptModal">
													+ {{ trans('site.learner.course-show.upload-manuscript') }}
												</button>
											@else
												<button class="btn btn-primary disabled pull-right btn-xs">
													+ {{ trans('site.learner.course-show.upload-manuscript') }}
												</button>
											@endif
											<i class="fa fa-file-word-o"></i>&nbsp;&nbsp;{{ trans('site.learner.manuscripts-uploaded') }}
										</div>
										<div class="table-responsive">
											<table class="table">
												<thead>
												<tr>
													<th>
														{{ trans('site.learner.script') }}
													</th>
													<th>
														{{ trans('site.learner.word') }}
													</th>
													<th>
														{{ trans('site.learner.date-uploaded') }}
													</th>
													<th>
														{{ trans('site.earner.status') }}
													</th>
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
																<span class="label label-success">
																	{{ trans('site.learner.finished') }}
																</span>
															@elseif( $manuscript->status == 'Started' )
																<span class="label label-primary">
																	{{ trans('site.learner.started') }}
																</span>
															@elseif( $manuscript->status == 'Not started' )
																<span class="label label-warning">
																	{{ trans('site.learner.not-started') }}
																</span>
															@endif
														</td>
														<td>
															<a class="btn btn-primary btn-xs pull-right"
															   href="{{ route('learner.manuscript.show', $manuscript->id) }}">
																{{ trans('site.learner.look-at-the-manuscript') }}
															</a>
														</td>
													</tr>
												@endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
							@endif

						@endif
					</div> <!-- end tab-content-->
				</div> <!-- end theme tabs-->
			</div> <!-- end col-sm-12 -->
		</div>
	</div>
</div>



@if( $courseTaken->manuscripts->count() < $courseTaken->package->manuscripts_count )
<div id="addManuscriptModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">
			{{ trans('site.learner.course-show.upload-manuscript') }}
		</h3>
		  <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="{{ route('learner.course.uploadManuscript', $courseTaken->id) }}">
      		{{ csrf_field() }}
      		<div class="form-group">
      		* {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}</div>
      		<div class="form-group row">
      			<div class="col-sm-6">
      				<input type="file" class="form-control" required name="file"
						   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
      			</div>
      		</div>
      		<button type="submit" class="btn btn-primary pull-right">
				{{ trans('site.learner.course-show.upload-manuscript') }}
			</button>
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
		  	{{ trans('site.learner.upload-manuscript-success') }}
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

