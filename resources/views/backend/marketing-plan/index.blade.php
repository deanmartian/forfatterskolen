@extends('backend.layout')

@section('title')
    <title>Publishing &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file"></i> Marketing Plan</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12 margin-top">
        <button type="button" class="btn btn-success addMarketingPlanBtn" data-toggle="modal"
                data-target="#marketingPlanModal" data-action="{{ route($marketingPlanStoreRoute) }}">
            Add Marketing Plan
        </button>

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Questions</th>
                    <th width="200"></th>
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
                            <button class="btn btn-primary btn-xs updateMarketingPlanBtn"
                                    data-toggle="modal" data-target="#marketingPlanModal"
                                    data-record="{{ json_encode($marketingPlan) }}"
                                    data-action="{{ route($marketingPlanUpdateRoute, $marketingPlan->id) }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteMarketingPlanBtn" data-toggle="modal"
                                    data-target="#deleteMarketingPlanModal"
                                    data-action="{{ route($marketingPlanDeleteRoute, $marketingPlan->id) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="marketingPlanModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name">
                        </div>

                        <div class="checklist-container">
                            {{--<div class="form-group checklist-group" data-number="1">
                                <label> Main Question </label>
                                <input type="text" class="form-control" name="arr[1][main_question]">

                                <div class="sub-container"></div>
                                <button type="button" class="btn btn-success btn-xs addSub" style="margin-top: 5px" onclick="addSub(this)">
                                    Add Sub Question
                                </button>
                            </div>--}}
                        </div>

                        <button type="button" class="btn btn-primary btn-xs addChecklist">
                            Add Main Question
                        </button>

                        <button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteMarketingPlanModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Delete Marketing Plan
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}

                        <p>Are you sure you want to delete this record?</p>

                        <button type="submit" class="btn btn-danger pull-right margin-top">
                            {{ trans('site.delete') }}
                        </button>

                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>

        $(".addChecklist").click(function() {
            let checklistCount = $(".checklist-container").find('.checklist-group').length + 1;

            let formGroup = "<div class='form-group checklist-group' data-number='" + checklistCount + "'>";
                formGroup += "<label>Main Question</label>";
                formGroup += "<input type='text' class='form-control' name='arr[" + checklistCount + "][main_question]'" +
                    " style='width: 95%; display: inline'>";
                formGroup += "<button type='button' class='btn btn-danger btn-xs'" +
                    " style='margin-left: 5px' onclick='removeQuestion(this, true)'><i class='fa fa-minus'></i></button>";

                formGroup += "<div class='sub-container'></div>";
                formGroup += "<button type='button' class='btn btn-success btn-xs addSub' style='margin-top: 5px' onclick='addSub(this)'> Add Sub Question</button>";
                formGroup += "</div>";
            $(this).closest('form').find('.checklist-container').append(formGroup);
        });

        $(".addMarketingPlanBtn").click(function() {
            let action = $(this).data('action');
            let modal = $("#marketingPlanModal");
            let form = modal.find('form');
            modal.find('.modal-title').text('Add Marketing Plan');
            form.attr('action', action);
            form.find('[name=name]').val('');
            form.find("[name=_method]").remove();

            $(".checklist-container").empty();
            addMain();
        });

        $(".updateMarketingPlanBtn").click(function() {
            let action = $(this).data('action');
            let record = $(this).data('record');
            let modal = $("#marketingPlanModal");
            let form = modal.find('form');

            form.prepend('<input type="hidden" name="_method" value="PUT">');

            modal.find('.modal-title').text('Edit Marketing Plan');
            form.attr('action', action);
            form.find('[name=name]').val(record.name);

            $(".checklist-container").empty();
            $.each(record.questions, function (k, question) {
                let number = k + 1;

                let formGroup = "<div class='form-group checklist-group' data-number='" + number + "'>";
                    formGroup += "<label>Main Question</label>";
                    formGroup += "<input type='text' class='form-control' name='arr[" + number + "][main_question]'" +
                        " value='" + question.main_question + "' style='width: 95%; display: inline'>";
                    formGroup += "<input type='hidden' class='form-control' name='arr[" + number + "][main_question_id]'" +
                    " value='" + question.id + "'>";
                    formGroup += "<button type='button' class='btn btn-danger btn-xs'" +
                        " style='margin-left: 5px' onclick='removeQuestion(this, true)'><i class='fa fa-minus'></i></button>";

                        formGroup += "<div class='sub-container'>";
                            $.each(question.sub_question_decoded, function (ks, sub_question) {
                                formGroup += "<div class='form-group' style='margin-top: 5px'>";
                                formGroup += "<label>Question</label>";
                                formGroup += "<input type='text' class='form-control' name='arr[" + number + "][sub_question][]'" +
                                    " value='" + sub_question + "' style='width: 88.5%; display: inline'>";
                                formGroup += "<button type='button' class='btn btn-danger btn-xs'" +
                                    " style='margin-left: 5px' onclick='removeQuestion(this)'><i class='fa fa-minus'></i></button>";
                                formGroup += "</div>";
                            });
                        formGroup += "</div>";
                    formGroup += "<button type='button' class='btn btn-success btn-xs addSub' style='margin-top: 5px'" +
                        " onclick='addSub(this)'> Add Sub Question</button>";
                    formGroup += "</div>";
                $('.checklist-container').append(formGroup);

            });
        });

        $(".deleteMarketingPlanBtn").click(function () {
            let modal = $("#deleteMarketingPlanModal");
            let form = modal.find("form");
            let action = $(this).data('action');

            form.attr('action', action);
        });

        // add first main question container
        function addMain() {
            let formGroup = "<div class='form-group checklist-group' data-number='1'>";
            formGroup += "<label>Main Question</label>";
            formGroup += "<input type='text' class='form-control' name='arr[1][main_question]' style='width: 95%; display: inline'>";
            formGroup += "<button type='button' class='btn btn-danger btn-xs'" +
                " style='margin-left: 5px' onclick='removeQuestion(this, true)'><i class='fa fa-minus'></i></button>";

            formGroup += "<div class='sub-container'></div>";
            formGroup += "<button type='button' class='btn btn-success btn-xs addSub' style='margin-top: 5px'" +
                " onclick='addSub(this)'> Add Sub Question</button>";
            formGroup += "</div>";
            $('.checklist-container').append(formGroup);
        }

        function addSub(self) {
            let checklistGroupNumber = $(self).closest('.checklist-group').data('number');
            let formGroup = "<div class='form-group' style='margin-top: 5px'>";
            formGroup += "<label>Question</label>";
            formGroup += "<input type='text' class='form-control' name='arr["+checklistGroupNumber+"][sub_question][]'" +
                " style='width: 88.5%; display: inline'>";
            formGroup += "<button type='button' class='btn btn-danger btn-xs'" +
                " style='margin-left: 5px' onclick='removeQuestion(this)'><i class='fa fa-minus'></i></button>";
            formGroup += "</div>";

            $(self).closest('.checklist-group').find('.sub-container').append(formGroup);
        }

        function removeQuestion(self, is_main = false) {
            if (is_main) {
                $(self).closest('.checklist-group').remove();
            } else {
                $(self).closest('.form-group').remove();
            }
        }
    </script>
@stop