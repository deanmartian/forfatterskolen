@extends('frontend.learner.pilot-reader.private-groups.layout')

@section('private-content')
    <div class="global-card">
        <div class="card-body">
            <h1 class="font-weight-light mt-0">Member Preferences</h1>
            <p class="lead">Email Notifications</p>
            <div class="form-group mt-2">
                <div class="custom-control custom-radio no-left-padding">
                    <input type="radio" id="customRadio4" name="email_notifications_option" class="custom-control-input" value="1">
                    <label class="custom-control-label" for="customRadio4">All Messages</label>
                </div>
                <small class="text-muted d-block mb-2">{{ "You'll receive an email notification for all messages posted to the group." }}</small>
                <div class="custom-control custom-radio no-left-padding">
                    <input type="radio" id="customRadio5" name="email_notifications_option" class="custom-control-input" checked value="2">
                    <label class="custom-control-label" for="customRadio5">New Discussions</label>
                </div>
                <small class="text-muted d-block mb-2">{{ "You'll receive an email notification whenever a new discussion or announcement is posted." }}</small>
                <div class="custom-control custom-radio no-left-padding">
                    <input type="radio" id="customRadio6" name="email_notifications_option" class="custom-control-input" value="3">
                    <label class="custom-control-label" for="customRadio6">Announcements Only</label>
                </div>
                <small class="text-muted d-block mb-2">{{ "You'll receive an email notification only when the group manager(s) make an announcement." }}</small>
                <div class="custom-control custom-radio no-left-padding">
                    <input type="radio" id="customRadio7" name="email_notifications_option" class="custom-control-input" value="0">
                    <label class="custom-control-label" for="customRadio7">No Email</label>
                </div>
                <small class="text-muted d-block">{{ "You will not receive any email notifications for this group." }}</small>
            </div>
            <div class="form-group mt-2">
                <button class="btn btn-outline-info btn-sm" onclick="methods.setPreference()">Save Changes</button>
            </div>
        </div>
    </div>
@stop