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
        <ul class="nav nav-tabs margin-top">
            <li @if( Request::input('p') == 'course' || !Request::has('p') ) class="active" @endif>
                <a href="?p=course">Course</a>
            </li>
            <li @if( Request::input('p') == 'shop-manuscript' ) class="active" @endif>
                <a href="?p=shop-manuscript">Shop Manuscript</a>
            </li>
            <li @if( Request::input('p') == 'pay-later' ) class="active" @endif>
                <a href="?p=pay-later">Pay Later</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade in active">

                @if( Request::input('p') != 'pay-later')
                    <ul class="nav nav-tabs margin-top">
                        <li @if( Request::input('tab') != 'archive' ) class="active" @endif>
                            <a href="?p={{ Request::input('p') }}&tab=new">{{ trans('site.new') }}</a>
                        </li>
                        <li @if( Request::input('tab') == 'archive' ) class="active" @endif>
                            <a href="?p={{ Request::input('p') }}&tab=archive">{{ trans('site.archive') }}</a>
                        </li>
                    </ul>
                @endif

                <div class="tab-content">
                    <div class="tab-pane fade in active">

                        @if( Request::input('p') == 'course' )

                            @if( Request::input('tab') != 'archive' )
                                <div class="table-users table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('site.packages', 1) }}</th>
                                            <th>{{ trans_choice('site.learners', 1) }}</th>
                                            <th>{{ trans('site.date-sold') }}</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($newCourses as $newCourseTaken)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.course.show',
                                                    $newCourseTaken->package->course_id) }}?section=packages">
                                                        {{ $newCourseTaken->package->course->title . ' - ' .
                                                        $newCourseTaken->package->variation }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.learner.show', $newCourseTaken->user->id) }}">
                                                        {{ $newCourseTaken->user->full_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $newCourseTaken->created_at }}
                                                </td>
                                                <td>
                                                    <?php
                                                    // select the template for course, if not template for course use the general.
                                                        $emailTemplate = null;
                                                        $tempData = null;
                                                        
                                                        $emailTemplate = null;
                                                        $tempData = null;
                                                        
                                                        if ($newCourseTaken->package->course->type === 'Group') {
                                                            
                                                            $tempData = \App\EmailTemplate::where('course_id', $newCourseTaken->package->course->id)->where('course_type', 'GROUP')->first();
                                                            $emailTemplate = $tempData ? $tempData : $groupCourseEmail;
                                                            // no group multiple invoice
                                                            // if($newCourseTaken->order && $newCourseTaken->order->paymentPlan->division > 1){
                                                            //     $tempData = \App\EmailTemplate::where('course_id', $newCourseTaken->package->course->id)->where('course_type', 'GROUP-MULTI-INVOICE')->first();
                                                            //     $emailTemplate = $tempData ? $tempData : $groupCourseMultiInvoiceEmail;
                                                            // }else{ //group
                                                            //     $tempData = \App\EmailTemplate::where('course_id', $newCourseTaken->package->course->id)->where('course_type', 'GROUP')->first();
                                                            //     $emailTemplate = $tempData ? $tempData : $groupCourseEmail;
                                                            // }

                                                        }else{ //Single
                                                            $tempData = \App\EmailTemplate::where('course_id', $newCourseTaken->package->course->id)->where('course_type', 'SINGLE')->first();
                                                            $emailTemplate = $tempData ? $tempData : $singleCourseEmail;
                                                        }

                                                    ?>
                                                    <button class="btn btn-success btn-xs sendEmailBtn"
                                                        data-toggle="modal"
                                                        data-target="#sendEmailModal"
                                                        data-email-template="{{ json_encode($emailTemplate) }}"
                                                        data-action="{{ route('admin.sales.send-email',
                                                        [$newCourseTaken->id, 'courses-taken-welcome']) }}">
                                                        {{ trans('site.send-email') }}
                                                    </button>
                                                    <button class="btn btn-warning btn-xs moveToArchiveBtn"
                                                        data-toggle="modal"
                                                        data-target="#moveToArchiveModal"
                                                        data-action="{{ route('admin.sales.move-to-archive', [$newCourseTaken->id]) }}">
                                                        {{ trans('site.move-to-archive') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div> <!-- end new course -->
                                <div class="pull-right">{{$newCourses->appends(request()->except('page'))}}</div>

                            @else<!-- archive -->
                                <div class="table-users table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('site.packages', 1) }}</th>
                                            <th>{{ trans_choice('site.learners', 1) }}</th>
                                            <th>{{ trans('site.date-sold') }}</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($archiveCourses as $archiveCourse)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.course.show',
                                                    $archiveCourse->package->course_id) }}?section=packages">
                                                        {{ $archiveCourse->package->course->title . ' - ' .
                                                        $archiveCourse->package->variation }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.learner.show', $archiveCourse->user->id) }}">
                                                        {{ $archiveCourse->user->full_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $archiveCourse->created_at }}
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-xs viewEmailBtn"
                                                            data-toggle="modal"
                                                            data-target="#viewEmailModal"
                                                            data-record="{{ json_encode($archiveCourse) }}"
                                                            data-type="courses-taken">
                                                        View Email
                                                    </button>

                                                    <button class="btn btn-success btn-xs sendEmailBtn"
                                                            data-toggle="modal"
                                                            data-target="#sendEmailModal"
                                                            data-email-template="{{ json_encode($followUpEmailCourseTaken) }}"
                                                            data-action="{{ route('admin.sales.send-email',
                                                    [$archiveCourse->id, 'courses-taken-follow-up']) }}">
                                                        Send following up email
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div> <!-- end new course -->
                            <div class="pull-right">{{$archiveCourses->appends(request()->except('page'))}}</div>
                            @endif

                        @elseif( Request::input('p') == 'pay-later' )
                            <div class="table-users table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>{{ trans_choice('site.packages', 1) }}</th>
                                        <th>{{ trans_choice('site.learners', 1) }}</th>
                                        <th>{{ trans('site.date-sold') }}</th>
                                        <th>Sent Invoice</th>
                                        <th>Trukket bestilling</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payLaterOrders as $order)
                                            <tr>
                                                <td>
                                                    @if (in_array($order->type, [1, 6]))
                                                        <a href="{{ route('admin.course.show', 
                                                            $order->package->course_id) }}?section=packages">
                                                            {{ $order->package->course->title . ' - ' .
                                                            $order->package->variation }}
                                                        </a>
                                                    @endif

                                                    @if (in_array($order->type, [2, 7, 9]))
                                                        {{ $order->item  }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.learner.show', $order->user->id) }}"
                                                        class="d-block">
                                                        {{ $order->user->full_name }}
                                                    </a>

                                                    {{-- <a href="{{ route('admin.sale.add-to-po', $order->id) }}" 
                                                        class="btn btn-primary btn-xs">
                                                        Add to PO
                                                    </a> --}}
                                                </td>
                                                <td>
                                                    {{ $order->created_at }}
                                                </td>
                                                <td>
                                                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                                                        class="is-invoice-sent-toggle" data-off="No"
                                                        data-id="{{$order->id}}" data-size="mini"
                                                         @if($order->is_invoice_sent) {{ 'checked' }} @endif>
                                                </td>
                                                <td>
                                                    <input type="checkbox" data-toggle="toggle" data-on="Yes"
                                                        class="is-order-withdrawn-toggle" data-off="No"
                                                        data-id="{{$order->id}}" data-size="mini"
                                                         @if($order->is_order_withdrawn) {{ 'checked' }} @endif>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="pull-right">{{$payLaterOrders->appends(request()->except('page'))}}</div>
                            </div>
                        @else <!-- shop manuscript -->
                            @if( Request::input('tab') != 'archive' )
                                <div class="table-users table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('site.manuscripts', 1) }}</th>
                                            <th>{{ trans_choice('site.learners', 1) }}</th>
                                            <th>{{ trans('site.date-sold') }}</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($newManuscriptsTaken as $newManuscriptTaken)
                                            <tr>
                                                <td>
                                                    {{ $newManuscriptTaken->shop_manuscript->title }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.learner.show', $newManuscriptTaken->user->id) }}">
                                                        {{ $newManuscriptTaken->user->full_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $newManuscriptTaken->created_at }}
                                                </td>
                                                <td>
                                                    <button class="btn btn-success btn-xs sendEmailBtn"
                                                        data-toggle="modal"
                                                        data-target="#sendEmailModal"
                                                        data-email-template="{{ json_encode($shopManuscriptEmail) }}"
                                                        data-action="{{ route('admin.sales.send-email',
                                                        [$newManuscriptTaken->id, 'shop-manuscripts-taken-welcome']) }}">
                                                        {{ trans('site.send-email') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div> <!-- end new shop-manuscript -->
                            <div class="pull-right">{{$newManuscriptsTaken->appends(request()->except('page'))}}</div>

                            @else <!-- archive -->
                                <div class="table-users table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('site.manuscripts', 1) }}</th>
                                            <th>{{ trans_choice('site.learners', 1) }}</th>
                                            <th>{{ trans('site.date-sold') }}</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($archiveManuscriptsTaken as $archiveManuscriptTaken)
                                            <tr>
                                                <td>
                                                    @if($archiveManuscriptTaken->is_active)
                                                        <a href="{{ route('shop_manuscript_taken',
                                                        ['id' => $archiveManuscriptTaken->user_id,
                                                        'shop_manuscript_taken_id' => $archiveManuscriptTaken->id]) }}">
                                                            {{$archiveManuscriptTaken->shop_manuscript->title}}
                                                        </a>
                                                    @else
                                                        {{$archiveManuscriptTaken->shop_manuscript->title}}
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.learner.show', $archiveManuscriptTaken->user->id) }}">
                                                        {{ $archiveManuscriptTaken->user->full_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $archiveManuscriptTaken->created_at }}
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-xs viewEmailBtn"
                                                            data-toggle="modal"
                                                            data-target="#viewEmailModal"
                                                            data-record="{{ json_encode($archiveManuscriptTaken) }}"
                                                            data-type="shop-manuscripts-taken">
                                                        View Email
                                                    </button>

                                                    <button class="btn btn-success btn-xs sendEmailBtn"
                                                            data-toggle="modal"
                                                            data-target="#sendEmailModal"
                                                            data-email-template="{{ json_encode($followUpEmailShopManuscript) }}"
                                                            data-action="{{ route('admin.sales.send-email',
                                                        [$archiveManuscriptTaken->id, 'shop-manuscripts-taken-follow-up']) }}">
                                                        Send following up email
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div> <!-- end new shop-manuscript -->
                                <div class="pull-right">{{$archiveManuscriptsTaken->appends(request()->except('page'))}}</div>
                            @endif
                        @endif

                    </div><!-- end new/archive tab-pane-->
                </div> <!-- end new/archive tab-content-->

            </div> <!-- end tab-pane-->
        </div> <!-- end tab-content -->
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
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script type="text/javascript">

        $(".sendEmailBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#sendEmailModal');
            let form = modal.find('form');
            let emailTemplate = $(this).data('email-template');

            form.attr('action', action);
            form.find('[name=subject]').val(emailTemplate.subject);
            form.find('[name=from_email]').val(emailTemplate.from_email);
            tinymce.get('send_email_editor').setContent(emailTemplate.email_content);
        });

        $(".moveToArchiveBtn").click(function(){
            let action = $(this).data('action');
            let modal = $('#moveToArchiveModal');
            let form = modal.find('form');

            form.attr('action', action);
        });

        $(".viewEmailBtn").click(function(){
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

        $(".is-invoice-sent-toggle").change(function(){
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

        $(".is-order-withdrawn-toggle").change(function(){
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
    </script>
@stop