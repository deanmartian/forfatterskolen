@extends('giutbok.layout')

@section('title')
    <title>Learners &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <style>
        .form-inline {
            display: inline;
            margin-left: 10px;
        }

        .center-area {
            position: absolute;
            left: 18%;/*30%*/
        }
    </style>
@stop

@section('content')
    <div class="page-toolbar" style="position: relative;">
        <h3><i class="fa fa-users"></i> {{ trans('site.all-learners') }}</h3>
    </div>
    <div class="col-md-12">

        <div class="table-users table-responsive">
            <button type="button" class="btn btn-success addLearnerBtn margin-top" data-toggle="modal"
                    data-target="#addLearnerModal">
                Add Learner
            </button>

            <table class="table">
                <thead>
                <tr>
                    <th>{{ trans('site.id') }}</th>
                    <th>{{ trans('site.first-name') }}</th>
                    <th>{{ trans('site.last-name') }}</th>
                    <th>{{ trans_choice('site.emails', 1) }}</th>
                    <th>Free Courses</th>
                    <th>{{ trans_choice('site.workshops',1) }}</th>
                    <th>{{ trans_choice('site.shop-manuscripts', 1) }}</th>
                    <th>{{ trans_choice('site.courses', 2) }}</th>
                    <th>{{ trans('site.date-joined') }}</th>
                    <th>{{ trans('site.admin') }}</th>
                    <th>{{ trans('site.auto-renew') }}</th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                @foreach($learners as $learner)
                    <tr>
                        <td><a href="{{route('g-admin.learner.show', $learner->id)}}">{{$learner->id}}</a></td>
                        <td>{{$learner->first_name}}</td>
                        <td>{{$learner->last_name}}</td>
                        <td>{{$learner->email}}</td>
                        <td>{{ $learner->freeCourses->count() }}</td>
                        <td>{{($learner->workshopsTaken->count())}}</td>
                        <td>{{($learner->shopManuscriptsTaken->count())}}</td>
                        <td>{{count($learner->coursesTaken)}}</td>
                        <td>{{$learner->created_at}}</td>
                        <td>{{ $learner->is_admin ? 'Yes' : 'No' }}</td>
                        <td>{{ $learner->auto_renew_courses ? 'Yes' : 'No' }}</td>
                        <td><a href="{{route('g-admin.learner.show', $learner->id)}}" class="btn btn-xs btn-primary pull-right">{{ trans('site.view-learner') }}</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="pull-right">
            {{$learners->appends(Request::all())->render()}}
        </div>
        <div class="clearfix"></div>
    </div>

    <div id="addLearnerModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Add Learner
                    </h4>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('g-admin.learner.register') }}" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>{{ trans('site.front.form.email') }}</label>
                            <input type="email" name="email"
                                   class="form-control no-border-left" required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.front.form.first-name') }}</label>
                            <input type="text" name="first_name"
                                   class="form-control no-border-left" required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.front.form.last-name') }}</label>
                            <input type="text" name="last_name"
                                   class="form-control no-border-left" required>
                        </div>

                        <div class="form-group">
                            <label>{{ trans('site.front.form.password') }}</label>
                            <input type="text" name="password"
                                   class="form-control no-border-left" required>
                            <button class="btn btn-success btn-sm generatePassword margin-top" type="button">
                                Generate
                            </button>
                        </div>

                        <button type="submit" class="btn btn-primary pull-right">{{ trans('site.save') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(".generatePassword").click(function() {
            $.ajax({
                type:'GET',
                url:'/learner/generate-password',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: {},
                success: function(data){
                    $("input[name=password]").val(data);
                }
            });
        });
    </script>
@stop