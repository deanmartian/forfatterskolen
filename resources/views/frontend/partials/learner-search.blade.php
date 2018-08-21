<form role="search" class="row" method="get" action="{{ route('learner.account.search') }}">
		<div class="col-sm-4">
			<h3 class="no-margin-top">@yield('heading')</h3>
		</div>
		<div class="col-sm-3 pull-right">
		<div class="input-group">
		  	<input type="text" class="form-control" name="search" value="{{ Request::input('search') }}" placeholder="Søk kurs, oppgaver, workshops, webinars.." required>
		    <span class="input-group-btn">
		    	<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
		    </span>
		</div>
	</div>
</form>
<br />