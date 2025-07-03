<div id="deletePublisherBookModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{ trans('site.delete-publisher-book') }}</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.publisher-book.destroy', $book['id']) }}"
                    onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <p>{{ trans('site.delete-publisher-book-question') }}</p>
                    <button type="submit" class="btn btn-danger pull-right margin-top">{{ trans('site.delete') }}</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>