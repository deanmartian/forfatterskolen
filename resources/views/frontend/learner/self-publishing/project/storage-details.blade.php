@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Project Storage &rsaquo; Forfatterskolen</title>
@stop

@section('content')
<div class="learner-container">
    <div class="container">
        <div class="row">
            <a href="{{ route('learner.project.storage', $project->id) }}"
               class="btn btn-secondary mb-3">
                <i class="fa fa-arrow-left"></i> Back
            </a>

            <div class="col-md-12 learner-assignment no-left-padding">
                <div class="card global-card">
                    <div class="card-body p-0">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ISBN</th>
                                    <th>
                                        Book name
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        {{ $projectUserBook->value }}
                                    </td>
                                    <td>
                                        {{ $projectBook->book_name ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($projectUserBook)
                    <ul class="nav nav-tabs my-5">
                        <li @if( Request::input('tab') == 'master' || Request::input('tab') == '') class="active" @endif>
                            <a href="?tab=master">Master Data</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade in active">
                            @if( Request::input('tab') == 'various')
                                {{-- @include('backend.project.partials._various') --}}
                            @elseif( Request::input('tab') == 'inventory')
                                {{-- @include('backend.project.partials._inventory') --}}
                            @elseif( Request::input('tab') == 'distribution')
                                {{-- @include('backend.project.partials._distributions') --}}
                            @elseif( Request::input('tab') == 'sales')
                                {{-- @include('backend.project.partials._sales') --}}
                            @elseif( Request::input('tab') == 'sales-report')
                                {{-- @include('backend.project.partials._sales_report') --}}
                            @else
                                @include('frontend.learner.self-publishing.project.partials._master')
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection