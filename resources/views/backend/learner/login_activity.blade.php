@extends('backend.layout')

@section('styles')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
<title>{{ trans('site.login-activity-for') }} {{ $login->created_at }}</title>
@stop

@section('content')

<div class="page-toolbar">
    <h3><i class="fa fa-file-text-o"></i> {{ trans('site.login-activity-for') }} {{ date('M d, Y', strtotime($login->created_at)) }}</h3>
    <div class="clearfix"></div>
</div>


<div class="col-sm-12 margin-top">
    <div class="table-responsive">
        <table class="table table-side-bordered table-white">
            <thead>
            <tr>
                <th>{{ trans('site.activity') }}</th>
                <th>{{ trans('site.date') }}</th>
            </tr>
            </thead>
            <tbody>

            @foreach($login->loginActivity as $activity)
                <tr>
                    <td> {{ $activity->activity }} </td>
                    <td> {{ $activity->created_at }} </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>
</div>

@stop