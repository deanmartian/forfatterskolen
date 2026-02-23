@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Project Storage &rsaquo; Forfatterskolen</title>
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
                                        <th>
                                            {{ trans('site.author-portal.book-name') }}
                                        </th>
                                        <th width="100"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projectCentralDistributions as $projectCentralDistribution)
                                        <tr>
                                            <td>
                                                <a href="{{ route('learner.project.storage-details', 
                                                    [$project->id, $projectCentralDistribution->id]) }}">
                                                    {{ $projectCentralDistribution->value }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ $projectBook->book_name ?? '' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection