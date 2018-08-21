@extends('backend.layout')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="col-sm-12 col-md-10 dashboard-left">
	<div class="row">
		<div class="col-sm-12 col-md-5">
			<!-- Summary  -->
			<div class="row">
				<div class="col-xs-6 col-sm-6">
					<div class="panel panel-total-courses text-center text-white">
						<div class="panel-body">
							<h3>{{count(App\Course::all())}}</h3>
							Total Courses
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-sm-6">
					<div class="panel panel-default text-center">
						<div class="panel-body">
							<h3>{{App\Manuscript::count()}}</h3>
							Manuscripts Uploaded
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-6 col-sm-6">
					<div class="panel panel-default text-center">
						<div class="panel-body">
							<h3>{{count(App\User::where('role', 2)->get())}}</h3>
							Total Learners
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-sm-6">
					<div class="panel panel-total-revenue text-center text-white">
						<div class="panel-body">
							<h3>{{AdminHelpers::currencyFormat(App\Transaction::sum('amount'))}}</h3>
							Total Revenue
						</div>
					</div>
				</div>
			</div>


			<!-- My assigned manuscripts -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>My assigned manuscripts</h4></div>
						<table class="table">
						    <thead>
						      <tr>
						        <th>Manuscript</th>
						        <th>Type</th>
						        <th>Status</th>
						      </tr>
						    </thead>
						    <tbody>
						    	@foreach( $assigned_course_manuscripts as $assigned )
						    	<tr>
						    		<td><a href="{{ route('admin.manuscript.show', $assigned->id) }}">
						    			@if( $assigned->filename )
						    			{{ basename($assigned->filename) }}
						    			@else
						    			<em>No document</em>
						    			@endif
						    		</a></td>
						    		<td>Course</td>
						    		<td>
							  			@if( $assigned->status == 'Finished' )
										<span class="label label-success">Finished</span>
										@elseif( $assigned->status == 'Started' )
										<span class="label label-primary">Started</span>
										@elseif( $assigned->status == 'Not started' )
										<span class="label label-warning">Not started</span>
										@endif
						    		</td>
						    	</tr>
							    @endforeach
						    	@foreach( $assigned_shop_manuscripts as $assigned )
						    	<tr>
						    		<td><a href="{{ route('shop_manuscript_taken', ['id' => $assigned->user->id, 'shop_manuscript_taken_id' => $assigned->id]) }}">
						    			@if( $assigned->file )
						    			{{ basename($assigned->file) }}
						    			@else
						    			<em>No document</em>
						    			@endif
						    		</a></td>
						    		<td>Shop manuscript</td>
						    		<td>
							  			@if( $assigned->status == 'Finished' )
										<span class="label label-success">Finished</span>
										@elseif( $assigned->status == 'Started' )
										<span class="label label-primary">Started</span>
										@elseif( $assigned->status == 'Not started' )
										<span class="label label-warning">Not started</span>
										@endif
						    		</td>
						    	</tr>
							    @endforeach
						    </tbody>
						</table>
					</div>
				</div>
			</div>


			<!-- Today's Sales -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>Today's Sales</h4></div>
						<table class="table">
						    <thead>
						      <tr>
						        <th>Course</th>
						        <th>Enrolled by</th>
						        <th>Plan</th>
						        <th>Price</th>
						      </tr>
						    </thead>
						    <tbody>
						      <tr>
						        <td>John</td>
						        <td>Doe</td>
						        <td>john@example.com</td>
						        <td>Kr 8,000</td>
						      </tr>
						      <tr>
						        <td>John</td>
						        <td>Doe</td>
						        <td>john@example.com</td>
						        <td>Kr 8,000</td>
						      </tr>
						    </tbody>
						</table>
						<div class="panel-footer">
							<div class="text-center">
								<a href="" class="btn btn-success btn-sm">Know More</a>
							</div>
						</div>
					</div>
				</div>
			</div>


			<!-- Ongoing Webinars -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>Ongoing Webinars</h4></div>
						<table class="table">
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
						</table>
					</div>
				</div>
			</div>

		</div>

		<div class="col-sm-12 col-md-7">
			<!-- Recent Assignments -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default ">
						<div class="panel-heading"><h4>Recent Assignments</h4></div>
						<table class="table">
						    <thead>
						      <tr>
						        <th>Title</th>
						        <th>Submitted By</th>
						        <th>Course</th>
						        <th>Date</th>
						      </tr>
						    </thead>
						    <tbody>
						      <tr>
						        <td>John</td>
						        <td>Doe</td>
						        <td>john@example.com</td>
						        <td>Kr 8,000</td>
						      </tr>
						      <tr>
						        <td>John</td>
						        <td>Doe</td>
						        <td>john@example.com</td>
						        <td>Kr 8,000</td>
						      </tr>
						    </tbody>
						</table>
					</div>
				</div>
			</div>



			<!-- Pending Courses -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>Pending Courses</h4></div>
						<div class="table-responsive">
							<table class="table">
							    <thead>
							      <tr>
							        <th>Course</th>
							        <th>Learner</th>
							        <th>Date Ordered</th>
							        <th></th>
							      </tr>
							    </thead>
							    <tbody>
							    	@foreach( $pending_courses as $pending_course )
							      	<tr>
								        <td>{{ $pending_course->package->course->title }}</td>
								        <td>{{ $pending_course->user->full_name }}</td>
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
						<div class="panel-heading"><h4>Pending Shop Manuscripts</h4></div>
						<table class="table">
						    <thead>
						      <tr>
						        <th>Manuscript</th>
						        <th>Learner</th>
						        <th>Date Ordered</th>
						        <th></th>
						      </tr>
						    </thead>
						    <tbody>
						    	@foreach( $pending_shop_manuscripts as $pending_shop_manuscript )
						      	<tr>
							        <td>{{ $pending_shop_manuscript->shop_manuscript->title }}</td>
							        <td>{{ $pending_shop_manuscript->user->full_name }}</td>
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
						<div class="panel-heading"><h4>Pending Workshops</h4></div>
						<table class="table">
						    <thead>
						      <tr>
						        <th>Manuscript</th>
						        <th>Learner</th>
						        <th>Date Ordered</th>
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



			<!-- Manuscripts Received -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-heading"><h4>Manuscripts Received</h4></div>
						<table class="table">
						    <thead>
						      <tr>
						        <th>Title</th>
						        <th>Uploaded By</th>
						        <th>Assigned To</th>
						        <th>Date</th>
						      </tr>
						    </thead>
						    <tbody>
						      <tr>
						        <td>John</td>
						        <td>Doe</td>
						        <td>john@example.com</td>
						        <td>Kr 8,000</td>
						      </tr>
						      <tr>
						        <td>John</td>
						        <td>Doe</td>
						        <td>john@example.com</td>
						        <td>Kr 8,000</td>
						      </tr>
						    </tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Welcome Email -->
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default ">
						<div class="panel-heading">
							<button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#editEmailModal"><i class="fa fa-pencil"></i></button>
							<h4>Welcome Email</h4>
						</div>
						<div class="panel-body">
							{!! nl2br(App\Settings::welcomeEmail()) !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="col-sm-12 col-md-2 dashboard-right">
	<h3 class="actitities-header">Recent Activities</h3>
	<div class="dashboard-activity" style="color: green">
		<p>
			<span class="activ-time">10 minutes ago</span>
			<a href="">Mark</a> commented on a manuscript
		</p>
	</div>
	<div class="dashboard-activity" style="color: yellow">
		<p>
			<span class="activ-time">15 minutes ago</span>
			<a href="">Jay</a> uploaded a new manuscript
		</p>
	</div>
</div>


<div id="editEmailModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Welcome Email</h4>
      </div>
      <div class="modal-body">
      	<form method="POST" action="{{ route('admin.settings.update.welcome_email') }}">
      		{{ csrf_field() }}
      		<textarea class="form-control" name="welcome_email" rows="6">{{ App\Settings::welcomeEmail() }}</textarea>
      		<div class="text-right margin-top">
      			<button type="submit" class="btn btn-primary">Save</button>
      		</div>
      	</form>
      </div>
    </div>

  </div>
</div>
@stop