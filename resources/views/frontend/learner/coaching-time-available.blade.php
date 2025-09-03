@extends('frontend.layouts.course-portal')

@section('title')
    <title>Available Time Slots &rsaquo; Forfatterskolen</title>
@endsection

@section('styles')
<style>
    .slot-card {
        border: 1px solid #e4e4e7;
        border-radius: 5px;
        padding: 15px;
        text-align: center;
        margin: 5px;
        width: 120px;
        display: inline-block;
    }
</style>
@stop

@section('content')
<div class="learner-container coaching-time-wrapper">
    <div class="container">
        <h1 class="page-title">Available Time Slots</h1>

        @isset($coachingTimer)
            @foreach($editors as $editorSlots)
                <h3 class="mt-4">Available Time Slots - {{ $editorSlots->first()->editor->full_name }}</h3>

                @foreach($editorSlots->groupBy('date') as $date => $slots)
                    <div class="mb-4">
                        <h4>{{ \Carbon\Carbon::parse($date, 'UTC')->setTimezone(config('app.timezone'))->isoFormat('dddd - MMMM D') }}</h4>
                        <div class="d-flex flex-wrap">
                            @foreach($slots as $slot)
                                <div class="slot-card">
                                    <div><i class="fa fa-clock-o"></i></div>
                                    <div class="mt-2">{{ \Carbon\Carbon::parse($slot->date.' '.$slot->start_time, 'UTC')->setTimezone(config('app.timezone'))->format('H:i') }}</div>
                                    <div>{{ $slot->duration }} min</div>
                                    <form method="POST" action="{{ route('learner.coaching-time.request') }}" class="mt-2">
                                        @csrf
                                        <input type="hidden" name="coaching_timer_id" value="{{ $coachingTimer->id }}">
                                        <input type="hidden" name="editor_time_slot_id" value="{{ $slot->id }}">
                                        <button type="submit" class="btn btn-primary btn-sm">Book</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endforeach
        @else
            <p>Ingen coaching time tilgjengelig.</p>
        @endisset

        <a href="{{ route('learner.coaching-time') }}" class="btn btn-secondary mt-4">Back</a>
    </div>
</div>
@endsection
