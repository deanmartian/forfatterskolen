@extends('giutbok.layout')

@section('title')
    <title>Publishing &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-users"></i> {{ $selfPublishing->title }} learners</h3>

        <a href="{{ route('g-admin.self-publishing.index') }}" class="btn btn-default" style="margin-left: 10px">
            Back
        </a>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12 margin-top">

        <button type="button" class="btn btn-success addLearnersBtn" data-toggle="modal"
                data-target="#addLearnersModal" data-action="">
            Add Learners
        </button>

        <div class="clearfix"></div>

        <div class="col-md-8 col-sm-offset-2">
            <div class="table-users table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Learner</th>
                        <th width="150"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($learners as $learner)
                        <tr>
                            <td>
                                {{ $learner->user->full_name }}
                            </td>
                            <td>
                                <button class="btn btn-danger btn-xs deleteLearnerBtn" data-toggle="modal"
                                        data-target="#deleteLearnerModal"
                                        data-action="{{ route('g-admin.self-publishing.delete-learner', $learner->id) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="addLearnersModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Learners to Self Publishing</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('g-admin.self-publishing.add-learners', $selfPublishing->id) }}"
                          onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>
                                Learners
                            </label>
                            <select name="learners[]" class="form-control select2 template" multiple="multiple">
                                @foreach($availableLearners as $learner)
                                    <option value="{{$learner->id}}">
                                        {{$learner->full_name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div id="deleteLearnerModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.delete') }} <em></em></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        Are you sure you want to remove this learner?
                        <div class="text-right margin-top">
                            <button class="btn btn-danger" type="submit">{{ trans('site.delete') }}</button>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(".deleteLearnerBtn").click(function() {
            var action = $(this).data('action');
            let modal = $("#deleteLearnerModal");

            let form = modal.find('form');
            form.attr('action', action);
        });
    </script>
@stop