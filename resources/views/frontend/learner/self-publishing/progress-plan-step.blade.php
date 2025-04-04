@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Progress Plan Step &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <a href="{{ route('learner.progress-plan') }}" class="btn btn-secondary mb-3">
                <i class="fa fa-arrow-left"></i> Back
            </a>

            <div class="card card-global">
                <div class="card-header">
                    {{ $stepTitle }}

                    <button type="button" class="btn red-global-btn uploadManuscriptBtn py-2 px-6 pull-right"
                        data-toggle="modal" data-target="#uploadManuscriptModal"
                        style="width: auto;">
                        {{ trans('site.learner.upload-script') }}
                        <i class="fa fa-upload"></i>
                    </button>
                </div>
                <div class="card-body">
                    <h3>Step 1. Manuscript</h3>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <td>File</td>
                                <td>Date</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>File 1</td>
                                <td>03.04.2025</td>
                            </tr>
                            <tr>
                                <td>File 2</td>
                                <td>04.04.2025</td>
                            </tr>
                            <tr>
                                <td>File 3</td>
                                <td>05.04.2025</td>
                            </tr>
                            <tr>
                                <td>File 4</td>
                                <td>06.04.2025</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                
            </div>
        </div>
    </div>
@stop