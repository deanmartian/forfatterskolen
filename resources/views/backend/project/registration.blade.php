@extends('backend.layout')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Registration</h3>
    </div>
    <div class="col-sm-12 margin-top">
        <button type="button" class="btn btn-success">+ Add ISBN</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>ISBN</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add Central distribution</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Central distribution</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add Mentor book base</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Mentor book base</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add Upload files to mentor book base</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Upload files to mentor book base</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop