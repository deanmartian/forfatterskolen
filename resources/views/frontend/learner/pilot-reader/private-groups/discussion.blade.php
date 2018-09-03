@extends('frontend.layout')

@section('title')
    <title>{{ $page_title }} &rsaquo; Forfatterskolen</title>
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

                    <div class="global-card mt-3">
                        <a href="{{ route('learner.private-groups.discussion', $privateGroup->id) }}"
                        class="pl-4 fa fa-long-arrow-left"> All Discussions</a>

                        <div class="card-body">
                            <div class="clearfix">
                                <span class="font-weight-light pull-left detail" data-id="subject"
                                style="font-size: 36px;">
                                    {{ $discussion->subject }}
                                </span>
                                @if ($discussion->is_announcement)
                                <h3 class="pull-right mt-3">
                                    <span class="badge badge-success badge-border-radius py-2 font-24" data-id="is_announcement">
                                        Announcement
                                    </span>
                                </h3>
                                @endif
                            </div>
                            <div class="clearfix font-weight-light text-muted">
                                <span class="detail pull-left" data-id="owner">
                                    {{ $discussion->user->full_name }}
                                </span>
                                <span class="detail pull-right font-14-body" data-id="created_at">
                                    {{ \Carbon\Carbon::parse($discussion->created_at)->format('M d, h:i A') }}
                                </span>
                            </div>
                            <div class="form-group mt-2 clearfix">
                                <div class="lead detail mb-0" data-id="message" id="message">
                                    {!! $discussion->message !!}
                                </div>
                                @if ($discussion->user->id == Auth::user()->id)
                                    <button class="btn btn-outline-info btn-sm pull-right" onclick="methods.inlineEdit(this)">Edit</button>
                                @endif
                            </div>
                            <div class="form-group mt-2 display-none">
                                <textarea name="message" id="message_editor"></textarea>
                                <div class="form-group clearfix mt-2">
                                    <button class="btn btn-primary btn-sm pull-right" onclick="methods.inlineSave(this)">Save</button>
                                    <button class="btn btn-danger btn-sm pull-right mr-1" onclick="methods.inlineCancel(this)">Cancel</button>
                                </div>
                            </div>
                            <hr/>
                            <ul class="list-group list-group-flush no-border-top-bottom font-14-body" id="discussion-replies-ul">

                            </ul>
                            <!-- check if policy is not announcements only -->
                            @if ($manager || (!$manager && $privateGroup->policy !== 3))
                                <div class="card card-body mt-2">
                                    <div class="form-group clearfix mb-0 pb-0">
                                        <span class="d-inline-block mt-1">Have something to add?</span> <button class="btn btn-outline-primary btn-sm pull-right" onclick="methods.addForm(this)">Post a reply</button>
                                    </div>
                                    <div class="form-group add display-none mb-0 pb-0">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div> <!-- end col-md-8 col-sm-offset-2 col-sm-12 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content white-background -->

        <div class="clearfix"></div>
    </div>

    <input type="hidden" name="group_id" value="{{ $privateGroup->id }}">
@stop

@section('scripts')
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
    <script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js" integrity="sha384-BpuqJd0Xizmp9PSp/NTwb/RSBCHK+rVdGWTrwcepj1ADQjNYPWT2GDfnfAr6/5dn" crossorigin="anonymous"></script>
    <script src="{{ asset('js/showdown/dist/showdown.min.js') }}"></script>
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
    <script>
        let autogrow_link = '{{ asset('js/autogrow/plugin.js') }}';
        let discussion_id = '{{ $discussion->id }}';
    </script>
    <script src="{{ asset('/js/pilot-reader/private-groups/discussion.js') }}"></script>
@stop