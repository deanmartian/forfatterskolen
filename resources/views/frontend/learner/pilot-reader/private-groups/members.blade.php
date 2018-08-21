@extends('frontend.learner.pilot-reader.private-groups.layout')

@section('private-content')

    <div class="global-card font-14-body">
        <div class="card-body">
            <h1 class="font-weight-light mb-0 mt-0">Group Members</h1>
            <small class="d-block text-muted">From this page you can invite and remove group members.</small>
            <h3 class="font-weight-light mt-2">Manage Invitations</h3>
            <div class="form-group mt-3">
                <h5 class="card-subtitle font-weight-light">
                    Invitation Link
                </h5>
                <div class="form-group mt-2 mb-0">
                    <div class="switch-container">
                        <label class="switch">
                            <input type="checkbox" class="link-toggle" onchange="methods.getLink('toggle', this)">
                            <span class="slider round"></span>
                        </label>
                        <label class="switch-label">Enable Link</label>
                    </div>
                </div>
                <div class="form-group margin-top display-none" id="shareable_link_div">
                    <div class="label-control font-italic mb-1">Your shareable link</div>
                    <div class="input-group-global mb-3">
                        <input type="text" class="form-control bg-light" readonly />
                        <div class="input-group-append">
                            <button class="btn btn-info" type="button" onclick="methods.copyToClipboard(this)"><i class="fa fa-clipboard"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <h5 class="card-subtitle font-weight-light">
                Send Invitations
            </h5>
            <form id="inviteForm">
                <div class="input-group-global mb-0 mt-2">
                    <input type="text" class="form-control" id="email" placeholder="Recipient's email address" aria-label="Recipient's email address" aria-describedby="basic-addon2" autocomplete="off">
                    <div class="input-group-append">
                        <button class="btn btn-info" type="submit"><i class="fa fa-plus-circle"></i></button>
                    </div>
                </div>
            </form>
            <div class="form-group form-control mt-2 display-none" id="email_list">
            </div>
            <div class="form-group mt-2 mb-2">
                <div class="custom-control custom-checkbox no-left-padding">
                    <input type="checkbox" class="custom-control-input" id="add_personal_msg" onchange="methods.onAddPersonalMsg(this)">
                    <label class="custom-control-label" for="add_personal_msg">Add a personal message</label>
                </div>
                <textarea class="form-control  mt-2 display-none" id="personal_msg" cols="30" rows="5" ></textarea>
            </div>
            <button class="btn btn-outline-success disabled" onclick="methods.onSendInvite()" disabled id="send_invite"><i class="fa fa-spinner fa-pulse fa-fw display-none"></i> Send</button>
            <div class="margin-top invitation-manager">
                <nav>
                    <ul class="nav nav-tabs">
                        <li>
                            <a href="#nav-pending" data-toggle="tab" onclick="methods.listInvitations(0, 0)">Pending</a>
                        </li>
                        <li>
                            <a href="#nav-decline" data-toggle="tab" onclick="methods.listInvitations(1, 2)">Decline</a>
                        </li>
                        <li>
                            <a href="#nav-members" data-toggle="tab" onclick="methods.listInvitations(2, 1)">Members</a>
                        </li>
                    </ul>
                </nav>
                <div class="tab-content margin-top" id="nav-tabContent">
                    <div class="tab-pane fade" id="nav-pending" role="tabpanel" aria-labelledby="nav-pending-tab">
                        <table id="pending-table" class="table table-striped table-bordered" width="100%">
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>Sent At</th>
                                <th>Send Count</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-decline" role="tabpanel" aria-labelledby="nav-decline-tab">
                        <table id="decline-table" class="table table-striped table-bordered" width="100%">
                            <thead>
                            <tr>
                                <th>Reader</th>
                                <th>Decline At</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-members" role="tabpanel" aria-labelledby="nav-members-tab">
                        <table id="members-table" class="table table-striped table-bordered" width="100%">
                            <thead>
                            <tr>
                                <th>Member</th>
                                <th>Joined at</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop