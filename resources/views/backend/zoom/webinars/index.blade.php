@extends('backend.layout')


@section('title')
    <title>Zoom Webinars</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> All Webinars</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a class="btn btn-success margin-top" href="{{route('admin.zoom.webinar.create', $user_id)}}">Add Webinar</a>
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Topic</th>
                    <th>Date</th>
                    <th>Join Url</th>
                </tr>
                </thead>
                <tbody>
                @foreach($webinars as $webinar)
                    <tr>
                        <td>
                            <a href="{{ route('admin.zoom.webinar.edit', $webinar->id) }}">
                                {{ $webinar->topic }}
                            </a>
                        </td>
                        <td>
                            <?php
                            // use the the appropriate timezone for your stamp
                            $timestamp = DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $webinar->start_time, new DateTimeZone('UTC'));

                            // set it to whatever you want to convert it
                            $timestamp->setTimeZone(new DateTimeZone($webinar->timezone));
                            echo $timestamp->format('Y-m-d H:i A');
                            ?>
                        </td>
                        <td>
                            <a href="{{ $webinar->join_url }}" target="_blank">{{ $webinar->join_url }}</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop