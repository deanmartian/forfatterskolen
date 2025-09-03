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
        <div class="mb-3">
            <a href="{{ route('learner.coaching-time') }}" class="btn btn-secondary">Back</a>
        </div>
        <h1 class="page-title">Available Time Slots</h1>

        @isset($coachingTimer)
            @foreach($editors as $editorSlots)
                <h3 class="mt-4">Available Time Slots - {{ $editorSlots->first()->editor->full_name }}</h3>

                @php
                    $dateGroups = $editorSlots->groupBy('date');
                    $chunks = $dateGroups->chunk(7);
                @endphp

                <div class="editor-slots" id="editor-{{ $loop->index }}">
                    @foreach($chunks as $page => $chunk)
                        <div class="editor-page" data-page="{{ $page }}" @if($page > 0) style="display:none;" @endif>
                            @foreach($chunk as $date => $slots)
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
                        </div>
                    @endforeach

                    @if($chunks->count() > 1)
                        <div class="d-flex justify-content-between mt-2">
                            <button type="button" class="btn btn-secondary btn-sm prev-btn" data-editor="{{ $loop->index }}" disabled>Prev</button>
                            <button type="button" class="btn btn-secondary btn-sm next-btn" data-editor="{{ $loop->index }}">Next</button>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <p>Ingen coaching time tilgjengelig.</p>
        @endisset
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.editor-slots').forEach(function (wrapper) {
            const pages = wrapper.querySelectorAll('.editor-page');
            if (pages.length <= 1) {
                return;
            }

            let current = 0;
            const prevBtn = wrapper.querySelector('.prev-btn');
            const nextBtn = wrapper.querySelector('.next-btn');

            function updateButtons() {
                prevBtn.disabled = current === 0;
                nextBtn.disabled = current === pages.length - 1;
            }

            function showPage(index) {
                pages[current].style.display = 'none';
                current = index;
                pages[current].style.display = 'block';
                updateButtons();
            }

            prevBtn.addEventListener('click', function () {
                if (current > 0) {
                    showPage(current - 1);
                }
            });

            nextBtn.addEventListener('click', function () {
                if (current < pages.length - 1) {
                    showPage(current + 1);
                }
            });

            updateButtons();
        });
    });
</script>
@endsection
