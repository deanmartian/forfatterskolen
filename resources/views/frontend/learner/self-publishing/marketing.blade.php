@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Marketing &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            @if($standardProject)
                <a href="{{ route('learner.marketing-download') }}" class="btn btn-primary mb-3">
                    <i class="fa fa-download"></i> Download
                </a>
            @endif
            <div class="card card-global">
                <div class="table-users table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ trans('site.name') }}</th>
                                <th>{{ trans('site.author-portal.questions') }}</th>
                                <th>{{ trans('site.answer-text') }}</th>
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
                                    <td>
                                        @foreach($marketingPlan->questions as $question)
                                            <?php
                                                $answer = isset($question->answers[0]) ? $question->answers[0] : NULL;
                                            ?>
                                            @if($answer)
                                                <ul style="list-style: square; margin-bottom: 0">
                                                    <li>{{ $answer ? $question->answers[0]->main_answer : '' }} </li>

                                                    @if($question->sub_question_decoded)
                                                        <ul>
                                                            @foreach($question->sub_question_decoded as $k => $subQuestion)
                                                                <li>
                                                                    {{ $answer && isset($answer->sub_answer_decoded[$k])
                                                                    ? $question->answers[0]->sub_answer_decoded[$k] : '' }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </ul>
                                            @endif
                                        @endforeach
                                        <button class="btn btn-success btn-xs pull-right answerMarketingPlanBtn"
                                            data-toggle="modal" data-target="#marketingPlanAnswerModal"
                                            data-action="{{ route('learner.project.save-marketing-qa', $standardProject->id) }}"
                                            data-plan="{{ json_encode($marketingPlan) }}">
                                            {{ trans('site.answer-text') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="marketingPlanAnswerModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        {{ trans('site.answer-text') }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <div class="question-container"></div>

                        <button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(".answerMarketingPlanBtn").click(function() {
            let plan = $(this).data('plan');
            let action = $(this).data('action');
            let modal = $("#marketingPlanAnswerModal");
            let form = modal.find('form');
            modal.find('form').attr('action', action);
            modal.find('.modal-title').text(plan.name);

            let container = form.find(".question-container");
            container.empty();
            let questions = "";

            $.each(plan.questions, function(qk, question) {
                let number = qk + 1;
                let answer = question.answers[0] && question.answers[0].main_answer ? question.answers[0].main_answer : '';

                questions += "<div class='form-group'>";
                questions += "<label>" + question.main_question + "</label>";
                questions += "<textarea type='text' class='form-control' name='arr[" + number + "][main_answer]' rows='5'>"
                    + answer + "</textarea>";
                questions += "<input type='hidden' class='form-control' name='arr[" + number + "][main_question_id]'" +
                    " value='" + question.id + "'>";

                    if (question.sub_question_decoded) {
                        questions += "<div class='sub-questions ml-5'>";
                            $.each(question.sub_question_decoded, function(k, sub_question){
                                let answer = question.answers[0] && question.answers[0].sub_answer_decoded[k]
                                    ? question.answers[0].sub_answer_decoded[k] : '';
                                questions += "<div class='form-group'>";
                                    questions += "<label>" + sub_question + "</label>";
                                    questions += "<textarea type='text' class='form-control' name='arr[" + number + "][sub_answer][]' rows='5'>"
                                        + answer + "</textarea>";
                                questions += "</div>";
                            });
                        questions += "</div>";
                    }

                questions += "</div>";
            });

            container.append(questions);
        });
    </script>
@stop