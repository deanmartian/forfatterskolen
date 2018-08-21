<div class="modal fade" id="createPrivateGroupModal" tabindex="-1" role="dialog" aria-labelledby="createPrivateGroupModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="createPrivateGroupForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="createPrivateGroupModalLongTitle">New Group</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="label-control font-weight-light">Group Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="label-control font-weight-light">Discussion Policy</label>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="customRadio1" name="policy" class="custom-control-input" value="1" checked>
                            <label class="custom-control-label" for="customRadio1">Open Discussion</label>
                        </div>
                        <small class="d-block text-muted">Anyone can start a discussion or reply.</small>
                        <div class="custom-control custom-radio mt-2">
                            <input type="radio" id="customRadio2" name="policy" class="custom-control-input" value="2">
                            <label class="custom-control-label" for="customRadio2">Announcements and Replies</label>
                        </div>
                        <small class="d-block text-muted">Only the group manager(s) can start a discussion, but members can reply.</small>
                        <div class="custom-control custom-radio mt-2">
                            <input type="radio" id="customRadio3" name="policy" class="custom-control-input" value="3">
                            <label class="custom-control-label" for="customRadio3">Announcements Only</label>
                        </div>
                        <small class="d-block text-muted">Only the group manager(s) can start a discussion and no replies are allowed.</small>
                    </div>
                    <div class="form-group">
                        <label class="label-control font-weight-light mb-0">Contact Email</label>
                        <small class="d-block text-muted">If you want group members to be able to email you with questions or to ask for help, please set this field. If set, the Group Contact email will be displayed for group members on the group home page and where relevant throughout the group.</small>
                        <div class="form-group mt-3">
                            <input type="text" name="contact_email" class="form-control" placeholder="Enter email here">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Create</button>
                </div>
            </form>
       </div>
    </div>
</div>