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
        <div class="card card-global">
            <div class="card-body">
                <div class="mb-3">
                    <a href="{{ route('learner.coaching-time') }}" class="btn btn-secondary">Back</a>
                </div>
                <h1 class="page-title">Available Time Slots</h1>

                @php
                    $hasPendingRequest = $coachingTimer && $coachingTimer->requests->where('status', 'pending')->isNotEmpty();
                @endphp

                @if($coachingTimers->count())
                    @foreach($editors as $editorSlots)
                        <h3 class="mt-4">Available Time Slots - {{ $editorSlots->first()->editor->full_name }}</h3>

                        @php
                            $dateGroups = $editorSlots->groupBy('date')->sortKeys();
                            $chunks = $dateGroups->chunk(7);
                        @endphp

                        <div class="editor-slots" id="editor-{{ $loop->index }}">
                            @if($chunks->count() > 1)
                                <div class="d-flex justify-content-end mb-2">
                                    <button type="button" class="btn btn-secondary btn-sm prev-btn mr-2 px-3 bg-white" data-editor="{{ $loop->index }}" disabled>
                                        <i class="fa fa-chevron-left text-dark"></i>
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm next-btn px-3 bg-white" data-editor="{{ $loop->index }}">
                                        <i class="fa fa-chevron-right text-dark"></i>
                                    </button>
                                </div>
                            @endif

                            @foreach($chunks as $page => $chunk)
                                <div class="editor-page" data-page="{{ $page }}" @if($page > 0) style="display:none;" @endif>
                                    @foreach($chunk as $date => $slots)
                                        <div class="mb-4">
                                            <h4>{{ \Carbon\Carbon::parse($date, 'UTC')->isoFormat('dddd - MMMM D') }}</h4>
                                            <div class="d-flex flex-wrap">
                                                @foreach($slots->sortBy('start_time') as $slot)
                                                    <div class="slot-card">
                                                        <div><i class="fa fa-clock-o"></i></div>
                                                        <div class="mt-2 slot-time" data-time="{{ \Carbon\Carbon::parse($slot->date.' '.$slot->start_time, 'UTC')->toIso8601String() }}"></div>
                                                        <div>{{ $slot->duration }} min</div>
                                                        @php
                                                            $requested = $slot->requests
                                                                ->where('status', 'pending')
                                                                ->whereIn('coaching_timer_manuscript_id', $coachingTimers->pluck('id'))
                                                                ->isNotEmpty();
                                                        @endphp
                                                        @if($requested)
                                                            <div class="mt-2 text-muted">Requested</div>
                                                        @elseif($hasPendingRequest)
                                                            {{-- No action available while another request is pending --}}
                                                        @elseif($coachingTimer)
                                                            <form method="POST" action="{{ route('learner.coaching-time.request') }}" class="mt-2">
                                                                @csrf
                                                                <input type="hidden" name="coaching_timer_id" value="{{ $coachingTimer->id }}">
                                                                <input type="hidden" name="editor_time_slot_id" value="{{ $slot->id }}">
                                                                <button type="submit" class="btn btn-primary btn-sm">Book</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    <p>Ingen coaching time tilgjengelig.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.slot-time').forEach(function (el) {
            const dt = new Date(el.dataset.time);
            let hours = dt.getHours();
            const minutes = dt.getMinutes();
            const suffix = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12 || 12;
            const formatted = minutes ? `${hours}:${String(minutes).padStart(2, '0')}${suffix}` : `${hours}${suffix}`;
            el.textContent = formatted;
        });

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
