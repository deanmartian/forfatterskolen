@extends('frontend.layouts.course-portal')

@section('title')
<title>Ny samtale &rsaquo; Meldinger</title>
@stop

@section('heading') Meldinger @stop

@section('styles')
<style>
    .msg-create-card { border-radius: 8px; overflow: hidden; }
    .msg-create-card .card-header {
        background: #862736; color: #fff; padding: 16px 24px;
        display: flex; align-items: center; gap: 10px;
    }
    .msg-create-card .card-header h5 { margin: 0; font-weight: 600; font-size: 16px; }
    .msg-create-card .card-body { padding: 24px; }
    .msg-create-card .form-group { margin-bottom: 20px; }
    .msg-create-card .form-group label {
        font-weight: 600; font-size: 13px; color: #555;
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
    }
    .msg-create-card .form-control:focus {
        border-color: #862736; box-shadow: 0 0 0 2px rgba(134,39,54,0.15);
    }
    .msg-create-card textarea.form-control { resize: vertical; min-height: 150px; }
    .msg-recipient-info {
        background: #f8f5f0; border-radius: 8px; padding: 14px 18px;
        display: flex; align-items: center; gap: 12px; margin-bottom: 20px;
    }
    .msg-recipient-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: #862736; color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 600; flex-shrink: 0;
    }
    .msg-recipient-info .name { font-weight: 600; color: #333; }
    .msg-recipient-info .role { font-size: 12px; color: #888; }
    .msg-actions { display: flex; align-items: center; gap: 10px; padding-top: 4px; }
    .msg-actions .btn-send {
        background: #862736; color: #fff; border: none;
        padding: 10px 24px; border-radius: 6px; font-weight: 600;
        display: inline-flex; align-items: center; gap: 8px;
        transition: background 0.2s;
    }
    .msg-actions .btn-send:hover { background: #6e1f2d; color: #fff; }
    .msg-actions .btn-cancel {
        background: transparent; color: #888; border: 1px solid #ddd;
        padding: 10px 20px; border-radius: 6px; text-decoration: none;
        transition: all 0.2s;
    }
    .msg-actions .btn-cancel:hover { background: #f5f5f5; color: #555; }
</style>
@stop

@section('content')
<div class="learner-container">
    <div class="container">
        <div class="row">
            @include('frontend.partials.learner-search-new')
        </div>

        <div class="row mt-4">
            <div class="col-sm-12 col-md-10 col-lg-8">
                <div class="mb-3">
                    <a href="{{ route('learner.messages.index') }}" style="color: #862736; text-decoration: none; font-size: 14px;">
                        <i class="fa fa-arrow-left"></i> Tilbake til innboks
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin: 0;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card global-card msg-create-card">
                    <div class="card-header">
                        <i class="fa fa-pencil"></i>
                        <h5>Ny samtale</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('learner.messages.store') }}">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="recipient_id">Til</label>
                                @php $selectedRecipient = old('recipient_id', request('recipient_id')); @endphp
                                <select name="recipient_id" id="recipient_id" class="form-control" required>
                                    <option value="">-- Velg mottaker --</option>
                                    @if($editors->isNotEmpty())
                                        <optgroup label="Din redaktør">
                                            @foreach($editors as $editor)
                                                <option value="{{ $editor->id }}" {{ $selectedRecipient == $editor->id ? 'selected' : '' }}>
                                                    {{ $editor->full_name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($admins->isNotEmpty())
                                        <optgroup label="Administrasjon">
                                            @foreach($admins as $admin)
                                                <option value="{{ $admin->id }}" {{ $selectedRecipient == $admin->id ? 'selected' : '' }}>
                                                    {{ $admin->full_name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="subject">Emne</label>
                                <input type="text" name="subject" id="subject" class="form-control"
                                       value="{{ old('subject', request('subject')) }}" required
                                       placeholder="Hva handler meldingen om?">
                            </div>

                            <div class="form-group">
                                <label for="body">Melding</label>
                                <textarea name="body" id="body" class="form-control" rows="8" required
                                          placeholder="Skriv din melding her...">{{ old('body', request('body')) }}</textarea>
                            </div>

                            <div class="msg-actions">
                                <button type="submit" class="btn-send">
                                    <i class="fa fa-paper-plane"></i> Send melding
                                </button>
                                <a href="{{ route('learner.messages.index') }}" class="btn-cancel">Avbryt</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
