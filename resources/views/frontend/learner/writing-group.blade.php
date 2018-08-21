@extends('frontend.layout')

@section('title')
    <title>Writing Group &rsaquo; Forfatterskolen</title>
@stop

@section('heading') Skrivegrupper @stop

@section('styles')
    <style>
        #group-name-container {
            padding-left: 0;
        }

        #contact-id-container {
            padding-right: 0;
        }

        img {
            width: 100%;
            height: 200px;
        }
    </style>
@stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content">
            <div class="col-sm-12">

                <div class="row">
                    <div class="col-sm-4">
                        <h3 class="no-margin-top">@yield('heading')</h3>
                    </div>
                </div>

                <div class="col-sm-12 col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group col-md-6" id="group-name-container">
                                <label>Group Name</label> <br>
                                {{ $writingGroup->name }}
                            </div>
                            <div class="form-group col-md-6" id="contact-id-container">
                                <label>Contact Person</label> <br>
                                {{ \App\User::find($writingGroup->contact_id)->full_name }}
                            </div>

                            <div class="form-group">
                                <label>About the group</label> <br>
                                <p>
                                    {!! nl2br($writingGroup->description) !!}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label id="course-image">Photo</label>
                                @if ($writingGroup->group_photo)
                                    <img src="{{$writingGroup->group_photo}}" class="img">
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Next Meeting</label>
                                @if ($writingGroup->contact_id == Auth::user()->id)
                                    <form method="POST" action="{{ route('learner.update.writing-group', $writingGroup->id) }}">
                                        {{ method_field('PUT') }}
                                        {{csrf_field()}}
                                    <textarea name="next_meeting" id="" cols="20" rows="8" class="form-control"
                                    >{{ old('next_meeting') ? old('next_meeting') : $writingGroup->next_meeting }}</textarea>
                                    <button type="submit" class="btn btn-primary margin-top">Update Meeting</button>
                                    </form>
                                @else
                                    <p>
                                        {!! nl2br($writingGroup->next_meeting) !!}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="clearfix"></div>
    </div>

@stop

