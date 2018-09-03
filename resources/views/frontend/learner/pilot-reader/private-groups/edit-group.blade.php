@extends('frontend.learner.pilot-reader.private-groups.layout')

@section('private-content')
    <div class="global-card font-14-body">
        <div class="card-body">
            <h1 class="font-weight-light">Edit Group</h1>
            <form id="editGroupForm">
                <div class="form-group">
                    <label class="label-control font-weight-light">Group Name</label>
                    <input type="text" name="edit_name" class="form-control">
                </div>
                <div class="form-group">
                    <label class="label-control font-weight-light">Discussion Policy</label>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="customRadio8" name="edit_policy" class="custom-control-input" value="1">
                        <label class="custom-control-label" for="customRadio8">Open Discussion</label>
                    </div>
                    <small class="d-block text-muted">Anyone can start a discussion or reply.</small>
                    <div class="custom-control custom-radio mt-2">
                        <input type="radio" id="customRadio9" name="edit_policy" class="custom-control-input" value="2">
                        <label class="custom-control-label" for="customRadio9">Announcements and Replies</label>
                    </div>
                    <small class="d-block text-muted">Only the group manager(s) can start a discussion, but members can reply.</small>
                    <div class="custom-control custom-radio mt-2">
                        <input type="radio" id="customRadio10" name="edit_policy" class="custom-control-input" value="3">
                        <label class="custom-control-label" for="customRadio10">Announcements Only</label>
                    </div>
                    <small class="d-block text-muted">Only the group manager(s) can start a discussion and no replies are allowed.</small>
                </div>
                <div class="form-group">
                    <label class="label-control font-weight-light mb-0">Contact Email</label>
                    <small class="d-block text-muted">If you want group members to be able to email you with questions or to ask for help, please set this field. If set, the Group Contact email will be displayed for group members on the group home page and where relevant throughout the group.</small>
                    <div class="form-group mt-3">
                        <input type="text" name="edit_contact_email" class="form-control" placeholder="Enter email here">
                    </div>
                </div>
                <div class="form-group clearfix">
                    <button class="btn btn-outline-info btn-sm float-right">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@stop