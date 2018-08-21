@extends('backend.layout')

@section('title')
    <title>Writing Group &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')

    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Writing Groups</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a class="btn btn-success margin-top" href="{{route('admin.writing-group.create')}}">Add Group</a>
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Group Name</th>
                    <th>Contact Person</th>
                </tr>
                </thead>
                <tbody>

                @foreach($writingGroups as $writingGroup)
                    <tr>
                        <td>
                            <a href="{{ route('admin.writing-group.edit', $writingGroup->id) }}">
                                {{ $writingGroup->id }}
                            </a>
                        </td>
                        <td>
                            {{ $writingGroup->name }}
                        </td>
                        <td>
                            {{ \App\Http\AdminHelpers::getLearnerList($writingGroup->contact_id) ?
                             \App\Http\AdminHelpers::getLearnerList($writingGroup->contact_id)->full_name
                             : '' }}
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>

        <div class="pull-right">
            {{$writingGroups->render()}}
        </div>

    </div>
@stop