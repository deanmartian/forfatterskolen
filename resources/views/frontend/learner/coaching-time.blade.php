@extends('frontend.layouts.course-portal')

@section('title')
    <title>Coaching Time &rsaquo; Forfatterskolen</title>
@endsection

@section('styles')
<style>
    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #f5f5f5;
        line-height: 50px;
        margin: 0 auto 10px;
        font-size: 24px;
    }

    .stats-card {
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        text-align: center;
    }

    .stats-card h2 {
        margin: 0;
        font-size: 36px;
    }

    .stats-card p {
        margin: 0;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 12px;
    }

    .black-btn {
        width: 100%;
        border: 1px solid #e4e4e7;
        background: #ffffff;
        border-radius: 5px;
        color: #000000;
    }

    .black-btn:hover {
        background: #000000;
        color: #ffffff;
    }

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
        <h1 class="page-title">Coaching Time</h1>

        @if(session('success'))
            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                {{ session('success') }}
            </div>
        @endif

        @php
            $availableSlots = $editors->reduce(function ($carry, $group) {
                return $carry + $group->count();
            }, 0);
        @endphp

        <div class="row mb-5">
            <div class="col-sm-3">
                <div class="stats-card text-center">
                    <p>Mine Redaktører</p>
                    <h2>{{ $editors->count() }}</h2>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="stats-card">
                    <p>Neste Redaksjon</p>
                    <h2>-</h2>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="stats-card">
                    <p>Denne Måneden</p>
                    <h2>-</h2>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="stats-card">
                    <p>Ledige Slots</p>
                    <h2>{{ $availableSlots }}</h2>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-6">
                <div class="stats-card text-left">
                    <h3>Book Redaksjonstime</h3>
                    <span>Velg redaktør og tid for å booke din neste sesjon.</span>
                    
                    @if($coachingTimers->count() >= 1)
                        <p class="mt-4">Se tilgjengelige tider nedenfor.</p>
                    @else
                        <p>Ingen coaching time tilgjengelig.</p>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="stats-card text-left">
                    <h3>Mine Sesjoner</h3>
                    <span>Ingen kommende sesjoner.</span>
                </div>
            </div>
        </div>

        @php
            $availableTimers = $coachingTimers->filter(fn($t) => $t->requests->isEmpty());
            $hasPendingRequest = $coachingTimers->pluck('requests')
                ->flatten()
                ->where('status', 'pending')
                ->isNotEmpty();
        @endphp

        @if($coachingTimers->count())
            @if($coachingTimers->count() > 1 && $availableTimers->count())
                <div class="form-group">
                    <label for="coaching_timer_select">Select Coaching Time</label>
                    <select id="coaching_timer_select" class="form-control">
                        <option value="" disabled selected>Select Coaching Time</option>
                        @foreach($availableTimers as $timer)
                            <option value="{{ $timer->id }}">Coaching Time #{{ $loop->iteration }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

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
                                                        <form method="POST" action="{{ route('learner.coaching-time.request') }}" class="mt-2 book-form">
                                                            @csrf
                                                            <input type="hidden" name="coaching_timer_id">
                                                            <input type="hidden" name="editor_time_slot_id" value="{{ $slot->id }}">
                                                            <button type="submit" class="btn btn-primary btn-sm" disabled>Book</button>
                                                        </form>
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

        <h3>Hurtighandlinger</h3>
        <div class="row">
            <div class="col-sm-3"><button class="btn btn-default btn-block">Endre Tidspunkt</button></div>
            <div class="col-sm-3"><button class="btn btn-default btn-block">Avbryt Booking</button></div>
            <div class="col-sm-3"><button class="btn btn-default btn-block">Kontakt Redaktør</button></div>
            <div class="col-sm-3"><button class="btn btn-default btn-block">&nbsp;</button></div>
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

        @if($coachingTimers->count() > 1)
        const timerSelect = document.getElementById('coaching_timer_select');
        document.querySelectorAll('.book-form').forEach(function(form){
            const button = form.querySelector('button');
            button.disabled = !timerSelect.value;
            timerSelect.addEventListener('change', function(){
                button.disabled = !this.value;
            });
            form.addEventListener('submit', function(e){
                if(!timerSelect.value){
                    e.preventDefault();
                    return;
                }
                form.querySelector('input[name="coaching_timer_id"]').value = timerSelect.value;
            });
        });
        @endif
    });
</script>
@endsection
