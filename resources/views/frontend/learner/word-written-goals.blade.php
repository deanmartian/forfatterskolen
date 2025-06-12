@extends('frontend.layout')

@section('title')
    <title>Word Written Goals &rsaquo; Forfatterskolen</title>
@stop

@section('heading')
    Word Written Goals
@stop

@section('styles')
    <style>
        .table-users .table {
            margin-top: 12px;
            margin-bottom: 12px;
            background-color: #fff;
            border: solid 1px #ccc;
        }

        .table thead {
            background-color: #eee;
        }
    </style>
@stop

@section('content')
    <div class="account-container">

    @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content">
            <div class="col-sm-12">

                <div class="row">
                    <div class="col-sm-4">
                        <h3 class="no-margin-top">@yield('heading')</h3>
                    </div>

                    <button class="btn btn-primary pull-right light-blue" data-toggle="modal" data-target="#addGoalModal">
                        Add Goal
                    </button>

                    <a class="btn btn-default pull-right light-blue" href="{{ route('learner.word-written') }}"
                       style="margin-right: 5px">
                        << BACK
                    </a>
                </div>

                <div class="col-sm-8 col-sm-offset-2">

                    <div class="table-users table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>From</th>
                                <th>To</th>
                                <th>Total Words</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($wordsGoal as $goal)
                                <tr>
                                    <td>{{ $goal->from_date }}</td>
                                    <td>{{ $goal->to_date }}</td>
                                    <td>
                                        <a href="#" data-target="#statisticsModal" data-toggle="modal"
                                        class="showStatisticsBtn"
                                        data-action="{{ route('learner.goal-statistic', $goal->id) }}"
                                        data-maximum="{{ $goal->total_words }}"
                                        data-from-month="{{ ucfirst(\App\Http\FrontendHelpers::convertMonthLanguage(date('n', strtotime($goal->from_date)))) }}"
                                       data-to-month="{{ ucfirst(\App\Http\FrontendHelpers::convertMonthLanguage(date('n', strtotime($goal->to_date)))) }}">
                                            {{ $goal->total_words }}
                                        </a>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-xs btn-info editGoalBtn" data-toggle="modal"
                                                data-target="#editGoalModal"
                                        data-action="{{ route('learner.word-written-goals-update', $goal->id) }}"
                                        data-from="{{ strftime('%Y-%m-%d', strtotime($goal->from_date)) }}"
                                        data-to="{{ strftime('%Y-%m-%d', strtotime($goal->to_date)) }}"
                                        data-total-words="{{ $goal->total_words }}"><i class="fa fa-pencil"></i></button>
                                        <button type="button" class="btn btn-xs btn-danger deleteGoalBtn" data-toggle="modal"
                                                data-target="#deleteGoalModal"
                                                data-action="{{ route('learner.word-written-goals-delete', $goal->id) }}"
                                                ><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                        <div class="pull-right">
                            {{ $wordsGoal->render() }}
                        </div>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>

    <div id="addGoalModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Goal</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('learner.word-written-goals.submit') }}">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>From</label>
                            <input type="date" class="form-control" name="from_date" required>
                        </div>

                        <div class="form-group">
                            <label>To</label>
                            <input type="date" class="form-control" name="to_date" required>
                        </div>

                        <div class="form-group">
                            <label>Total Words</label>
                            <input type="number" class="form-control" step="1" name="total_words" required>
                        </div>

                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editGoalModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Goal</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}

                        <div class="form-group">
                            <label>From</label>
                            <input type="date" class="form-control" name="from_date" required>
                        </div>

                        <div class="form-group">
                            <label>To</label>
                            <input type="date" class="form-control" name="to_date" required>
                        </div>

                        <div class="form-group">
                            <label>Total Words</label>
                            <input type="number" class="form-control" step="1" name="total_words" required>
                        </div>

                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteGoalModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete goal</h4>
                </div>
                <div class="modal-body">
                    Are you sure to delete this goal?
                    Warning: This cannot be undone.
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="statisticsModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Statistics</h4>
                </div>
                <div class="modal-body">
                    <div id="chartContainer" style="height: 430px;width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
    <script>
        $(document).ready(function(){
           $(".editGoalBtn").click(function(){
               var action       = $(this).data('action'),
                   modal        = $("#editGoalModal"),
                   from         = $(this).data('from'),
                   to           = $(this).data('to'),
                   total_words  = $(this).data('total-words');

               modal.find('form').attr('action', action);
               modal.find('form').find('input[name=from_date]').val(from);
               modal.find('form').find('input[name=to_date]').val(to);
               modal.find('form').find('input[name=total_words]').val(total_words);
           });

            $(".deleteGoalBtn").click(function(){
                var action       = $(this).data('action'),
                    modal        = $("#deleteGoalModal");

                modal.find('form').attr('action', action);
            });

            var dataPoints = [];

            var options = {
                animationEnabled: true,
                title: {
                    text: ""
                },
                axisY: {
                    title: "Target Goal",
                    suffix: "CHR",
                    includeZero: true
                },
                axisX: {
                    title: "Months"
                },
                data: [{
                    type: "column",
                    yValueFormatString: "#,###"
                    //dataPoints: dataPoints
                }]
            };


            var chart = new CanvasJS.Chart("chartContainer",options);

            $(".showStatisticsBtn").click(function() {
                var action = $(this).data('action');
                var maximum = $(this).data('maximum');
                var from_month = $(this).data('from-month');
                var to_month = $(this).data('to-month');
                //options.axisY.maximum = $(this).data('maximum'); // set a max value for the y axis

                chart.options.data[0].dataPoints = [];
                $.getJSON(action, function(data){
                    $.each(data,function(k,v) {
                        chart.options.data[0].dataPoints.push({
                            label: v.month,
                            y: v.words
                        });
                    });

                    chart.options.data[0].dataPoints.push({
                        label: "Target Total",
                        y: maximum
                    });

                    options.title.text = from_month+' - '+to_month;
                    chart.render();
                });
            });

        });
    </script>
@stop