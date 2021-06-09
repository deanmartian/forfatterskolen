@extends('backend.layout')

@section('title')
<title>Admins &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-users"></i> {{ $user->fullname }}</h3>

        <div class="clearfix"></div>
    </div>

<div class="col-sm-12 dashboard-left">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h4>{{ str_replace('_COUNT_', 15 , trans('site.last-login-count')) }}</h4>

                    <div class="table-responsive" style="margin-top: 10px">
                        <table class="table" style="margin-bottom: 0" id="myTable">
                            <thead>
                            <tr>
                                <th>{{ trans('site.time') }}</th>
                                <th>{{ trans('site.ip-address') }}</th>
                                <th>{{ trans('site.country') }}</th>
                                <th>{{ trans('site.provider') }}</th>
                                <th>{{ trans('site.platform') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach( $user->logins as $login )
                                <tr>
                                    <td>
                                        <a href="{{route('admin.learner.login_activity', $login->id)}}" target="_blank">
                                            {{ $login->created_at }}
                                        </a>
                                    </td>
                                    <td>{{ $login->ip }}</td>
                                    <td>{{ $login->country }}</td>
                                    <td>{{ $login->provider }}</td>
                                    <td>{{ $login->platform }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop