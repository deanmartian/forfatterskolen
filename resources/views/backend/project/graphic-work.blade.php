@extends('backend.layout')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Project &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Graphic Work</h3>
    </div>
    <div class="col-sm-12 margin-top">
        <button type="button" class="btn btn-success">+ Add Cover</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Cover</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <!-- TODO: only one record per project -->
        <button type="button" class="btn btn-success">+ Add Barecode</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Barecode</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add Rewrite script</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Rewrite script</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add Trial pages</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Trial pages</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-success">+ Add Sample book/PDF</button>
        <div class="table-responsive margin-top">
            <table class="table table-side-bordered table-white">
                <thead>
                <tr>
                    <th>Sample book/PDF</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop