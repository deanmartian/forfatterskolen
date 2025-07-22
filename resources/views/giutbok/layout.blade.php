<!DOCTYPE html>
<html lang="en">
    <head>
        @yield('title')
        @include('backend.partials.backend-css')
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0 maximum-scale=1.0, user-scalable=no">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" />
        @yield('styles')
        <meta name="csrf-token" content="{{ csrf_token() }}" />
    </head>
    <body>
        @include('giutbok.partials.navbar')
        @yield('content')
        <div id="changePasswordModal" class="modal fade" role="dialog" data-backdrop="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Change Password</h4>
                    </div>
                    <div class="modal-body">
                        <form id="form-change-password" role="form" method="POST" action="{{ route('giutbok.change-password') }}" novalidate class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="col-md-9">
                                <label for="current-password" class="col-sm-4 control-label">Current Password</label>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="current-password" name="current-password" placeholder="Password">
                                    </div>
                                </div>
                                <label for="password" class="col-sm-4 control-label">New Password</label>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                    </div>
                                </div>
                                <label for="password_confirmation" class="col-sm-4 control-label">Re-enter Password</label>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Re-enter Password">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-5 col-sm-6">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        @if($errors->count())
            <?php
                $alert_type = session('alert_type');
                if(!Session::has('alert_type')) {
                    $alert_type = 'danger';
                }
            ?>
            <div class="alert alert-{{ $alert_type }} global-alert-box" style="z-index: 9">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('backend.partials.scripts')
        <script src="https://Forfatterskolen.cdn.vooplayer.com/assets/vooplayer.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
        {{-- <script src="https://cdn.tiny.cloud/1/58zy6vmujertl448ngd6tq81e40p4een1t8y9lnkq9licymu/tinymce/5/tinymce.min.js"
                referrerpolicy="origin"></script> --}}
        <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>
        <script>
            $(".dt-table").DataTable({
                "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                pageLength: 10,
                "aaSorting": []
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // tinymce load editor
            let tiny_editor_config = {
                path_absolute: "{{ URL::to('/') }}",
                height: '500',
                selector: '.tinymce',
                plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table directionality',
                    'emoticons template paste textpattern'],
                toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript ' +
                'superscript | forecolor backcolor | link | alignleft aligncenter alignright ' +
                'alignjustify  | removeformat',
                toolbar2: 'undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media code ' +
                '| print fullscreen',
                relative_urls: false,
                file_picker_callback : function(callback, value, meta) {
                    let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                    let y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

                    let cmsURL = tiny_editor_config.path_absolute + '/laravel-filemanager?editor=tinymce5';
                    if (meta.filetype == 'image') {
                        cmsURL = cmsURL + "&type=Images";
                    } else {
                        cmsURL = cmsURL + "&type=Files";
                    }

                    tinyMCE.activeEditor.windowManager.openUrl({
                        url : cmsURL,
                        title : 'Filemanager',
                        width : x * 0.8,
                        height : y * 0.8,
                        resizable : "yes",
                        close_previous : "no",
                        onMessage: (api, message) => {
                            callback(message.content);
                        }
                    });
                }
            };
            tinymce.init(tiny_editor_config);

            function disableSubmit(t) {
                let submit_btn = $(t).find('[type=submit]');
                submit_btn.text('');
                submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
                submit_btn.attr('disabled', 'disabled');
            }

            function enableSelect(parent) {
                let parentContainer = $("#" + parent);
                parentContainer.find('.select2').show();
                parentContainer.find('.hidden-container').hide();
            }
        </script>
        @yield('scripts')
    </body>
</html>
