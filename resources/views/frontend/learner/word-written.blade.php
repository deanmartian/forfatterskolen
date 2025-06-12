@extends('frontend.layout')

@section('title')
    <title>Words Written &rsaquo; Forfatterskolen</title>
@stop

@section('heading')
    Words Written
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

                    <button class="btn btn-primary pull-right light-blue" data-toggle="modal" data-target="#addDateModal">
                        Add Words Written Date
                    </button>
                    <a class="btn btn-success pull-right light-blue" href="{{ route('learner.word-written-goals') }}"
                    style="margin-right: 5px">
                        Add/View Goals
                    </a>
                </div>

                <div class="col-sm-6 col-sm-offset-3">
                    <div class="table-users table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Words Written</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($words as $word)
                                <tr>
                                    <td>{{ $word->words }}</td>
                                    <td>{{ $word->date }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                        <div class="pull-right">
                            {{ $words->render() }}
                        </div>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>

    <div id="addDateModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Words Written Date</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('learner.word-written.submit') }}">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>Words Written</label>
                            <input type="number" class="form-control" step="1" name="words">
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" name="date">
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
@stop