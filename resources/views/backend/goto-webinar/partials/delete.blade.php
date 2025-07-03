<div id="deleteWebinarModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Notification</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.goto-webinar.destroy', $webinar['id']) }}"
                    onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <p>
                        Are you sure you want to delete this GoToWebinar notification?
                    </p>
                    <button type="submit" class="btn btn-danger pull-right margin-top">{{ trans('site.delete') }}</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>