@extends('frontend.layouts.course-portal')

@section('page_title', 'Manusrom › Skrivefellesskap › Forfatterskolen')
@section('robots', '<meta name="robots" content="noindex, follow">')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/community.css?v=' . time()) }}">
@stop

@section('content')
<div class="learner-container community-wrapper">
    <div class="container">
        @include('frontend.learner.community._nav')

        <div class="coming-soon-wrapper">
            <div class="coming-soon-icon">
                <i class="fa fa-book"></i>
            </div>
            <h1 class="coming-soon-title">Manusrom</h1>
            <p class="coming-soon-subtitle">Kommer snart</p>

            <div class="coming-soon-description">
                <p>Manusrommet er en privat prosjektrom der du kan jobbe med manuset ditt og få tilbakemeldinger fra skrivefellesskapet.</p>
            </div>

            <div class="coming-soon-features">
                <div class="coming-soon-feature">
                    <div class="coming-soon-feature-icon"><i class="fa fa-edit"></i></div>
                    <div>
                        <strong>Private prosjektrom</strong>
                        <p>Opprett prosjekter for manuset ditt med sjanger, status og ordtelling.</p>
                    </div>
                </div>
                <div class="coming-soon-feature">
                    <div class="coming-soon-feature-icon"><i class="fa fa-files-o"></i></div>
                    <div>
                        <strong>Del utdrag</strong>
                        <p>Last opp utdrag fra manuset ditt og del dem med fellesskapet.</p>
                    </div>
                </div>
                <div class="coming-soon-feature">
                    <div class="coming-soon-feature-icon"><i class="fa fa-comments"></i></div>
                    <div>
                        <strong>Tilbakemeldinger</strong>
                        <p>Få konstruktive tilbakemeldinger fra andre skribenter på tekstene dine.</p>
                    </div>
                </div>
                <div class="coming-soon-feature">
                    <div class="coming-soon-feature-icon"><i class="fa fa-users"></i></div>
                    <div>
                        <strong>Følg prosjekter</strong>
                        <p>Følg andre sine prosjekter og bli med på skrivefellesskapet.</p>
                    </div>
                </div>
            </div>

            <div class="coming-soon-note">
                <i class="fa fa-info-circle"></i>
                Manusrom vil være tilgjengelig for studenter i årskurs og påbygg.
            </div>
        </div>
    </div>
</div>
@stop
