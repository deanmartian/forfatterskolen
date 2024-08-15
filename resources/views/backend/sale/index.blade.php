@extends('backend.layout')

@section('title')
    <title>Sales &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Sales</h3>
    </div>

    <div class="col-md-12">
        <ul class="nav nav-tabs margin-top parent-nav">
            <li class="{{ Request::input('p') == 'course' || !Request::has('p') ? 'active' : '' }}">
                <a href="#" data-target="course">Course</a>
            </li>
            <li class="{{ Request::input('p') == 'shop-manuscript' ? 'active' : '' }}">
                <a href="#" data-target="shop-manuscript">Shop Manuscript</a>
            </li>
            <li class="{{ Request::input('p') == 'pay-later' ? 'active' : '' }}">
                <a href="#" data-target="pay-later">Pay Later</a>
            </li>
        </ul>

        <div id="tab-content" class="tab-content" style="position: relative; min-height: 400px">
            <!-- Dynamic content will be loaded here -->
        </div>

        <div id="loading-indicator" 
        style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <i class="fa fa-spinner fa-pulse" style="font-size: 100px"></i>
            <!-- You can replace this with a spinner or any loading animation -->
        </div>
    </div>

    <div id="sendEmailModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.send-email') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" id="sendEmailForm">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans('site.subject') }}</label>
                            <input type="text" class="form-control" name="subject" required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.from') }}</label>
                            <input type="text" class="form-control" name="from_email" required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('site.message') }}</label>
                            <textarea name="message" cols="30" rows="10" class="form-control tinymce"
                                      id="send_email_editor"></textarea>
                        </div>
                        <div class="clearfix"></div>
                        <button type="submit" class="btn btn-success pull-right margin-top">{{ trans('site.send') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- sendEmailModal -->

    <div id="viewEmailModalOrig" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">View Email</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ trans('site.subject') }}</label> <br>
                        <span class="subject-container"></span>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('site.from') }}</label> <br>
                        <span class="from-container"></span>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('site.message') }}</label> <br>
                        <span class="message-container"></span>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end viewEmailModal -->

    <div id="viewEmailModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">View Email</h4>
                </div>
                <div class="modal-body">

                    <?php
                        $dataList = [
                            [
                                'container' => 'welcome-email',
                                'title' => 'Welcome Email'
                            ],

                            [
                                'container' => 'expected-finish',
                                'title' => 'Expected Finish'
                            ],

                            [
                                'container' => 'admin-feedback',
                                'title' => 'Mail with feedback'
                            ],

                            [
                                'container' => 'follow-up',
                                'title' => 'Follow up Email'
                            ]
                        ];
                    ?>

                    @foreach($dataList as $data)
                            <div class="{{ $data['container'] }}-container">
                                <div class="panel-group" id="{{ $data['container'] }}-accordion">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#{{ $data['container'] }}-accordion"
                                                   href="#collapse-{{ $data['container'] }}" class="all-caps collapsed">
                                                    {{ $data['title'] }}
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse-{{ $data['container'] }}" class="panel-collapse collapse">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label>{{ trans('site.date-sent') }}</label> <br>
                                                    <span class="date-sent-container"></span>
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ trans('site.subject') }}</label> <br>
                                                    <span class="subject-container"></span>
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ trans('site.from') }}</label> <br>
                                                    <span class="from-container"></span>
                                                </div>

                                                <div class="form-group">
                                                    <label>{{ trans('site.message') }}</label> <br>
                                                    <span class="message-container"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div> <!-- end viewShopManuscriptEmailModal-->
    
    <div id="moveToArchiveModal" class="modal fade" role="dialog">
	    <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('site.move-to-archive') }}</h4>
                </div>
                <div class="modal-body">
                    <form method="GET" action="" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('delete') }}
                        <p>{{ trans('site.are-you-sure-you-want-to-move-this-record-to-archive') }}</p>
                        <button type="submit" class="btn btn-danger pull-right margin-top">{{ trans('site.move-to-archive') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('styles')
    <style>
        .panel-heading a:after {
            font-family: FontAwesome;
            content: "\f068";
            color: #828282;
            float: right;
        }

        .panel-heading a.collapsed:after {
            content: "\f067";
        }
    </style>
@stop

@section('scripts')
    <script type="text/javascript">

        $(document).on('click', '.sendEmailBtn', function() {
            let action = $(this).data('action');
            let modal = $('#sendEmailModal');
            let form = modal.find('form');
            let emailTemplate = $(this).data('email-template');

            form.attr('action', action);
            form.find('[name=subject]').val(emailTemplate.subject);
            form.find('[name=from_email]').val(emailTemplate.from_email);
            tinymce.get('send_email_editor').setContent(emailTemplate.email_content);
        });

        $(document).on('click', '.moveToArchiveBtn', function() {
            let action = $(this).data('action');
            let modal = $('#moveToArchiveModal');
            let form = modal.find('form');

            form.attr('action', action);
        });

        $(document).on('click', '.viewEmailBtn', function() {
            let record = $(this).data('record');
            let type = $(this).data('type');
            let modal = $('#viewEmailModal');

            let data = [
                ['received_welcome_email', 'welcome-email-container'],
                ['received_expected_finish_email', 'expected-finish-container'],
                ['received_admin_feedback_email', 'admin-feedback-container'],
                ['received_follow_up_email', 'follow-up-container'],
            ];

            $.each(data, function(k, v) {
                let email_data = record[v[0]];
                let email_container = modal.find('.'+v[1]);

                if (type === 'courses-taken') {
                    if(v[1] === 'expected-finish-container' || v[1] === 'admin-feedback-container') {
                        email_container = email_container.hide();
                    }
                }

                email_container.find('.date-sent-container').empty().append(email_data ? email_data.created_at : '');
                email_container.find('.subject-container').empty().append(email_data ? email_data.subject : '');
                email_container.find('.from-container').empty().append(email_data ? email_data.from_email : '');
                email_container.find('.message-container').empty().append(email_data ? email_data.message : '');
            })
        });

        $(document).on('change', '.is-invoice-sent-toggle', function() {
               var order_id = $(this).attr('data-id');
               var is_checked = $(this).prop('checked');
               var check_val = is_checked ? 1 : 0;
               $.ajax({
                   type:'POST',
                   url:'/sale/is-invoice-sent',
                   headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                   data: { "order_id" : order_id, 'is_invoice_sent' : check_val },
                   success: function(data){
                    console.log(data);
                   }
               });
		   });

        $(document).on('change', '.is-order-withdrawn-toggle', function() {
            var order_id = $(this).attr('data-id');
            var is_checked = $(this).prop('checked');
            var check_val = is_checked ? 1 : 0;
            $.ajax({
                type:'POST',
                url:'/sale/is-order-withdrawn',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { "order_id" : order_id, 'is_order_withdrawn' : check_val },
                success: function(data){
                console.log(data);
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.parent-nav a');
            const subTabs = document.querySelectorAll('.sub-nav a'); // Subtabs within the course tab

            tabs.forEach(tab => {
                tab.addEventListener('click', function(event) {
                    event.preventDefault();

                    const target = this.getAttribute('data-target');
                    const tabParam = new URLSearchParams(window.location.search).get('tab') || 'new';

                    // Update the URL without refreshing the page
                    const newUrl = `${window.location.pathname}?p=${target}&tab=${tabParam}`;
                    window.history.pushState({ path: newUrl }, '', newUrl);

                    // Load the content for the selected tab
                    loadTabContent(target, tabParam);

                    // Update active tab class
                    document.querySelector('.parent-nav .active').classList.remove('active');
                    this.parentNode.classList.add('active');
                });
            });

            subTabs.forEach(subTab => {
                subTab.addEventListener('click', function(event) {
                    event.preventDefault();

                    const subTabTarget = this.getAttribute('data-tab');
                    const mainTab = new URLSearchParams(window.location.search).get('p') || 'course';

                    // Update the URL without refreshing the page
                    const newUrl = `${window.location.pathname}?p=${mainTab}&tab=${subTabTarget}`;
                    window.history.pushState({ path: newUrl }, '', newUrl);

                    // Load the content for the selected tab
                    loadTabContent(mainTab, subTabTarget);

                    // Update active sub-tab class
                    document.querySelector('.sub-nav .active').classList.remove('active');
                    this.parentNode.classList.add('active');
                });
            });

            function handlePaginationLinks() {
                const paginationLinks = document.querySelectorAll('#tab-content .pagination a');

                paginationLinks.forEach(link => {
                    link.addEventListener('click', function(event) {
                        event.preventDefault();

                        const urlParams = new URLSearchParams(this.search);
                        const targetTab = urlParams.get('p') || 'course';
                        const subTab = urlParams.get('tab') || 'new';
                        const page = urlParams.get('page') || 1;

                        // Update the URL with pagination
                        const newUrl = `${window.location.pathname}?p=${targetTab}&tab=${subTab}&page=${page}`;
                        window.history.pushState({ path: newUrl }, '', newUrl);

                        // Load the content for the selected tab and page
                        loadTabContent(targetTab, subTab, page);
                    });
                });
            }

            // Modified loadTabContent function to include pagination handling
            function loadTabContent(tab, subTab, page = 1) {
                // Show loading indicator
                $('#tab-content').empty();
                document.getElementById('loading-indicator').style.display = 'block';

                $.ajax({
                    url: `/sale/load-tab-content?p=${tab}&tab=${subTab}&page=${page}`, // Replace with your actual endpoint
                    method: 'GET',
                    success: function(data) {
                        // Populate the tab-content div with the loaded data
                        $('#tab-content').html(data);
                        
                        // Re-bind pagination click events
                        handlePaginationLinks();
                    },
                    error: function() {
                        $('#tab-content').html('<p>An error occurred while loading the content.</p>');
                    },
                    complete: function() {
                        // Hide loading indicator
                        document.getElementById('loading-indicator').style.display = 'none';
                    }
                });
            }

            // Initial load
            const initialTab = new URLSearchParams(window.location.search).get('p') || 'course';
            const initialSubTab = new URLSearchParams(window.location.search).get('tab') || 'new';
            const initialPage = new URLSearchParams(window.location.search).get('page') || 1;
            loadTabContent(initialTab, initialSubTab, initialPage);
    });
    </script>
@stop