<div class="global-card">
    <div class="card-body">
        <h5 class="card-title clearfix">
            <span data-title="title">{{ $book->title }}</span>
        </h5>
        <h6 class="card-subtitle with-border-b pb-2 text-muted clearfix">
            Reader Settings
        </h6>
        <p class="lead-17 mt-3 text-muted">Notification Prerences</p>
        <div class="form-group">
            <input type="checkbox" class="custom-control-input" id="customCheck1">
            <label class="custom-control-label" for="customCheck1">Enable email notifications?</label>
        </div>
        <p class="lead-17 mt-3 text-muted">Finished Reading</p>
        @if($readingBook->status == 0)
            <p>{{ "Done with the book? You can let the author know you're finished with it and remove it from your sidebar." }}</p>
            <div class="form-group mt-2 mb-0">
                <div id="btn-container">
                    <button class="btn btn-success btn-sm" onclick="settings.submitReadingStatus(1)">I finished reading</button>
                    <button class="btn btn-danger btn-sm" onclick="settings.toggleReasonContainer('show')">I need to quit</button>
                </div>
                <div class="mt-1 display-none" id="reasons_div">
                    <form id="quitForm">
                        <div class="form-group mb-1">
                            <label for="" class="label-control text-muted">{{ "Please let the author know why you weren't able to finish the book:" }}</label>
                            <textarea name="reasons" cols="30" rows="5" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-info btn-sm" type="submit">Quit</button>
                            <button class="btn btn-secondary btn-sm" onclick="settings.toggleReasonContainer('hide')" type="button">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($readingBook->status == 1)
            <p><i class="fa fa-check right-space"></i>Great work finishing the book, thanks for letting the author know!</p>
        @endif

        <p class="lead-17 mt-3 text-muted">Author's Options</p>
        <p>{{ "The author has chosen to keep comments private, so whatever you post can only be seen by yourself and the author." }}</p>

    </div>
</div>