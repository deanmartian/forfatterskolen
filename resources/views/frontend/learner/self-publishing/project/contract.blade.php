@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Time Register &rsaquo; Forfatterskolen</title>
@stop

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
                                                   class="button btn btn-outline-brand btn-sm" download>Download PDF</a>
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