<div class="panel" id="master-panel" data-record="{{ json_encode($book->detail) }}">
    <div class="panel-body">
        <div class="col-md-6">
            <form method="POST" action="{{ route('admin.project.storage.save-details', $book->id) }}"
                onsubmit="disableSubmit(this)">
                @csrf
                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">Subtitle</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="subtitle" 
                            value="{{ $book->detail->subtitle ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">Original Title</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="original_title"
                            value="{{ $book->detail->original_title ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">Author</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="author"
                            value="{{ $book->detail->author ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">Editor</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="editor"
                            value="{{ $book->detail->editor ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">Publisher</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="publisher"
                            value="{{ $book->detail->publisher ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">Book Group</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="book_group"
                            value="{{ $book->detail->book_group ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">Item Number</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="item_number"
                            value="{{ $book->detail->item_number ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">ISBN</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="isbn"
                            value="{{ $book->detail->isbn ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">ISBN E-bok</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="isbn_ebook"
                            value="{{ $book->detail->isbn_ebook ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">Edition on sale</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="number" class="form-control" name="edition_on_sale"
                            value="{{ $book->detail->edition_on_sale ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">Sum all editions</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="number" class="form-control" name="edition_total"
                            value="{{ $book->detail->edition_total ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">Release Date</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="date" class="form-control" name="release_date"
                            value="{{ $book->detail->release_date ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">Release Date For Media</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="date" class="form-control" name="release_date_for_media"
                            value="{{ $book->detail->release_date_for_media ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">Price ex VAT</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="number" class="form-control" name="price_vat"
                            value="{{ $book->detail->price_vat ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">
                                Registered with the cultural council
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="registered_with_council"
                            value="{{ $book->detail->registered_with_council ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="pull-right">
                    <button type="button" class="btn btn-primary btn-sm" id="editMasterBtn">
                        Edit
                    </button>
        
                    <div class="save-master-container hidden">
                        <button type="submit" class="btn btn-primary btn-sm">
                            Save
                        </button>
        
                        <button type="button" class="btn btn-default btn-sm" id="cancelMasterBtn">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div> <!-- end col-md-6 -->
    </div>
</div>