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
        @include('backend.partials.navbar')
        @yield('content')
        <div id="changePasswordModal" class="modal fade" role="dialog" data-backdrop="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Change Password</h4>
                    </div>
                    <div class="modal-body">
                        <form id="form-change-password" role="form" method="POST" action="{{ route('backend.change-password') }}" novalidate class="form-horizontal">
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
        @if (!in_array(Route::currentRouteName(),['backend.dashboard', 'admin.course.show', 'admin.learner.show',
             'admin.email-template.index', 'admin.free-manuscript.index']))
            <script src="https://cdn.tiny.cloud/1/at9h19nmgr4aahzi0km0mkwkejc37a686yxis2zks33o0p6n/tinymce/5/tinymce.min.js"
                referrerpolicy="origin"></script>
        @endif
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

            const currentRoute = "{{ Route::currentRouteName() }}";

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

                    let cmsURL = tiny_editor_config.path_absolute + '/filemanager?editor=tinymce5';
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

            function initTinyMCE() {
                tinymce.init(tiny_editor_config);
            }

            if (!['backend.dashboard', 'admin.course.show', 'admin.learner.show', 'admin.email-template.index', 'admin.free-manuscript.index'].includes(currentRoute)) {
                initTinyMCE();
            } else {
                document.querySelectorAll('.loadScriptButton').forEach(button => {
                    button.addEventListener('click', function() {
                        if (typeof tinymce === 'undefined') {
                            var script = document.createElement('script');
                            script.src = "https://cdn.tiny.cloud/1/at9h19nmgr4aahzi0km0mkwkejc37a686yxis2zks33o0p6n/tinymce/5/tinymce.min.js";
                            script.referrerPolicy = "origin";
                            script.onload = initTinyMCE;
                            document.body.appendChild(script);
                        } else {
                            initTinyMCE();
                        }
                    });
                });
            }

            function setEditorContent(editorId, content) {
                if (typeof tinymce !== 'undefined') {
                    tinymce.get(editorId).setContent(content);
                } else {
                    console.error('TinyMCE is not defined');
                }
            }


            function disableSubmit(t) {
                let submit_btn = $(t).find('[type=submit]');
                submit_btn.text('');
                submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
                submit_btn.attr('disabled', 'disabled');
            }

            function checkFormAction(t) {
                let btn = $(t);
                btn.text('');
                btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
                btn.attr('disabled','disabled');
                let form = $(t).closest('form');

                if (form.attr('action') === '') {
                    alert("Form don't have any action. Please refresh the page.");
                    return;
                }

                form.submit();
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
