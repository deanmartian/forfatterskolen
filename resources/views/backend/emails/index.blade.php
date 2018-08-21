@extends('backend.layout')

@section('title')
    <title>Email</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-envelope"></i> Email</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">

        @if($flash = session('message.content'))
            <div class="alert alert-{{ session('message.level') }}" role="alert" id="fixed_to_bottom_alert">
                {{ $flash }}
            </div>
        @endif

        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Subject</th>
                    <th>From</th>
                    <th>Date</th>
                    <th>Size</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($emails as $inbox)
                       <tr>
                           <td>
                               <a href="{{ route('admin.email.show', trim($inbox['header']->Msgno)) }}">
                                   {{ str_replace('_',' ',mb_decode_mimeheader($inbox['header']->Subject)) }}
                               </a>
                           </td>
                           <td>
                               {{ $inbox['header']->from[0]->personal }}
                           </td>
                           <td>
                               {{ date('M d, Y H:i', $inbox['header']->udate) }}
                           </td>
                           <td>
                               {{ \App\Http\AdminHelpers::formatBytes($inbox['header']->Size) }}
                           </td>
                           <td>
                               <button type="button" data-toggle="modal" data-target="#forwardEmailModal" class="btn btn-success btn-xs forwardEmailBtn" data-action="{{ route('admin.email.forward',trim($inbox['header']->Msgno)) }}"><i class="fa fa-mail-forward"></i></button>
                               <button type="button" data-toggle="modal" data-target="#moveEmailModal" class="btn btn-info btn-xs moveEmailBtn" data-action="{{ route('admin.email.move',trim($inbox['header']->Msgno)) }}"><i class="fa fa-arrows"></i></button>
                               <button type="button" data-toggle="modal" data-target="#deleteEmailModal" class="btn btn-danger btn-xs deleteEmailBtn" data-action="{{ route('admin.email.delete',trim($inbox['header']->Msgno)) }}"><i class="fa fa-trash"></i></button>
                           </td>
                       </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="moveEmailModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Move Email</h4>
                </div>
                <div class="modal-body">
                    <label> Are you sure you want to move this email?</label>

                    <div class="text-right">
                        <a href="" class="btn btn-primary" id="move_email_btn">Move</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="deleteEmailModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Email</h4>
                </div>
                <div class="modal-body">
                    <label> Are you sure you want to delete this email?</label>

                    <div class="text-right">
                        <a href="" class="btn btn-danger" id="delete_email_btn">Delete</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="forwardEmailModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Forward Email</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" id="forwardForm">
                        <?php echo e(csrf_field()); ?>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="to_email" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="email_content" cols="30" rows="10" class="form-control" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary pull-right" id="forwardBtn">Send</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@stop

@section('scripts')
    <script>

        $(function(){
           setTimeout(function(){
               $(".alert").fadeOut();
           },3000);

           $(".moveEmailBtn").click(function(){
              var link = $(this).data('action');
              $("#move_email_btn").attr('href', link);
           });

            $(".deleteEmailBtn").click(function(){
                var link = $(this).data('action');
                $("#delete_email_btn").attr('href', link);
            });

            $("#move_email_btn").on('click',function(){
                $(this).attr('disabled', true);
                $(this).text('Moving...');
            });

            $("#delete_email_btn").on('click',function(){
               $(this).attr('disabled', true);
               $(this).text('Deleting...');
            });

            $(".forwardEmailBtn").click(function(){
                var link = $(this).data('action');
                $("#forwardForm").attr('action', link);
            });
        });
    </script>
@stop