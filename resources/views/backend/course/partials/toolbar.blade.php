<div class="page-toolbar">
	<h3><i class="fa fa-file-text-o"></i> {{$course->title}}</h3>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="get" action="">
				<div class="input-group">
				  	<input type="text" class="form-control" placeholder="Search Course, Webinar, Manuscript, etc">
				    <span class="input-group-btn">
				    	<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
				    </span>
				</div>
			</form>
		</div>
	  	<div class="form-group">
	  		<a href="{{url('course')}}" class="btn btn-primary">{{ trans('site.view-all-courses') }}</a>
	  	</div>
	</div>
	<div class="clearfix"></div>
</div>