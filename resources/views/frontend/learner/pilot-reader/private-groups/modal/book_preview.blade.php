<div class="modal fade" id="bookPreviewModal" tabindex="-1" role="dialog" aria-labelledby="bookPreviewModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="bookPreviewModalLongTitle">Book Preview</h5>
            </div>
            <div class="modal-body">
                <div class="book-preview-details-load text-center">
                    <h3 class="d-inline-block">Loading Book Details</h3>
                    <i class="fa fa-spinner fa-pulse fa-2x fa-fw mt-2"></i>
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="book-preview-details display-none">
                    <h1 class="preview-detail font-weight-light mb-0 mt-0" data-id="book_title">Book 1</h1>
                    <h6 class="text-muted mb-0">By <span class="preview-detail" data-id="book_display_name">Jay</span></h6>
                    <small class="preview-detail text-muted" data-id="book_word_counts">0 words</small>
                    <p class="lead mt-2 mb-0">About the book</p>
                    <p class="preview-detail" data-id="book_about_book">ssgs</p>
                    <p class="lead mt-2 mb-0">Critique Guidance</p>
                    <p class="preview-detail" data-id="book_critique_guidance">dsfsdgs</p>
                    <hr/>
                    <span class="font-weight-light display-none" id="not-reader">Would you like to read and leave feedback on this book?</span>
                    <span class="font-weight-light display-none" id="quit-reader">
                        You already quit reading this book. Do you want to read it again?
                    </span>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-3" onclick="methods.becomeReader(this)">Become a reader</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
       </div>
    </div>
</div>