<div class="panel">
    <div class="panel-body">
        <div class="col-md-12">
            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">Subtitle</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control" name="subtitle" 
                        value="{{ $projectUserBook->detail->subtitle ?? '' }}" disabled>
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
                        value="{{ $projectUserBook->detail->original_title ?? '' }}" disabled>
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
                        value="{{ $projectUserBook->detail->author ?? '' }}" disabled>
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
                        value="{{ $projectUserBook->detail->editor ?? '' }}" disabled>
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
                        value="{{ $projectUserBook->detail->publisher ?? '' }}" disabled>
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
                        value="{{ $projectUserBook->detail->book_group ?? '' }}" disabled>
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
                        value="{{ $projectUserBook->detail->release_date ?? '' }}" disabled>
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
                        value="{{ $projectUserBook->detail->price_vat ?? '' }}" disabled>
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
                        value="{{ $projectUserBook->detail->registered_with_council ?? '' }}" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
