@extends('backend.layout')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<link rel="stylesheet" href="{{asset('bootstrap-calendar/css/calendar.min.css')}}">
@stop

@section('content')
<div class="container">
	<div class="row">
		<div class="col-sm-12 margin-top" style="background-color: #fff">
			<h3>Section 1</h3>
			<p>
				Do you want to be a pilot reader?
			</p>
			<button class="btn btn-primary">Yes</button>
			<button class="btn btn-default">No</button>

			<hr>

			<p>
				<em>If answer is YES</em> - What kind of genre do you want to read?
			</p>

			<div class="col-sm-6">
				<div class="col-sm-6">
					<input type="checkbox"> Genre 1 <br>
					<input type="checkbox"> Genre 2 <br>
				</div>
				<div class="col-sm-6">
					<input type="checkbox"> Genre 3 <br>
					<input type="checkbox"> Genre 4 <br>
				</div>
			</div>
			<div class="clearfix"></div>

			<h3>Section 2</h3>
			Upload Manuscript
			<input type="file">

			<div class="form-group margin-top">
				<label>Genre</label>
				<select name="" id="" class="form-control">

				</select>
			</div>

			<div class="form-group">
				<label>How many pilot Reader do you want?</label>
				<input type="text" class="form-control" style="width: 10%">
			</div>

		</div>
	</div>
</div>
@stop
