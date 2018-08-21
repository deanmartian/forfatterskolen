@extends('frontend.layout')

@section('title')
    <title>Private Groups &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
@stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content white-background">
            <div class="col-sm-12">

                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">
                    <div class="page-main">
                        <h1>Private Groups</h1>

                        <p>
                            Groups make it easy to share multiple books with a set of readers, and offer an easy way for
                            authors to engage with the group. You can use the group to send announcements to readers,
                            and optionally open up the group for discussion, message-board style.
                        </p>

                        <p class="pb-1">
                            <strong>Like everything on BetaBooks, groups are private so only the group members will have
                                access.</strong> If you have an invitation to join a group it will be listed on your
                            <a href="{{ route('learner.book-author') }}">dashboard</a>.
                        </p>

                        <hr class="margin-top-16 dotted-hr">
                    </div>

                    <div class="col-sm-12 margin-top no-left-padding">
                        <h2 class="no-margin-top group-label"> Your Groups </h2>

                        @if (!$members->count())
                            <p class="margin-top" id="no-group-prompt">You aren't a member of any groups.</p>
                        @endif

                        <div class="form-group margin-top {{ !$members->count() ? 'display-none' : '' }}" id="group-list-container">
                            <div class="global-card with-border">
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @foreach($members as $member)
                                            <li class="list-group-item clearfix">
                                                <div class="form-group mb-0 clearfix">
                                                    <span class="text-muted float-left font-14">
                                                        Member Since:
                                                        {{ \Carbon\Carbon::parse($member->created_at)->format('M d') }}
                                                    </span>

                                                    <div class="ml-2 message-content mt-3">
                                                        <p class="mb-0">
                                                            <a href="{{ route('learner.private-groups.show', $member->private_group->id) }}">
                                                                {{ $member->private_group->name }}
                                                            </a>
                                                            <span class="badge badge-info color1 py-2">
                                                                {{ $member->role }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <p>
                            <a href="#createPrivateGroupModal" class="beta-button color color1" data-toggle="modal">
                                <i class="fa fa-plus right-space"></i>Create a New Group</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    @include('frontend.learner.pilot-reader.private-groups.modal.create_groups')
@stop

@section('scripts')
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('js/showdown/dist/showdown.min.js') }}"></script>
    <script src="{{ asset('/js/pilot-reader/private-groups/index.js') }}"></script>
@stop