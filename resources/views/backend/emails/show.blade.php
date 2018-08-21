@extends('backend.layout')

@section('title')
    <title>Email</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3>
            <i class="fa fa-envelope"></i> {{str_replace('_',' ',mb_decode_mimeheader($email['header']->subject))}}
            <button class="btn btn-success btn-xs" data-toggle="modal" data-target="#replyToEmail"><i class="fa fa-reply"></i></button>
        </h3>
        <div class="clearfix"></div>
    </div>
    <br>
    <div class="col-sm-8 col-sm-offset-2">

        @if($flash = session('message.content'))
            <div class="alert alert-{{ session('message.level') }}" role="alert" id="fixed_to_bottom_alert">
                {{ $flash }}
            </div>
        @endif

        <div class="panel panel-default">
            <div class="padding_10">
            <?php print_r($email['readable_body']);?>
            </div>
        </div>
    </div>

    <div id="replyToEmail" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Reply to email</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.email.reply') }}" id="sendReplyForm" novalidate>
                        <?php echo e(csrf_field()); ?>

                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="email_content" cols="30" rows="10" class="form-control" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary pull-right" id="sendReplyBtn">Send</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@stop

@section('scripts')
    <script src="http://tinymce.cachefly.net/4.0/tinymce.min.js"></script>
    <script type="text/javascript">
        $(function() {

            tinymce.init({
                selector:'textarea',
                height : "300",
                menubar: false,
                toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
            });

            setTimeout(function () {
                $(".alert").fadeOut();
            }, 3000);

            $("#sendReplyBtn").on('click',function(){
               $(this).text('Please wait...').attr('disabled', true);
               $("#sendReplyForm").submit();
            });
        });
    </script>
@stop