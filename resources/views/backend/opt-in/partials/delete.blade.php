<div id="deleteOptInModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Opt-in</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.opt-in.destroy', $optIn['id']) }}">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <p>Are you sure you want to delete this opt-in?</p>
                    <button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>