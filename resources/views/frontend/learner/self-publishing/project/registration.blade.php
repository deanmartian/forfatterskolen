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
                    <i class="fa fa-arrow-left"></i> {{ trans('site.back') }}
                </a>

                <div class="col-md-12 dashboard-course no-left-padding">
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.isbn') }}</th>
                                    <th width="700">{{ trans('site.type') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($isbns as $isbn)
                                    <tr>
                                        <td>{!! $isbn->value !!}</td>
                                        <td>{{ $isbn->isbn_type }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for isbn-->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.central-distribution') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($centralDistributions as $centralDistribution)
                                    <tr>
                                        <td>{!! $centralDistribution->value !!}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for central distribution -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.mentor-book-base') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($mentorBookBases as $mentorBookBase)
                                    <tr>
                                        <td>{!! $mentorBookBase->value ? 'Yes' : 'No' !!}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for mentor book base -->

                    <div class="card global-card mt-5">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.upload-files-mentor-book-base') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($uploadFilesToMentorBookBases as $uploadFilesToMentorBookBase)
                                    <tr>
                                        <td>{!! $uploadFilesToMentorBookBase->value ? 'Yes' : 'No' !!}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for upload files to mentor book base -->
                </div>

            </div>
        </div>
    </div>
@stop