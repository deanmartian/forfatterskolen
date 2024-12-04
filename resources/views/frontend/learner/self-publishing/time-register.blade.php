@extends('frontend.learner.self-publishing.layout')

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
                                {{ trans('site.author-portal-menu.time-register') }}
                            </h1>
                        </div>
                        <div class="card-body" style="padding: 0">
                            <table class="table" style="margin-bottom: 0">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.project') }}</th>
                                    <th>{{ trans('site.date') }}</th>
                                    <th>{{ trans('site.author-portal.number-of-hours') }}</th>
                                    <th>{{ trans('site.author-portal.time-used') }}</th>
                                    {{-- <th>{{ trans('site.description') }}</th> --}}
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
                                            <td>
                                                <a href="#" data-toggle="modal" data-target="#timeUsedModal" class="timeUsedBtn"
                                                data-time-used-list="{{ json_encode($timeRegister->usedTimes) }}">
                                                    {{ 
                                                        $timeRegister->usedTimesDurationSum && isset($timeRegister->usedTimesDurationSum[0])
                                                        ? $timeRegister->usedTimesDurationSum[0]->total_duration 
                                                        : '' 
                                                    }}
                                                </a>
                                            </td>
                                            {{-- <td>{{ $timeRegister->description }}</td> --}}
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

    <div id="timeUsedModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        {{ trans('site.author-portal.time-used') }}
                    </h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive margin-top">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{ trans('site.date') }}</th>
                                <th>{{ trans('site.author-portal.time-used') }}</th>
                                <th>{{ trans('site.description') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script>
    $(".timeUsedBtn").click(function(){
        let timeUsedList = $(this).data('time-used-list');
        let modal = $("#timeUsedModal");

        modal.find('tbody').empty();
        let tr = "";
        $.each(timeUsedList, function(k, record) {
            console.log(record);
            tr += "<tr>";
                tr += "<td>" + record.date + "</td>";
                tr += "<td>" + record.time_used + "</td>";
                tr += "<td>" + record.description + "</td>";
            tr += "</tr>";
        });

        modal.find('tbody').append(tr);
    });
</script>
@stop