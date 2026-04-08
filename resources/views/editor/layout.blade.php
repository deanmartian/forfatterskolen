<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        @yield('title')
        @include('backend.partials.backend-css')
        <link rel="manifest" href="{{ asset('manifest-editor.json') }}">
        <meta name="theme-color" content="#862736">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <link rel="apple-touch-icon" href="/icons/icon-192.png">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" />
        <link rel="stylesheet" href="{{ asset('css/editor-v2.css') }}">
        @yield('styles')
        <meta name="csrf-token" content="{{ csrf_token() }}" />
    </head>
    <body>
        <div class="ed-shell">
            {{-- Sidebar --}}
            @include('editor.partials.sidebar')

            {{-- Main Area --}}
            <main class="ed-main">
                {{-- Top Header --}}
                <header class="ed-header">
                    <h1 class="ed-header__title">@yield('page-title', 'Dashboard')</h1>
                    <div class="ed-header__actions">
                        <div class="ed-search">
                            <i class="fa fa-search"></i>
                            <input type="text" placeholder="{{ trans('site.search-learner-id') }}...">
                        </div>
                        <button class="ed-notif-btn">
                            <i class="fa fa-bell-o"></i>
                            <span class="ed-notif-btn__dot"></span>
                        </button>
                    </div>
                </header>

                {{-- Page Content --}}
                <div class="ed-content">
                    @yield('content')
                    <div class="ed-footer">
                        Forfatterskolen Redaktørportal
                    </div>
                </div>
            </main>
        </div>

        {{-- Change Password Modal --}}
        <div id="changePasswordModal" class="modal fade" role="dialog" data-backdrop="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">{{ trans('site.change-password') }}</h4>
                    </div>
                    <div class="modal-body">
                        <form id="form-change-password" role="form" method="POST" action="{{ route('editor.change-password') }}" novalidate class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="current-password">Gjeldende passord</label>
                                <input type="password" class="form-control" id="current-password" name="current-password" placeholder="Passord">
                            </div>
                            <div class="form-group">
                                <label for="password">Nytt passord</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Passord">
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Gjenta passord</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Gjenta passord">
                            </div>
                            <button type="submit" class="ed-btn ed-btn--primary">Send inn</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Global Alerts --}}
        @if($errors->count())
            @php
                $alert_type = session('alert_type', 'danger');
            @endphp
            <div class="alert alert-{{ $alert_type }}" style="position:fixed; bottom:20px; right:20px; z-index:9999; max-width:400px; box-shadow: 0 8px 24px rgba(0,0,0,0.12);">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <ul style="margin:0; padding-left:18px;">
                    @foreach($errors->all() as $error)
                        <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Scripts --}}
        <script src="{{ mix('/js/app.js') }}"></script>
        @include('backend.partials.scripts')
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
        <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
        <script>
            // DataTables init
            $(".dt-table").DataTable({
                "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Alle"]],
                pageLength: 10,
                "aaSorting": [],
                "language": {
                    "search": "",
                    "searchPlaceholder": "Søk i tabell...",
                    "lengthMenu": "Vis _MENU_ rader",
                    "info": "Viser _START_ til _END_ av _TOTAL_ rader",
                    "infoEmpty": "Ingen rader å vise",
                    "infoFiltered": "(filtrert fra _MAX_ rader totalt)",
                    "zeroRecords": "Ingen treff",
                    "emptyTable": "Ingen data",
                    "paginate": { "previous": "Forrige", "next": "Neste" }
                }
            });

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            // TinyMCE config
            let tiny_editor_config = {
                path_absolute: "{{ URL::to('/') }}",
                height: '500',
                selector: '.tinymce',
                license_key: 'gpl',
                plugins: 'advlist autolink lists link image charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking save table directionality',
                toolbar1: 'blocks fontfamily fontSize | bold italic underline strikethrough subscript superscript | forecolor backcolor | alignleft aligncenter alignright alignjustify | removeformat',
                toolbar2: 'undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media code | print fullscreen',
                relative_urls: false,
                extended_valid_elements: 'iframe[src|width|height|frameborder|allowfullscreen]',
                media_live_embeds: true,
                images_upload_handler: function (blobInfo, progress) {
                    return new Promise((resolve, reject) => {
                        const xhr = new XMLHttpRequest();
                        xhr.withCredentials = false;
                        xhr.open('POST', '/tinymce-upload');
                        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        xhr.upload.onprogress = (e) => { progress(e.loaded / e.total * 100); };
                        xhr.onload = () => {
                            if (xhr.status < 200 || xhr.status >= 300) { reject('HTTP Error: ' + xhr.status); return; }
                            const json = JSON.parse(xhr.responseText);
                            if (!json || typeof json.location != 'string') { reject('Invalid JSON: ' + xhr.responseText); return; }
                            resolve(json.location);
                        };
                        xhr.onerror = () => { reject('Upload failed. Code: ' + xhr.status); };
                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
                        xhr.send(formData);
                    });
                },
            };
            tinymce.init(tiny_editor_config);

            function disableSubmit(t) {
                let btn = $(t).find('[type=submit]');
                btn.html('<i class="fa fa-spinner fa-pulse"></i> Vennligst vent...');
                btn.attr('disabled', 'disabled');
            }

            // Mobile sidebar toggle
            $(document).on('click', '.ed-sidebar-toggle', function() {
                $('#edSidebar').toggleClass('open');
            });
            // Registrer service worker for PWA.
            // updateViaCache: 'none' — tvinger browser til å sjekke nettverket
            // for ny SW-fil hver gang, i stedet for å bruke HTTP-cache.
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/service-worker.js', { updateViaCache: 'none' })
                    .then(function(reg) {
                        console.log('SW registered', reg.scope);
                        try { reg.update(); } catch (e) {}
                    })
                    .catch(function(err) { console.log('SW registration failed', err); });
            }
        </script>
        @yield('scripts')
    </body>
</html>
