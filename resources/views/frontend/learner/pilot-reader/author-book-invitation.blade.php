@extends('frontend.layout')

@section('title')
    <title>{{ $book->title }} Invitation &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css"
          integrity="sha384-yBEPaZw444dClEfen526Q6x4nwuzGO6PreKpbRVSLFCci3oYGE5DnD1pNsubCxYW" crossorigin="anonymous">

    <style>
        .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px !important; }
        .toggle.ios .toggle-handle { border-radius: 20px !important; }
    </style>
@stop

@section('heading') Invitations & Access Management @stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content book-invitation global">
            <div class="col-sm-12">
                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">

                @include('frontend.learner.pilot-reader.partials.nav')

                    <header class="page-main">
                        <h1>@yield('heading')</h1>
                        <hr>
                    </header>

                    <div class="reader-manager">
                        <h2>Manage Invitations</h2>

                        <div class="invitation-manager mb-3">
                            <div class="invitation-sender">
                                <h3 class="group-label">Invitation Link</h3>

                                <div class="form-group margin-top">
                                    <input type="checkbox" data-toggle="toggle" data-on="Link Enabled"
                                           class="link-toggle" data-off="Link Disabled" data-style="ios"
                                           onchange="settings.getLink('toggle', this)"
                                    @if($invitation_link_enabled)checked @endif>
                                </div>

                                <div class="form-group mt-1 @if(!$invitation_link_enabled)display-none @endif" id="shareable_link_div">
                                    <div class="label-control font-italic mb-1">Your shareable link</div>
                                    <div class="input-group-global mb-3">
                                        <input type="text" class="form-control bg-light" readonly=""
                                        value="@if($invitation_link_enabled) {{url("book/invitation/$invitation_link->link_token")}} @endif">
                                        <div class="input-group-append">
                                            <button class="btn btn-info" type="button" onclick="settings.copyToClipboard(this)"><i class="fa fa-clipboard"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="invitation-manager">
                            <div class="invitation-sender">
                                <h3 class="group-label">Send Invitations</h3>
                                <p class="hint sans margin-top-16">
                                    Invite readers by email address.
                                </p>
                                <div class="user-picker">
                                    <div class="picked-users"></div>
                                    <div class="input-group autocomplete">
                                        <div class="control-group inline-submit">
                                            <input type="text" class="search" name="search">
                                            <button disabled="disabled" class="color disabled add-email">
                                                <i class="fa fa-user-plus right-space"></i>
                                                <span>Add</span>
                                            </button>
                                        </div> <!-- end control-group inline-submit -->

                                        <div class="prompt keep-space"></div>
                                    </div>
                                </div> <!-- end user-picker -->

                                <div class="row personalize">
                                    <label class="small check sans">
                                        <input type="checkbox" class="right-space">Add a personal message?
                                    </label>
                                </div>

                                <div class="row margin-top-16">
                                    <button href="#" disabled="disabled"
                                            class="send-button color success right-space beta-button">
                                        <i class="fa fa-spinner fa-pulse fa-fw hidden"></i>
                                        <i class="fa fa-envelope"></i>
                                        <span>Send</span>
                                    </button>
                                </div> <!-- end row -->

                            </div> <!-- end invitation-sender -->

                            <div class="margin-top">
                                <nav>
                                    <ul class="nav nav-tabs">
                                        <li>
                                            <a href="#nav-pending" data-toggle="tab" onclick="settings.listInvitations(0, 0)">Pending</a>
                                        </li>
                                        <li>
                                            <a href="#nav-decline" data-toggle="tab" onclick="settings.listInvitations(1, 2)">Decline</a>
                                        </li>
                                        <li>
                                            <a href="#nav-readers" data-toggle="tab" onclick="settings.listInvitations(2, 1)">Readers</a>
                                        </li>
                                        @if (\App\Http\FrontendHelpers::countReaderWithStatus($book->id, 1))
                                            <li>
                                                <a href="#nav-finished" data-toggle="tab" onclick="settings.listInvitations(3, 3)">Finished</a>
                                            </li>
                                        @endif

                                        @if (\App\Http\FrontendHelpers::countReaderWithStatus($book->id, 2))
                                            <li>
                                                <a href="#nav-quitted" data-toggle="tab" onclick="settings.listInvitations(4, 4)">Quitted</a>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                                <div class="tab-content margin-top" id="nav-tabContent">
                                    <div class="tab-pane fade" id="nav-pending" role="tabpanel" aria-labelledby="nav-pending-tab">
                                        <table id="pending_table" class="table table-striped table-bordered" width="100%">
                                            <thead>
                                            <tr>
                                                <th>Reader</th>
                                                <th>Sent At</th>
                                                <th style="width:20%">Send Count</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="nav-decline" role="tabpanel" aria-labelledby="nav-decline-tab">
                                        <table id="decline_table" class="table table-striped table-bordered" width="100%">
                                            <thead>
                                            <tr>
                                                <th>Reader</th>
                                                <th>Decline At</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="nav-readers" role="tabpanel" aria-labelledby="nav-readers-tab">
                                        <table id="readers_table" class="table table-striped table-bordered" width="100%">
                                            <thead>
                                            <tr>
                                                <th>Reader</th>
                                                <th class="role">Role</th>
                                                <th>Started At</th>
                                                <th>Removed At</th>
                                                <th class="action">Action</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>

                                    <div class="tab-pane fade" id="nav-finished" role="tabpanel" aria-labelledby="nav-readers-tab">
                                        <table id="finished_table" class="table table-striped table-bordered" width="100%">
                                            <thead>
                                            <tr>
                                                <th>Reader</th>
                                                <th class="role">Role</th>
                                                <th>Finished At</th>
                                                <th class="action">Action</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>

                                    <div class="tab-pane fade" id="nav-quitted" role="tabpanel" aria-labelledby="nav-readers-tab">
                                        <table id="quitted_table" class="table table-striped table-bordered" width="100%">
                                            <thead>
                                            <tr>
                                                <th>Reader</th>
                                                <th>Quit At</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{--<div class="pending-invitations">
                                <h3 class="group-label">Pending Invitations&nbsp;
                                    <span class="dark-text">1</span>
                                </h3>

                                <table class="action-table margin-top">
                                    <thead>
                                    <tr>
                                        <th>Reader</th>
                                        <th>Sent At</th>
                                        <th>Send Count</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td class="reader">
                                            <div>ely butabara</div>
                                            <div class="email">
                                                elybutabara@gmail.com
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div> --}}<!-- end pending invitations -->
                        </div> <!-- end invitation-manager -->
                    </div> <!-- end reader-manager -->
                </div> <!-- end col-md-8 col-sm-offset-2 col-sm-12 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content book-invitation -->

        <div class="clearfix"></div>
    </div>
    <div id="cancelModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Cancel Invitation</h4>
                </div>
                <div class="modal-body">
                   <p>Are you sure you want to cancel it?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" id="cancelBtn">Ok</button>
                    <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div id="removeModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" id="removeBtn">Ok</button>
                    <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    @include('frontend.learner.pilot-reader.modals.quitted_reason')

    <input type="hidden" name="book_id" value="{{ $book->id }}">
