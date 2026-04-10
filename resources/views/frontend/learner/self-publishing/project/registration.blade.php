@extends('frontend.learner.self-publishing.layout')

@section('page_title', 'Registrering &rsaquo; Selvpublisering &rsaquo; Forfatterskolen')
@section('robots', '<meta name="robots" content="noindex, follow">')

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">
                <a href="{{ route('learner.project.show', $project->id) }}"
                   class="btn btn-outline-brand mb-3">
                    <i class="fa fa-arrow-left"></i> {{ trans('site.back') }}
                </a>

                <div class="col-md-12 dashboard-course no-left-padding">
                    <div class="card sp-card">
                        <div class="sp-card-body p-0">
                            <table class="sp-table">
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
                    </div> <!-- end card sp-card for isbn-->

                    <div class="card sp-card mt-5">
                        <div class="sp-card-body p-0">
                            <table class="sp-table">
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
                    </div> <!-- end card sp-card for central distribution -->

                    <div class="card sp-card mt-5">
                        <div class="sp-card-body p-0">
                            <table class="sp-table">
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
                    </div> <!-- end card sp-card for mentor book base -->

                    <div class="card sp-card mt-5">
                        <div class="sp-card-body p-0">
                            <table class="sp-table">
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
                    </div> <!-- end card sp-card for upload files to mentor book base -->
                </div>

            </div>
        </div>
    </div>
@stop