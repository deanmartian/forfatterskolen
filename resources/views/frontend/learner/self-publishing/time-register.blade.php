@extends('frontend.layout')

@section('title')
    <title>Time Register &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
    <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
    <style>
        .fa-clock-red:before {
            content: "\f017";
        }

        .fa-clock-red {
            color: #862736 !important;
            font-size: 20px;
        }
    </style>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">
                <div class="col-md-12 dashboard-course no-left-padding">
                    <div class="card global-card">
                        <div class="card-header">
                            <h1>
                                Time Register
                            </h1>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>{{ trans('site.date') }}</th>
                                    <th>Number of hours</th>
                                    <th>Time used</th>
                                    <th>{{ trans('site.description') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($timeRegisters as $timeRegister)
                                    <tr>
                                        <td>
                                            {{ $timeRegister->project ? $timeRegister->project->name : '' }}
                                        </td>
                                        <td>{{ $timeRegister->date }}</td>
                                        <td>{{ $timeRegister->time }}</td>
                                        <td>{{ $timeRegister->time_used }}</td>
                                        <td>{{ $timeRegister->description }}</td>
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