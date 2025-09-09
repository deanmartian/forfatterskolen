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
                    <h2>{{ $bookedEditorsCount }}</h2>
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
                        <form method="GET" action="{{ route('learner.coaching-time.available') }}">
                            @if($coachingTimers->count() > 1)
                                <div class="form-group mt-3">
                                    <label for="coaching_timer_id">Coaching Time</label>
                                    <select name="coaching_timer_id" id="coaching_timer_id" class="form-control">
                                        @foreach($coachingTimers as $timer)
                                            <option value="{{ $timer->id }}">Coaching Time #{{ $loop->iteration }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="coaching_timer_id" value="{{ $coachingTimers->first()->id }}">
                            @endif
                            <button type="submit" class="btn black-btn mt-4">
                                Se Tilgjengelige Tider
                            </button>
                        </form>
                    @else
                        <p class="mt-4">Ingen coaching time tilgjengelig.</p>
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

        <h3>Tilgjengelige Bokredaktører</h3>
        <div class="row">
            @foreach($editors as $editorSlots)
                <div class="col-sm-3">
                    <div class="panel panel-default text-center">
                        <div class="panel-body">
                            <div class="avatar">{{ substr($editorSlots->first()->editor->name, 0, 1) }}</div>
                            <p>{{ $editorSlots->first()->editor->name }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

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
