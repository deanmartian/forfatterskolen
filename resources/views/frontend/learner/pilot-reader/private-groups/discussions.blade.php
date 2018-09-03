@extends('frontend.learner.pilot-reader.private-groups.layout')

@section('private-content')
    <div class="card-body font-14-body">
        <div class="clearfix">
            <h1 class="font-weight-light float-left">Group Discussion</h1>
            <!-- check if policy is open discussion -->
            @if ($manager || (!$manager && $privateGroup->policy == 1))
                <button class="btn btn-outline-info btn-sm discussion-btn pull-right mt-3" onclick="methods.showDiscussionDivForm()">New Discussion</button>
            @endif
        </div>
        <div class="collapse mt-2" id="discussionDivForm">
            <div class="card card-body">
                <p class="lead mb-3">Start New Discussion</p>
                <form id="discussionForm">
                    <div class="form-group">
                        <label for="" class="label-control">Subject</label>
                        <input type="text" name="subject" class="form-control">
                    </div>
                    @if ($manager)
                        <div class="form-group announce-checkbox-div">
                            <div class="custom-control custom-checkbox no-left-padding">
                                <input type="checkbox" name="is_announcement" class="custom-control-input" id="customCheck1">
                                <label class="custom-control-label" for="customCheck1">This is an announcement</label>
                            </div>
                            <small class="text-muted d-inline-block">Announcements are displayed on the group home page. Group members may also set their email preference to 'announcements only', in which case they will not be alerted unless you choose the 'announcement' type.</small>
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="" class="label-control">Message</label>
                        <textarea name="message" id="message_editor"></textarea>
                    </div>
                    <div class="form-group clearfix">
                        <button type="submit" class="btn btn-outline-success btn-sm pull-right">Submit</button>
                        <button type="button" class="btn btn-outline-danger btn-sm pull-right mr-1" onclick="methods.closeDiscussionDivForm()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="form-group mt-2">
            <p class="lead mb-3">Discussion List</p>
            <table id="discussion-table" class="table table-striped table-bordered" width="100%">
                <thead>
                <tr>
                    <th>Subject</th>
                    <th>Posts</th>
                    <th>Started</th>
                    <th>Last Post</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@stop
