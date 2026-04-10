@extends('frontend.learner.self-publishing.layout')

@section('page_title', 'Markedsplan &rsaquo; Selvpublisering &rsaquo; Forfatterskolen')

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">
                <a href="{{ route('learner.project.show', $project->id) }}"
                   class="btn btn-outline-brand mb-3">
                    <i class="fa fa-arrow-left"></i> {{ trans('site.back') }}
                </a>

                <div class="col-md-12 dashboard-course no-left-padding">
                    <table class="sp-table">
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

                                    <button class="btn btn-brand btn-sm float-end answerMarketingPlanBtn"
                                            data-bs-toggle="modal" data-bs-target="#marketingPlanAnswerModal"
                                            data-action="{{ route('learner.project.save-marketing-qa', $project->id) }}"
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

    <div id="marketingPlanAnswerModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content sp-modal">
                <div class="sp-modal__header">
                    <h3 class="sp-modal__title">
                        <i class="fas fa-comments" style="color:var(--brand-primary);margin-right:6px"></i>
                        <span class="marketing-plan-title">{{ trans('site.answer-text') }}</span>
                    </h3>
                    <button type="button" class="sp-modal__close" data-bs-dismiss="modal" aria-label="Lukk">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="" onsubmit="disableSubmit(this)" data-sp-validate>
                    {{ csrf_field() }}
                    <div class="sp-modal__body">
                        <div class="question-container"></div>
                    </div>
                    <div class="sp-modal__footer">
                        <button type="button" class="btn-outline-brand" data-bs-dismiss="modal">Avbryt</button>
                        <button type="submit" class="btn-brand">{{ trans('site.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(".answerMarketingPlanBtn").click(function() {
            let plan = $(this).data('plan');
            let action = $(this).data('action');
            let modal = $("#marketingPlanAnswerModal");
            let form = modal.find('form');
            modal.find('form').attr('action', action);
            modal.find('.marketing-plan-title').text(plan.name);

            let container = form.find(".question-container");
            container.empty();
            let questions = "";

            $.each(plan.questions, function(qk, question) {
                let number = qk + 1;
                let answer = question.answers[0] && question.answers[0].main_answer ? question.answers[0].main_answer : '';

                questions += "<div class='sp-form-group'>";
                questions += "<label class='sp-label'>" + question.main_question + "</label>";
                questions += "<textarea class='sp-input sp-textarea' name='arr[" + number + "][main_answer]' rows='5'>"
                    + answer + "</textarea>";
                questions += "<input type='hidden' name='arr[" + number + "][main_question_id]'" +
                    " value='" + question.id + "'>";

                    if (question.sub_question_decoded) {
                        questions += "<div class='sub-questions' style='margin-left:24px;margin-top:12px'>";
                            $.each(question.sub_question_decoded, function(k, sub_question){
                                let answer = question.answers[0] && question.answers[0].sub_answer_decoded[k]
                                    ? question.answers[0].sub_answer_decoded[k] : '';
                                questions += "<div class='sp-form-group'>";
                                    questions += "<label class='sp-label'>" + sub_question + "</label>";
                                    questions += "<textarea class='sp-input sp-textarea' name='arr[" + number + "][sub_answer][]' rows='5'>"
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