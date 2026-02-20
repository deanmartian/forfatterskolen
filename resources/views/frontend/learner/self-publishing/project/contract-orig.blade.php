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
                                    <th>{{ trans('site.name') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($contracts as $contract)
                                    <tr>
                                        <td>
                                            @if ($contract->signature)
                                                {{ $contract->title }}
                                            @else
                                                @if($contract->admin_signature)
                                                    <a href="{{ route('front.contract-view', $contract->code) }}">
                                                        {{ $contract->title }}
                                                    </a>
                                                @else
                                                    {{ $contract->title }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {!! $contract->signature_text !!}

                                            @if ($contract->signature)
                                                <a href="{{ $contract->learner_download_link }}"
                                                   class="button btn btn-info btn-xs" download>Download PDF</a>
                                            @endif
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
@stop