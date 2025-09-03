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
            <div class="alert alert-success">{{ session('success') }}</div>
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
                    
                    @isset($coachingTimer)
                        <button class="btn black-btn mt-4" data-toggle="modal" data-target="#availableTimesModal">
                            Se Tilgjengelige Tider
                        </button>
                    @else
                        <p>Ingen coaching time tilgjengelig.</p>
                    @endisset
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

<!-- Modal -->
<div class="modal fade" id="availableTimesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Tilgjengelige Tider</h4>
            </div>
            <div class="modal-body">
                <div class="panel-group" id="editorAccordion" role="tablist" aria-multiselectable="true">
                    @isset($coachingTimer)
                        @forelse($editors as $editorId => $editorSlots)
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading{{ $editorId }}">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#editorAccordion" href="#collapse{{ $editorId }}" aria-expanded="false" aria-controls="collapse{{ $editorId }}">
                                            {{ $editorSlots->first()->editor->name }}
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse{{ $editorId }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{ $editorId }}">
                                    <div class="panel-body">
                                        <ul class="list-unstyled">
                                            @foreach($editorSlots as $slot)
                                                <li class="clearfix">
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
                            </div>
                        @empty
                            <p>Ingen tilgjengelige tidsluker.</p>
                        @endforelse
                    @else
                        <p>Ingen coaching time tilgjengelig.</p>
                    @endisset
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