@stop

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"
            integrity="sha384-cd07Jx5KAMCf7qM+DveFKIzHXeCSYUrai+VWCPIXbYL7JraHMFL/IXaCKbLtsxyB" crossorigin="anonymous"></script>
    <script src="{{ asset('js/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
    <script>
        let send_invitation_link = '{{ route('learner.book-author-book-invitation-send', $book->id) }}';
        let validate_email_link = '{{ route('learner.book-author-book-invitation-validate-email', $book->id) }}';
        let emailList = [];
        let isMyBooks = parseInt('{{ $book->user_id == Auth::user()->id ? 1 : 0 }}');
        const methods = {

            validateInput: function(el, e) {
                let self            = this;
                let input_val       = $(el).val();
                let add_email_res   = 0;
                if (input_val.length >= 3) {
                    let prompt = $(".prompt");
                    let message = '';
                    prompt.empty();

                    if (self.validateEmail(input_val)) {
                        message = '<i class="fa fa-check-circle text-color success"></i>';
                        message += '<span style="margin-left: 5px">Invite <em>'+input_val+'</em> (hit enter)</span>';
                        $(el).next('button').removeAttr('disabled').removeClass('disabled');

                        if (e.keyCode === 13) {
                            add_email_res = self.addEmail(input_val);
                        }

                    } else {
                        message = '<span>Enter an email address to invite a new contact (one email address at a time).</span>';
                        $(el).next('button').attr('disabled', true).addClass('disabled');
                    }

                    prompt.append(message);
                    // this is to remove the content of prompt class
                    if (add_email_res) {
                        prompt.empty();
                    }
                }
            },

            validateEmail: function(email) {
                let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(email).toLowerCase());
            },

            toggleMessage: function(el) {
                if ($(el).prop('checked')) {
                    let new_row = '';
                        new_row += '<div class="row margin-top-16">';
                            new_row += '<textarea placeholder="Enter your custom message here" class="small"></textarea>';
                            new_row += '<div class="hint sans margin-top">If you enter a message here, it will be added to the email invitation that your reader receives.</div>';
                        new_row += '</div>';
                    $(".personalize").after(new_row);
                } else {
                    $(".personalize").next('.row').remove();
                }
            },

            addEmail: function(email) {
                let book_id = $("[name=book_id]").val();
                let test = $.post(validate_email_link, { book_id, email})
                    .then( function(response){

                        // check if the email passed is not yet on the list to avoid duplicate
                        if ($.inArray(email, emailList) === -1) {
                            emailList.push(email);

                            let new_user = '<div class="user">';
                            new_user += email;
                            new_user += '<i class="fa fa-close" onclick="methods.removeEmail(this, \''+email+'\')"></i>';
                            new_user += '</div>';

                            $(".picked-users").append(new_user);
                            $(".send-button").removeAttr('disabled');

                        }
                        $("[name=search]").val('');
                        $(".prompt").empty();

                    })
                    .catch( function(err){
                        const error = err.responseJSON;
                        $(".prompt").empty();
                        $(".prompt").append(`<small class='text-danger'><i class='fa fa-exclamation-circle'></i>${error.email}</small>`);
                    });

            },

            removeEmail: function(el, email) {
                $(el).parent('.user').remove();

                emailList.splice( $.inArray(email,emailList) ,1 ); // remove the email from the list

                if ($(".picked-users").find(".user").length === 0) {
                    $(".send-button").attr('disabled', true);
                }
            },

            sendInvitation: function(el) {
                let message = $("textarea").length ? $("textarea").val() : null;

                let data = { emails: emailList, message: message};
                let sendBtn = $(".send-button");
                sendBtn.find(".fa-spinner").removeClass('hidden');
                sendBtn.attr('disabled', true);

                $.post(send_invitation_link, data)
                    .then(function(response){
                        emailList = [];
                        /*let message = '<span class="text-color-success sans small" style="display: inline-block; font-size: 14px">';
                            message += '<i class="fa fa-check" style="margin-right: 5px"></i>';
                            message += response.success;
                            message += '</span>';
                        $(el).attr('disabled', true)
                            .after(message);*/
                        $(".picked-users").empty();
                        $(".add-email").attr('disabled', true).addClass('disabled');
                        settings.listInvitations(0, 0);
                        toastr.success(response.success, "Success");
                        /*setTimeout(function () {
                            $(el).next('span').remove();
                        }, 3000);*/
                        $("textarea").val('');
                        $(".fa-spinner").addClass('hidden');
                    });
            }

        };

        $("[name=search]").keyup(function(e){
           methods.validateInput(this, e);
        });

        $("[type=checkbox]").click(function(){
            methods.toggleMessage(this);
        });

        $(".send-button").click(function(){
           methods.sendInvitation(this);
        });

        $(".add-email").click(function(){
           let email = $("[name=search]").val();
           methods.addEmail(email);
            $(".prompt").empty();
        });

        toastr.options.preventDuplicates = true;
        toastr.options.timeOut = 2000;

    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.10/lodash.min.js"></script>
    <script src="http://minifiedjs.com/download/minified-legacyie-src.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('js/pilot-reader/invitation.js') }}"></script>
@stop