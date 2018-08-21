@extends('frontend.layout')

@section('title')
    <title>Writing Groups &rsaquo; Forfatterskolen</title>
@stop

@section('heading') Skrivegrupper @stop

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
                </div>

                <div class="col-md-12">
                    <div class="table-users table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Group Name</th>
                                <th>Contact Person</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($writingGroups as $writingGroup)
                                <tr>
                                    <td>
                                        <a href="{{ route('learner.writing-group', $writingGroup->id) }}">
                                            {{ $writingGroup->name }}
                                        </a>
                                    </td>
                                    <td>{{ \App\User::find($writingGroup->contact_id)->full_name }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pull-right">
                        {{$writingGroups->render()}}
                    </div>

                </div>

            </div>
        </div>
        <div class="clearfix"></div>
    </div>

@stop

