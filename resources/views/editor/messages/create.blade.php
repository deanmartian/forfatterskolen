@extends('editor.layout')

@section('page_title', 'Ny samtale &rsaquo; Meldinger')

@section('page-title', 'Meldinger')

@section('content')
<div class="ed-section">
    <div class="ed-section__header">
        <h2 class="ed-section__title">
            <a href="{{ route('editor.messages.index') }}" style="color: var(--ink-soft); text-decoration: none; margin-right: 8px;">
                <i class="fa fa-arrow-left"></i>
            </a>
            Ny samtale
        </h2>
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

    <div class="panel panel-default">
        <div class="panel-body">
            <form method="POST" action="{{ route('editor.messages.store') }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="recipient_id">Mottaker</label>
                    <select name="recipient_id" id="recipient_id" class="form-control" required>
                        <option value="">-- Velg mottaker --</option>
                        @if($learners->count())
                            <optgroup label="Mine elever">
                                @foreach($learners as $learner)
                                    <option value="{{ $learner->id }}" {{ old('recipient_id') == $learner->id ? 'selected' : '' }}>
                                        {{ $learner->full_name }} (Elev #{{ $learner->id }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                        @if($editors->count())
                            <optgroup label="Redaktorer">
                                @foreach($editors as $editor)
                                    <option value="{{ $editor->id }}" {{ old('recipient_id') == $editor->id ? 'selected' : '' }}>
                                        {{ $editor->full_name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                        @if($admins->count())
                            <optgroup label="Administrasjon">
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ old('recipient_id') == $admin->id ? 'selected' : '' }}>
                                        {{ $admin->full_name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject">Emne</label>
                    <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject') }}" required placeholder="Skriv et emne...">
                </div>

                <div class="form-group">
                    <label for="body">Melding</label>
                    <textarea name="body" id="body" class="form-control" rows="6" required placeholder="Skriv din melding...">{{ old('body') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-paper-plane"></i> Send melding
                </button>
                <a href="{{ route('editor.messages.index') }}" class="btn btn-default" style="margin-left: 8px;">Avbryt</a>
            </form>
        </div>
    </div>
</div>
@stop
