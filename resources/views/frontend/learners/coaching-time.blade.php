@extends('frontend.layouts.course-portal')

@section('title')
    <title>Coaching Time &rsaquo; Forfatterskolen</title>
@endsection

@section('content')
<div class="learner-container coaching-time-wrapper">
    <div class="container">
        <h1 class="page-title">Coaching Time</h1>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @isset($coachingTimer)
            <h3>Tilgjengelige Bokredaktører</h3>
            @forelse($editors as $editorSlots)
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $editorSlots->first()->editor->name }}</div>
                    <div class="panel-body">
                        <ul class="list-unstyled">
                            @foreach($editorSlots as $slot)
                                <li>
                                    {{ \Carbon\Carbon::parse($slot->date)->format('d.m.Y') }} {{ $slot->start_time }} ({{ $slot->duration }} min)
                                    <form method="POST" action="{{ route('learner.coaching-time.request') }}" class="pull-right">
                                        @csrf
                                        <input type="hidden" name="coaching_timer_id" value="{{ $coachingTimer->id }}">
                                        <input type="hidden" name="editor_time_slot_id" value="{{ $slot->id }}">
                                        <button type="submit" class="btn btn-xs btn-primary">Book</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @empty
                <p>Ingen tilgjengelige tidsluker.</p>
            @endforelse
        @else
            <p>Ingen coaching time tilgjengelig.</p>
        @endisset
    </div>
</div>
@endsection
