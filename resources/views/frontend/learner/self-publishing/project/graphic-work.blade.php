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

                    <h3 class="mt-3">
                        {{ trans('site.homepage.illustration-cover-design') }}
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.homepage.illustration-cover-design') }}</th>
                                    <th>{{ trans('site.description') }}</th>
                                    <th>{{ trans('site.author-portal.format') }}</th>
                                    <th>{{ trans('site.author-portal.isbn') }}</th>
                                    <th>{{ trans('site.author-portal.backside-text') }}</th>
                                    <th>{{ trans('site.author-portal.backside-image') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($covers as $cover)
                                    <tr>
                                        <td>{!! $cover->image !!}</td>
                                        <td>{{ $cover->description }}</td>
                                        <td>
                                            {{ !is_array(AdminHelpers::projectFormats($cover->format)) ?
                                                AdminHelpers::projectFormats($cover->format) 
                                                : $cover->format . ' mm' }}
                                        </td>
                                        <td>
                                            {{ $cover->isbn?->value }}
                                        </td>
                                        <td>
                                            @if ($cover->backside_type == 'text')
                                                {{ $cover->backside_text }}
                                            @else
                                                <a href="{{ url('/dropbox/download/' . trim($cover->backside_text)) }}">
                                                    <i class="fa fa-download" aria-hidden="true"></i>
                                                </a>&nbsp;
                                                <a href="{{ url('/dropbox/shared-link/' . trim($cover->backside_text)) }}" target="_blank">
                                                    {{ basename($cover->backside_text) }}
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($cover->backside_image)
                                                @php
                                                    $backsideImages = explode(',', $cover->backside_image);
                                                @endphp
                                                @foreach ($backsideImages as $backsideImage)
                                                    <a href="{{ url('/dropbox/download/' . trim($backsideImage)) }}">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>&nbsp;
                                                    <span>{{ basename($backsideImage) }}</span>
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card global-card for cover-->

                    <h3 class="mt-5">
                        {{ trans('site.author-portal.page-format') }}
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ trans('site.author-portal.interior') }}</th>
                                        <th>{{ trans('site.author-portal.designer') }}</th>
                                        <th>{{ trans_choice('site.feedbacks', 1) }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookFormattingList as $bookFormatting)
                                        <tr>
                                            <td>
                                                {!! $bookFormatting->file_link !!}
                                            </td>
                                            <td>
                                                {{ $bookFormatting->designer?->full_name }}
                                            </td>
                                            <td>
                                                @if ($bookFormatting->feedback_status === 'completed')
                                                    {!! $bookFormatting->feedback_file_link !!}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <h3 class="mt-5">
                        {{ trans('site.author-portal.indesign') }}
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ trans('site.author-portal.interior') }}</th>
                                        <th>{{ trans('site.author-portal.interior') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($indesigns as $indesign)
                                        <tr>
                                            <td>
                                                @php
                                                    $coverFiles = explode(',', $indesign->value);
                                                @endphp
                                                @foreach ($coverFiles as $coverFile)
                                                    @if (strpos($coverFile, 'project-'))
                                                        <a href="{{ url('/dropbox/download/' . trim($coverFile)) }}">
                                                            <i class="fa fa-download" aria-hidden="true"></i>
                                                        </a>&nbsp;
                                                        <a href="{{ url('/dropbox/shared-link/' . trim($coverFile)) }}" target="_blank" 
                                                        style="margin-right: 5px">
                                                            {{ basename($coverFile) }}
                                                        </a>
                                                    @else
                                                        @if ($coverFile)
                                                            <a href="{{ $coverFile }}" class="btn btn-success btn-xs" download>
                                                                <i class="fa fa-download"></i>
                                                            </a>
                                                            <a href="{{ asset($coverFile) }}" target="_blank" style="margin-right: 5px">
                                                                {{ basename($coverFile) }}
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @if ($indesign->interior)
                                                    <a href="{{ url('/dropbox/download/' . trim($indesign->description)) }}">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>&nbsp;
                                                    {!! $indesign->interior !!}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <h3 class="mt-5">
                        {{ trans('site.author-portal.barcode') }}
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.barcode') }}</th>
                                    <th>{{ trans('site.date') }}</th>
                                    <th width="300"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($barCodes as $barCode)
                                    <tr>
                                        <td>{!! $barCode->image !!}</td>
                                        <td>
                                            {{ $barCode->date }}
                                        </td>
                                        <td>
                                            <a href="{{ url('/dropbox/download/' . trim($barCode->value)) }}" 
                                                class="btn btn-success btn-xs" download>
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
                        {{ trans('site.author-portal.print-ready') }}
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ trans_choice('site.files', 1) }}</th>
                                        <th>{{ trans('site.date-uploaded') }}</th>
                                        <th>{{ trans('site.author-portal.format') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($printReadyList as $printReady)
                                        <tr>
                                            <td>
                                                @if ($printReady->value)
                                                    @if (strpos($printReady->value, 'project-'))
                                                        <a href="{{ url('/dropbox/download/' . trim($printReady->value)) }}">
                                                            <i class="fa fa-download" aria-hidden="true"></i>
                                                        </a>&nbsp;
                                                    @else
                                                        <a href="{{ $printReady->value }}" class="btn btn-success btn-xs" download>
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                    @endif
                                                @endif
                                                
                                                {!! $printReady->image !!}
                                            </td>
                                            <td>
                                                {{ $printReady->upload_date }}
                                            </td>
                                            <td>
                                                {{ !is_array(AdminHelpers::projectFormats($printReady->format)) ?
                                                    AdminHelpers::projectFormats($printReady->format) 
                                                    : $printReady->format . ' mm' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <h3 class="mt-5">
                        {{ trans('site.author-portal.sample-book-pdf') }}
                    </h3>
                    <div class="card global-card">
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans_choice('site.files', 1) }}</th>
                                    <th width="300"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sampleBookPDFs as $sampleBookPDF)
                                    <tr>
                                        <td>{!! $sampleBookPDF->file_link !!}</td>
                                        <td>
                                            @if (strpos($sampleBookPDF->value, "project-"))
                                                <a href="{{ url('/dropbox/download/' . trim($sampleBookPDF->value)) }}" 
                                                    class="btn btn-success btn-xs">
                                                    <i class="fa fa-download" aria-hidden="true"></i>
                                                </a>
                                            @else
                                                <a href="{{ $sampleBookPDF->value }}" class="btn btn-success btn-xs" download>
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            @endif
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