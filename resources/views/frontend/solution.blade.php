@extends('frontend.layout')

@section('title')
    <title>Support &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="container">

        <div class="col-sm-12 margin-bottom support-tabs">
            <h1 class="margin-bottom">
                {{ trans('site.front.support.instructions') }}
            </h1>
            <br />

            @foreach($instructions->chunk(3) as $instructions_chunk)
                <div class="row support">
                    @foreach($instructions_chunk as $instruction)
                        <div class="col-sm-4 instruction-card">
                            <a href="{{ route('front.support-articles', $instruction->id) }}">
                                <div class="instruction-icon">
                                    <img src="{{ asset($instruction->image) }}" alt="">
                                </div>
                                <h2>{{ $instruction->title }}</h2>
                                <p>
                                    {{ $instruction->description }}
                                </p>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endforeach

        </div>

        <div class="col-sm-12 margin-bottom support-tabs">
            <h1 class="margin-bottom">
                {{ trans('site.front.support.support-text') }}
            </h1>
            <br />

            @foreach($solutions->chunk(4) as $solution_chunk)
                <div class="row support">
                    @foreach($solution_chunk as $solution)
                        <div class="col-sm-3 support-card">
                            <a href="{{ route('front.support-articles', $solution->id) }}">
                                <h3>{{ $solution->title }}</h3>
                                <p>
                                    {{ $solution->description }}
                                </p>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endforeach

        </div>
    </div>
@stop