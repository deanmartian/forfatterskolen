<!-- query detail modal -->

<div class="modal fade" id="queryReaderDetailModal" tabindex="-1" role="dialog" aria-labelledby="queryReaderDetailModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="queryReaderDetailModalLongTitle">Reader Query <span class="ml-1" id="status_div">Pending</span></h5>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <div class="with-border-b pb-2 clearfix mb-4">
                            <h5 class="card-title lead-17 pull-left mb-0 mt-0">Query Letter</h5>
                            <span class="pull-right text-muted font-weight-light" id="received_div"></span>
                        </div>
                        <div class="row">
                            <div class="col-md-3">From</div>
                            <div class="col" id="from_div"></div>
                        </div>
                        <hr class="my-2"/>
                        <div class="row">
                            <div class="col-md-3">To</div>
                            <div class="col" id="to_div"></div>
                        </div>
                        <hr class="my-2"/>
                        <div class="row">
                            <div class="col-md-3">Re</div>
                            <div class="col"><span id="book_div"></span> <small class="text-muted font-weight-light">(<span id="book_word_counts_div"></span>)</small></div>
                        </div>
                        <hr class="my-2"/>
                        <div class="form-group lead-17 mt-2" id="letter_div"></div>
                        <div class="form-group mb-0 clearfix">
                            <button class="btn btn-info btn-sm pull-right preview-btn" data-toggle="collapse" data-target="#collapseDiv" aria-expanded="false" aria-controls="collapseDiv"><i class="fa fa-book fa-fw"></i> Book Preview</button>
                        </div>
                        <div class="collapse mt-2" id="collapseDiv">
                            <div class="card">
                                <div class="card-body">
                                    <div class="with-border-b pb-1">
                                        <h5 class="card-title lead-17 mb-0" id="title_div"></h5>
                                        <small class="text-muted">By <span id="display_name_div"></span></small>
                                    </div>
                                    <small class="text-muted" id="word_counts_div"></small>
                                    <p class="lead-17 mb-0 mt-1">About the book</p>
                                    <small class="text-muted d-block" id="about_book_div"></small>
                                    <p class="lead-17 mb-0 with-border-b mt-2 pb-1">Content Review</p>
                                    <p class="card-subtile mb-0 mt-2" id="chapter_title_div"></p>
                                    <small class="d-block" id="chapter_content_div"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="with-border-b pb-2 clearfix mb-4">
                            <h5 class="card-title lead-17 pull-left mb-0 mt-0">Decision Letter</h5>
                            <span class="pull-right text-muted font-weight-light" id="decision_submitted_date_div"></span>
                        </div>
                        <div class="form-group lead-14 mt-2" id="decision_div">
                            Klaus von has not decided yet
                        </div>
                        <div class="form-group mt-2 display-none" id="your_decision_div">
                            <form id="decisionForm">
                                <input type="hidden" name="book_id" class="form-control" />
                                <input type="hidden" name="query_id" class="form-control" />
                                <div class="jumbotron jumbotron-fluid mt-2 p-3 mb-3 font-weight-light">
                                    {{ "There's no obligation to read a book when you're queried.
                                        In fact, one of the nicest things you can do is tell the
                                        author why the pitch didn't work for you, and then
                                        politely decline to read. This feedback will help the
                                        author learn how to pitch and sell their books." }}
                                </div>
                                <h5 class="card-title lead-17 mb-0">Your Decision</h5>
                                <div class="form-group mt-2">
                                    <label for="" class="label-control">{{ "From what you've seen in the query, what do you think of the book?" }} </label>
                                    <textarea name="decision" cols="30" rows="5" class="form-control"></textarea>
                                    <small class="d-block tetx-muted">Remember, this is a crucial feedback for the author to help them learn how to pitch and sell their work. Please give a much details as you can.</small>
                                </div>
                                <div class="form-group">
                                    <label for="" class="label-control">Do you want to beta read this book?</label>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="customRadio1" name="want_to_read" class="custom-control-input" value="1" checked>
                                        <label class="custom-control-label" for="customRadio1">{{ "Yes, I'd like to beta read it. Take me to the book!" }}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="customRadio2" name="want_to_read" class="custom-control-input" value="2">
                                        <label class="custom-control-label" for="customRadio2">{{ "No thanks, I don't this book is a fit for me" }}</label>
                                    </div>
                                </div>
                                <div class="form-group clearfix">
                                    <button type="submit" class="btn btn-success btn-sm">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- end query detail modal -->