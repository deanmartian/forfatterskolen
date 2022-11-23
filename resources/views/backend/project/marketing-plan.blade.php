@extends($layout)

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Marketing Plans</h3>
        <a href="{{ route($backRoute, $project->id) }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="col-sm-12 margin-top">
        <div class="table-responsive">
            <div class="table-users table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Questions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($marketingPlans as $marketingPlan)
                        <tr>
                            <td>{{ $marketingPlan->name }}</td>
                            <td>
                                <ul style="list-style: square">
                                    @foreach($marketingPlan->questions as $question)
                                        <li>{{ $question->main_question }} </li>

                                        @if($question->sub_question_decoded)
                                            <ul>
                                                @foreach($question->sub_question_decoded as $subQuestion)
                                                    <li>{{ $subQuestion }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop