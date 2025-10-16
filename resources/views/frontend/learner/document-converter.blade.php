@extends('frontend.layouts.course-portal')

@php
    $pageTitle = __('Convert document to DOCX');
    $description = __('The file will be converted to DOCX and downloaded automatically after upload.');
    $uploadLabel = __('Choose document');
    $note = __('Supported file formats: PAGES, PDF, DOC and DOCX.');
    $submitLabel = __('Convert to DOCX');
@endphp

@section('title')
    <title>{{ $pageTitle }} &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container learner-document-converter">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="global-card">
                        <div class="card-header">
                            <h2>{{ $pageTitle }}</h2>
                        </div>
                        <div class="card-body">
                            <p class="mb-4 text-muted">{{ $description }}</p>
                            <form method="POST" action="{{ route('learner.document-converter.convert') }}" enctype="multipart/form-data" onsubmit="disableSubmit(this);">
                                @csrf
                                <div class="form-group">
                                    <label for="document" class="font-barlow-semibold">
                                        {{ $uploadLabel }}
                                    </label>
                                    <input type="file" name="document" id="document" class="form-control" accept=".pages,.pdf,.doc,.docx" required>
                                    <small class="form-text text-muted">{{ $note }}</small>
                                    @error('document')
                                        <span class="text-danger d-block mt-2">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn site-btn-global mt-3" style="width: 100%">{{ $submitLabel }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
