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
                    $availableTimers = $coachingTimers->filter(fn($t) => $t->requests->isEmpty());
                    $hasPendingRequest = $coachingTimers->pluck('requests')
                        ->flatten()
                        ->where('status', 'pending')
                        ->isNotEmpty();
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
                                                        @else
                                                            @if($coachingTimers->count() === 1)
                                                                <form method="POST" action="{{ route('learner.coaching-time.request') }}" class="mt-2">
                                                                    @csrf
                                                                    <input type="hidden" name="coaching_timer_id" value="{{ $availableTimers->first()->id }}">
                                                                    <input type="hidden" name="editor_time_slot_id" value="{{ $slot->id }}">
                                                                    <button type="submit" class="btn btn-primary btn-sm">Book</button>
                                                                </form>
                                                            @else
                                                                <button type="button" class="btn btn-primary btn-sm book-btn" data-slot-id="{{ $slot->id }}">Book</button>
                                                            @endif
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
@if($coachingTimers->count() > 1)
<!-- Hidden trigger -->
<button id="hiddenTrigger" type="button" data-toggle="modal" data-target="#selectCoachingTimerModal" style="display:none;"></button>
    <div id="selectCoachingTimerModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Coaching Time</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('learner.coaching-time.request') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="modal_coaching_timer_id">Coaching Time</label>
                            <select name="coaching_timer_id" id="modal_coaching_timer_id" class="form-control">
                                @foreach($availableTimers as $timer)
                                    <option value="{{ $timer->id }}">Coaching Time #{{ $loop->iteration }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="editor_time_slot_id" id="modal_editor_time_slot_id">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Book</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
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

        @if($coachingTimers->count() > 1)
        document.querySelectorAll('.book-btn').forEach(function(btn){
            btn.addEventListener('click', function(){
                const slotId = this.dataset.slotId;
                document.getElementById('modal_editor_time_slot_id').value = slotId;

                // Simulate clicking the hidden trigger (Bootstrap handles it properly)
                document.getElementById('hiddenTrigger').click();
                //$('#selectCoachingTimerModal').modal('show');
            });
        });
        @endif
    });
</script>
@endsection
