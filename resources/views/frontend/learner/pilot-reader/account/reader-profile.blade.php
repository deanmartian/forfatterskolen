@extends('frontend.layout')

@section('title')
    <title>Preferences &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
@stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content white-background">
            <div class="col-sm-12">
                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">

                    @include('frontend.learner.pilot-reader.partials.profile-nav')

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title with-border-b pb-2">Readers Profile</h4>
                            <p class="text-muted">{{ "Your reader profile allows the authors you read for to know a bit more about you. If you enjoy reading and would like to read for new authors, you can indicate that on your profile." }}</p>
                            <p class="text-muted"><strong>{{ "Please note that whatever you enter here will be shared with author you read for."}}</strong> {{ "Reader Profiles are strictly opt-in. You don't have to share anything, and you can edit your profile at any time." }}</p>
                            <hr/>
                            <h4 class="card-subtitle mb-1" id="full-name">{{ Auth::user()->full_name }}</h4>
                            <small class="text-muted d-block">{{ "Your name will be displayed wherever your profile is visible. If you would rather not use your real name, please change your name to the pen name of your choice." }}</small>
                            <small class="text-muted d-block mt-2"><strong>Please remember that the purpose of the reader profile is to share information with authors, and be careful about what personally identifying information you disclose.</strong></small>
                            <form id="readerProfileForm" class="mt-2">
                                <div class="form-group">
                                    <label class="label-control">Genre Preferences</label>
                                    <textarea name="genre_preferences" cols="30" rows="3" class="form-control"></textarea>
                                    <small class="text-muted d-block">{{ 'List or describe the genres and subgenres you most enjoy reading. e.g. "I love Science Fiction and some fantasy as long as the magic system is believable."' }}</small>
                                </div>
                                <div class="form-group">
                                    <label class="label-control">{{ "Content you don't want to read?" }}</label>
                                    <textarea name="dislike_contents" cols="30" rows="3" class="form-control"></textarea>
                                    <small class="text-muted d-block">{{ "Is there any content you" }} <strong>{{ "don't" }}</strong> {{ "want to read, or that would bother you if didn't know ahead of time that it was in the book?" }}</small>
                                </div>
                                <div class="form-group">
                                    <label class="label-control">Expertise</label>
                                    <textarea name="expertise" cols="30" rows="3" class="form-control"></textarea>
                                    <small class="text-muted d-block">{{ "Do you have any life experiences, knowledge, or vocational skills that you think might be helpful for authors looking for feedback?" }}</small>
                                </div>
                                <div class="form-group">
                                    <label class="label-control">Favorite Authors</label>
                                    <textarea name="favourite_author" cols="30" rows="3" class="form-control"></textarea>
                                    <small class="text-muted d-block">{{ "What authors do you enjoy reading most? Are there particular styles that you really like? " }}</small>
                                </div>
                                <div class="form-group">
                                    <label class="label-control">Availability</label>
                                    <div class="custom-control custom-checkbox no-left-padding">
                                        <input type="checkbox" name="availability" class="custom-control-input" id="customCheck2">
                                        <label class="custom-control-label" for="customCheck2">I am available and would like to beta read for new authors.</label>
                                    </div>
                                    <small class="text-muted d-block">{{ "If you indicate that you are available, you grant BetaBooks permission to allow authors to contact you via the BetaBooks app. You can opt-out at any time." }}</small>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-outline-info btn-sm pull-right">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div> <!-- end col-md-8 col-sm-offset-2 col-sm-12 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content white-background -->

        <div class="clearfix"></div>

    </div> <!-- end account-container -->
@stop

@section('scripts')
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('js/pilot-reader/account.js') }}"></script>
@stop