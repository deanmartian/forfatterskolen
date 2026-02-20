@extends('editor.layout')

@section('title')
    <title>Editors Note &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<style>
    table td {
        border: 1px solid #eeeeee;
        padding: 5px 15px;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2; /* Alternate background color for even rows */
    }
</style>
@stop

@section('content')
    <div class="col-sm-12 dashboard-left">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel" style="padding: 30px 50px;">
                    <div class="panel-body">
                        {!! $note !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
