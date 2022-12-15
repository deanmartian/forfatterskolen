@extends('frontend.layout')

@section('title')
    <title>Time Register &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <style>
        .fa-file-red:before {
            content: "\f15b";
        }

        .fa-file-red {
            color: #862736 !important;
            font-size: 20px;
        }
    </style>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">
                <a href="{{ route('learner.project.show', $project->id) }}"
                   class="btn btn-secondary mb-3">
                    <i class="fa fa-arrow-left"></i> Back
                </a>

                <div class="col-md-12 dashboard-course no-left-padding">
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Cover</th>
                                    <th width="500">Description</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($covers as $cover)
                                    <tr>
                                        <td>{!! $cover->image !!}</td>
                                        <td>{{ $cover->description }}</td>
                                        <td>
                                            <a href="{{ $cover->value }}" class="btn btn-success btn-xs" download>
                                                <i class="fa fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for cover-->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Barcode</th>
                                    <th width="300"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($barCodes as $barCode)
                                    <tr>
                                        <td>{!! $barCode->image !!}</td>
                                        <td>
                                            <a href="{{ $barCode->value }}" class="btn btn-success btn-xs" download>
                                                <i class="fa fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for barcode-->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Rewrite script</th>
                                    <th width="300"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rewriteScripts as $rewriteScript)
                                    <tr>
                                        <td>{!! $rewriteScript->file_link !!}</td>
                                        <td>
                                            <a href="{{ $rewriteScript->value }}" class="btn btn-success btn-xs" download>
                                                <i class="fa fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for rewrite scripts -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Trial pages</th>
                                    <th width="300"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($trialPages as $trialPage)
                                    <tr>
                                        <td>{!! $trialPage->image !!}</td>
                                        <td>
                                            <a href="{{ $trialPage->value }}" class="btn btn-success btn-xs" download>
                                                <i class="fa fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for trial page -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Sample book/PDF</th>
                                    <th width="300"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sampleBookPDFs as $sampleBookPDF)
                                    <tr>
                                        <td>{!! $sampleBookPDF->file_link !!}</td>
                                        <td>
                                            <a href="{{ $sampleBookPDF->value }}" class="btn btn-success btn-xs" download>
                                                <i class="fa fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for sample book/pdf -->

                </div> <!-- end col-md-12 -->
            </div>
        </div>
    </div>
@stop