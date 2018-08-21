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
                            <h4 class="card-title with-border-b pb-2">Preferences</h4>

                            <div class="form-group mt-4">
                                <h5 class="card-subtitle lead-17 mb-2 font-gothic-regular">
                                    Primary Role
                                </h5>
                                <div class="custom-control custom-radio no-left-padding">
                                    <input type="radio" id="customRadio1" name="role" class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="customRadio1">Reader</label>
                                </div>
                                <small class="text-muted">{{ "I mainly enjoy reading other people's works, and don't write or don't feel like sharing my writing with others." }}</small>

                                <div class="custom-control custom-radio mt-2 no-left-padding">
                                    <input type="radio" id="customRadio2" name="role" class="custom-control-input" value="2" checked>
                                    <label class="custom-control-label" for="customRadio2">Writer</label>
                                </div>
                                <small class="text-muted">{{ "I mainly enjoy writing, and only occasionally have time or interest in reading other people's stuff." }}</small>

                                <div class="custom-control custom-radio mt-2 no-left-padding">
                                    <input type="radio" id="customRadio3" name="role" class="custom-control-input" value="3">
                                    <label class="custom-control-label" for="customRadio3">Both</label>
                                </div>
                                <small class="text-muted">{{ "I'm a writer and I also actively read and provide feedback for other writers." }}</small>
                            </div>

                            <div class="form-group">
                                <h5 class="card-subtitle lead mb-2">
                                    Other Settings
                                </h5>
                                <div class="custom-control custom-checkbox no-left-padding">
                                    <input type="checkbox" name="joined_reader_community" class="custom-control-input" id="customCheck1">
                                    <label class="custom-control-label" for="customCheck1">Join the Reader Community?</label>
                                </div>
                                <small class="text-muted d-block mt-1">{{ "If you join the reader community we will occasionally email you to let you know about new beta reading opportunities. There's never any obligation to read, and you can opt-out at any time." }}</small>
                            </div>

                            <div class="form-group">
                                <button type="button" class="btn btn-outline-info btn-sm pull-right" onclick="methods.setUserPreferences()">Save Changes</button>
                            </div>
                        </div>
                    </div>

                </div> <!-- end col-md-8 col-sm-offset-2 col-sm-12 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content white-background -->

        <div class="clearfix"></div>
    </div>
@stop

@section('scripts')
    <script>
        let reader_profile_link = "{{ route('learner.pilot-reader.account.reader-profile') }}";
    </script>
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('js/pilot-reader/account.js') }}"></script>
@stop