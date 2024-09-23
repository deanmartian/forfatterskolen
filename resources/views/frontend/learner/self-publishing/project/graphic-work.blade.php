@extends('frontend.learner.self-publishing.layout')

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

                    <h3 class="mt-3">
                        Cover
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Cover</th>
                                    <th>Description</th>
                                    <th>Format</th>
                                    <th>ISBN</th>
                                    <th>Backside Text</th>
                                    <th>Backside Image</th>
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

                    <h3 class="mt-5">
                        Page Format
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Interior</th>
                                        <th>Designer</th>
                                        <th>{{ trans_choice('site.feedbacks', 1) }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <h3 class="mt-5">
                        Indesign
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Cover</th>
                                        <th>Interior</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <h3 class="mt-5">
                        Barcode
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Barcode</th>
                                    <th>Date</th>
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

                    <h3 class="mt-5">
                        Print Ready
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>File</th>
                                        <th>Format</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <h3 class="mt-5">
                        Sample book/PDF
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>File</th>
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