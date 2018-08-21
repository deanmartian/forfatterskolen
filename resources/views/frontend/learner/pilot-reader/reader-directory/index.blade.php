@extends('frontend.layout')

@section('title')
    <title>Reader Directory &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
@stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content reader-directory-search white-background">
            <div class="col-sm-12">
                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">

                    @include('frontend.learner.pilot-reader.partials.reader-directory-nav')

                    <div class="card margin-top">
                        <div class="card-body">
                            <h4 class="card-title with-border-b pb-2">
                                The Reader Directory
                            </h4>

                            <div class="callout sans tiny">
                                <p>
                                    <strong>Want to be listed as a reader?</strong> Only readers who have opted-in via
                                    their Reader Profile are listed in the directory. To get listed, create your
                                    <strong><i>reader profile</i></strong> and indicate that you're available to read.
                                </p>
                                <p>
                                    <strong>Want to query readers?</strong> <i>Upgrade to one of our paid plans.</i>
                                    Your support unlocks all our features and pays to keep the servers running.
                                </p>
                            </div>

                            <div class="form-group margin-top">
                                <div class="form-group" id="simpleSearchbox">
                                    <form class="searchBoxForm">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="Enter here..." aria-label="Enter here..." aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-success border-color-grey" type="submit"><i class="fa fa-search"></i> Search</button>
                                                <button class="btn btn-outline-info border-color-grey" type="reset"><i class="fa fa-redo"></i> Reset</button>
                                            </div>
                                        </div>

                                        <small class="text-muted">
                                            This will search all the content in the reader's
                                            profile. Try searching for a genre like "SciFi" or a favorite author. It's
                                            interesting to see how readers describe their interests, and we encourage
                                            you to explore!
                                        </small>
                                    </form> <!-- end searchBoxForm -->
                                </div> <!-- end #simpleSearchbox -->

                                <div class="form-group display-none clearfix" id="advancedSearchbox">
                                    <form class="searchBoxForm">
                                        <small class="d-block text-muted mb-1">{{ "This will search within just the specified field in the reader's profile. Use this to narrow down your results if you're getting too many matches from the simple search." }}</small>
                                        <div class="form-group">
                                            <label class="label-control">Genre Preferences</label>
                                            <input type="text" class="form-control" name="genre_preferences">
                                        </div>
                                        <div class="form-group">
                                            <label class="label-control">{{ "What don't you want to read?" }}</label>
                                            <input type="text" class="form-control" name="dislike_contents">
                                        </div>

                                        <div class="form-group">
                                            <label class="label-control">Expertise</label>
                                            <input type="text" class="form-control" name="expertise">
                                        </div>
                                        <div class="form-group">
                                            <label class="label-control">Favorite Authors</label>
                                            <input type="text" class="form-control" name="favourite_author">
                                        </div>

                                        <div class="form-group mt-2">
                                            <button class="btn btn-outline-success border-color-grey float-right ml-1" type="submit"><i class="fa fa-search"></i> Search</button>
                                            <button class="btn btn-outline-info border-color-grey float-right" type="reset"><i class="fa fa-redo"></i> Reset</button>
                                        </div>
                                    </form>
                                </div> <!-- end #advancedSearchbox -->

                                <div class="jumbotron jumbotron-fluid">
                                    <div class="form-group mb-0">
                                        <label class="label-control">Search Mode:</label>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="customRadioInline1" name="search_mode" class="custom-control-input" checked value="simple">
                                            <label class="custom-control-label" for="customRadioInline1">Simple</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="customRadioInline2" name="search_mode" class="custom-control-input" value="advanced">
                                            <label class="custom-control-label" for="customRadioInline2">Advanced</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <h5 class="lead">Search Results</h5>
                                <div class="form-group" id="resultList">
                                </div>
                            </div>

                        </div><!-- end card-body -->
                    </div> <!-- end card -->

                </div><!-- end col-md-8 col-sm-offset-2 col-sm-12 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content reader-directory-search white-background -->

        <div class="clearfix"></div>
    </div>

    <!-- start queryReaderModal -->
    <div class="modal fade" id="queryReaderModal" tabindex="-1" role="dialog" aria-labelledby="queryReaderModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="queryReaderForm">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title" id="queryReaderModalLongTitle">Querying <span id="author_name"></span></h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="to">
                        <div class="form-group">
                            <label for="" class="label-control">Select a Book</label>
                            <select name="book_id" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <label for="" class="label-control">Query Letter</label>
                            <textarea name="letter" cols="30" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end queryReaderModal -->
@stop

@section('scripts')
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('js/pilot-reader/reader-directory.js') }}"></script>
@stop