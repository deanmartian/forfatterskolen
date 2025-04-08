<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Marketing Plan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <table>
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
                        <ul style="list-style: square; text-align: left">
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
                        <ul style="list-style: square; margin-bottom: 0; text-align: left">
                            @foreach($marketingPlan->questions as $question)
                                @php
                                    $answer = $question->answers[0] ?? null;
                                    $mainAnswer = $answer->main_answer ?? '&nbsp;';
                                    $subAnswers = $answer->sub_answer_decoded ?? [];
                                    $subQuestions = $question->sub_question_decoded ?? [];
                                @endphp
                    
                                <li>{!! $mainAnswer !!}</li>
                    
                                @if(!empty($subQuestions))
                                    <ul>
                                        @foreach($subQuestions as $k => $sub)
                                            <li>{!! $subAnswers[$k] ?? '&nbsp;' !!}</li>
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
</body>
</html>