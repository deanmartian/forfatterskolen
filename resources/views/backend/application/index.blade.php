@extends('backend.layout')

@section('title')
<title>Application &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> Applications</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ trans('site.id') }}</th>
                        <th>{{ trans('site.name') }}</th>
                        <th>{{ trans_choice('site.emails', 1) }}</th>
                        <th>File</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($applications as $application)
                        <tr>
                            <td>
                                {{ $application['id'] }}
                            </td>
                            <td>
                                {{ $application['first_name'] ." ". $application['last_name'] }}
                            </td>
                            <td>
                                {{ $application['email'] }}
                            </td>
                            <td>
                                {!! $application['file_link'] !!}
                            </td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pull-right">
            {{$applications->appends(Request::all())->render()}}
        </div>
    </div>
@endsection