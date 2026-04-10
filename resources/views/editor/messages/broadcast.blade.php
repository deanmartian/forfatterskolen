@extends('editor.layout')

@section('page_title', 'Broadcast &rsaquo; Meldinger')

@section('page-title', 'Meldinger')

@section('content')
<div class="ed-section">
    <div class="ed-section__header">
        <h2 class="ed-section__title">
            <a href="{{ route('editor.messages.index') }}" style="color: var(--ink-soft); text-decoration: none; margin-right: 8px;">
                <i class="fa fa-arrow-left"></i>
            </a>
            Broadcast til alle redaktorer
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
            <div class="alert alert-info" style="margin-bottom: 20px;">
                <i class="fa fa-info-circle"></i>
                Denne meldingen sendes til alle redaktorer. De vil motta en e-postvarsling.
            </div>

            <form method="POST" action="{{ route('editor.messages.broadcast.store') }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="subject">Emne</label>
                    <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject') }}" required placeholder="Skriv et emne...">
                </div>

                <div class="form-group">
                    <label for="body">Melding</label>
                    <textarea name="body" id="body" class="form-control" rows="6" required placeholder="Skriv din melding...">{{ old('body') }}</textarea>
                </div>

                <button type="submit" class="btn btn-warning">
                    <i class="fa fa-bullhorn"></i> Send broadcast
                </button>
                <a href="{{ route('editor.messages.index') }}" class="btn btn-default" style="margin-left: 8px;">Avbryt</a>
            </form>
        </div>
    </div>
</div>
@stop
