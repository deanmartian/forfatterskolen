<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="alternate" href="{{ config('app.url') }}" hreflang="no" />
        <link rel="alternate" href="{{ config('app.url') }}/en" hreflang="en" />
        <link rel="alternate" href="{{ url()->current() }}" hreflang="{{ app()->getLocale() }}" />
        <link rel="canonical" href="{{ url()->current() }}">
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-PBZBPBN2');</script>
            <!-- End Google Tag Manager -->

        @if(in_array(Route::currentRouteName(), ['front.free-webinar']))
            <!-- Event snippet for Webinar_pamelding conversion page In your html page, add the snippet and call
             gtag_report_conversion when someone clicks on the chosen link or button. -->
            <script>
                function gtag_report_conversion(url) {
                    var callback = function () {
                        if (typeof(url) != 'undefined') {
                            window.location = url;
                        }
                    };
                    gtag('event', 'conversion', {
                        'send_to': 'AW-754620576/3IacCOOq1sIDEKCx6ucC',
                        'event_callback': callback
                    });
                    return false;
                }
            </script>
        @endif


        <meta name="google-site-verification" content="PT1CQ7dxKhPpwvuFW6e2o_AVdp10XC-wUvvbHHuY0IE" />
        @include('frontend.partials.frontend-css')

        <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js" defer></script>
        <![endif]-->

        <?php
            $pageMeta = \App\PageMeta::where('url', url()->current())->first();

            $checkoutTitle = 'Forfatterskolens utsjekksside der brukerne kan legge inn bestillinger';
            $checkoutDescription = 'Utsjekkssiden viser alle nødvendige felt og betalingsalternativer som gjør det enklere for brukeren å bestille varen';
            $genericTitle = 'Forfatterskolens side for forfattere';
            $genericDescription = 'Denne siden tilhører Forfatterskolen og viser innhold som hjelper forfattere å øke sin kunnskap';

            $meta_title = $pageMeta ? $pageMeta->meta_title :
                (strpos(url()->current(), 'checkout') !== false ? $checkoutTitle : $genericTitle);
            $meta_description = $pageMeta ? $pageMeta->meta_description :
                (strpos(url()->current(), 'checkout') !== false ? $checkoutDescription : $genericDescription);

            $defaultKeywords = 'forfatterskolen, forfatterkurs, manusutvikling, manuskript, dikt, sakprosa, serieroman, krim, roman';
            $meta_keywords = $pageMeta && $pageMeta->meta_keywords ? $pageMeta->meta_keywords : $defaultKeywords;
        ?>

        {{--@if ($pageMeta)--}}
            <meta property="og:title" content="{{ $meta_title }}">
            <meta property="og:description" content="{{ $meta_description }}">
            <meta name="description" content="{{ $meta_description }}">
            <meta property="og:site_name" content="Forfatterskolen">
            <meta property="og:url" content="{{ url()->current() }}">
            <meta property="og:type" content="website" />
            @if ($pageMeta && $pageMeta->meta_image)
                <meta property="og:image" content="{{ url($pageMeta->meta_image) }}">
                <meta property="twitter:image" content="{{ url($pageMeta->meta_image) }}">
            @endif

            <meta property="twitter:title" content="{{ $meta_title }}">
            <meta property="twitter:description" content="{{ $meta_description }}">
            <meta name="twitter:site" content="@forfatterskolen" />
            <meta name="twitter:card" content="summary" />
            <meta name="twitter:title" content="{{ $meta_title }}" />
            <meta name="twitter:description" content="{{ $meta_description }}" />
            <meta property="fb:app_id" content="300010277156315" />

            <title>
                {{ $meta_title }}
            </title>
        {{--@endif--}}

        <!-- use meta title first before the title on the actual page added-->
        @yield('title')
        <meta name="keywords" content="{{ $meta_keywords }}">
        <meta name="nosnippets">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0 maximum-scale=1.0, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="p:domain_verify" content="eca72f9965922b1f82c80a1ef6e62743"/>
        @yield('metas')

        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}" />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css"
              integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
        @yield('styles')

        <script  async>
            window.Laravel = '{{ json_encode(['csrfToken' => csrf_token()]) }}';
        </script>

        <script type="text/javascript">
            window.GUMLET_CONFIG = {
                hosts: [{
                    current: "https://www.forfatterskolen.no/",
                    gumlet: "forfatterskolen.gumlet.com"
                }]
            };
        </script>
        <script async src="https://cdn.gumlet.com/gumlet.js/2.0/gumlet.min.js"></script>
    </head>
    <body>{{-- class="dark-mode"--}}
    {{--<img src="https://www.sociamonials.com/tracking.php?t=l&tid=6502" width="1" height="1">--}}
    @if(Session::has('new_user_social'))
        <div class="alert alert-success" role="alert" id="fixed_to_bottom_alert">
            Thank you. The default password is 123. Please update your password
            <a href="{{ route('learner.profile') }}">here</a>.
        </div>
    @endif

    <?php
        $shopManuscriptAdvisory = \App\Http\FrontendHelpers::getShopManuscriptAdvisory();
        $from_date              = \Carbon\Carbon::parse($shopManuscriptAdvisory->from_date);
        $to_date                = \Carbon\Carbon::parse($shopManuscriptAdvisory->to_date);
        $isBetweenDate          = \Carbon\Carbon::today()->between($from_date, $to_date);
        $included_pages         = unserialize($shopManuscriptAdvisory->page_included);
    ?>
        {{-- check if advisory could be displayed today and current page is included --}}
        @if($isBetweenDate && in_array(Route::currentRouteName(), $included_pages))
            <div class="alert shop-manuscript-advisory" role="alert" id="fixed_to_bottom_alert">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                {{ $shopManuscriptAdvisory->advisory }}
            </div>
        @endif

        <?php
/*        $newDesignPages = ['front.shop-manuscript.index', 'front.publishing', 'front.blog', 'front.shop.thankyou',
            'front.thank-you', 'front.course.index', 'front.course.show', 'front.opt-in.thanks', 'front.opt-in.referral',
            'front.contact-us', 'front.faq', 'front.read-blog', 'front.coaching-timer', 'front.support',
            'front.support-articles', 'front.support-article', 'front.course.checkout', 'front.home',
            'front.free-manuscript.success', 'front.workshop.index', 'front.workshop.show', 'front.course.apply-discount',
            'front.shop-manuscript.checkout', 'front.workshop.checkout', 'front.copy-editing', 'front.correction',
            'front.other-service-checkout', 'front.opt-in', 'front.coaching-timer-checkout', 'front.webinar-thanks',
            'front.free-manuscript.index', 'front.course.claim-reward', 'auth.login.show', 'front.henrik',
            'front.free-webinar', 'front.free-webinar-thanks', 'front.terms', 'front.opt-in-terms', 'front.poems'];*/

        $loggedInPages = ['learner.dashboard', 'learner.account.search', 'learner.course', 'learner.course.show',
            'learner.course.lesson', 'learner.shop-manuscript', 'learner.shop-manuscript.show', 'learner.workshop',
            'learner.webinar', 'learner.course-webinar', 'learner.assignment', 'learner.assignment.group.show',
            'learner.calendar', 'learner.invoice', 'learner.upgrade', 'learner.get-upgrade-manuscript',
            'learner.get-upgrade-assignment', 'learner.get-upgrade-course', 'learner.competition', 'learner.profile',
            'learner.survey', 'learner.private-message', 'learner.time-register', 'learner.book-sale', 'learner.project', 'learner.project.show',
            'learner.project.marketing-plan', 'learner.project.graphic-work', 'learner.project.registration',
            'learner.project.marketing', 'learner.project.contract', 'learner.project.invoice'];
        ?>
        {{--@if(!in_array(Route::currentRouteName(), $newDesignPages) && !in_array(Route::currentRouteName(), $loggedInPages))
            @include('frontend.partials.navbar')
        @else
            @if (in_array(Route::currentRouteName(),$loggedInPages))
                @if (Auth::user())
                    @include('frontend.partials.learner-nav')
                @else
                    @include('frontend.partials.navbar-new')
                @endif
            @else
                @include('frontend.partials.navbar-new')
            @endif
        @endif--}}

        @if (in_array(Route::currentRouteName(),$loggedInPages))
            @if (Auth::user())
                @if (Session::get('current-portal') === 'self-publishing')
                    @include('frontend.partials.self-publishing-nav')
                @else
                    @include('frontend.partials.learner-nav')
                @endif
            @else
                @include('frontend.partials._navbar-latest')
            @endif
        @else
            @include('frontend.partials._navbar-latest')
        @endif

        @yield('content')

        {{--@if(!in_array(Route::currentRouteName(), $newDesignPages) && !in_array(Route::currentRouteName(), $loggedInPages))
            @include('frontend.partials.footer')
        @else
            @include('frontend.partials.footer-new')
        @endif--}}

        @if (Route::currentRouteName() == 'front.home')
            @include('frontend.partials.home-footer-new')
        @else
            {{-- @include('frontend.partials.footer-new') --}}
            @include('frontend.partials.home-footer-new')
        @endif

        @if($errors->count())
            <?php
            $alert_type = session('alert_type');
            if(!Session::has('alert_type')) {
                $alert_type = 'danger';
            }
            ?>
            <div class="alert alert-{{ $alert_type }} global-alert-box" style="z-index: 9; min-width: 300px"
                 id="fixed_to_bottom_alert">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('frontend.partials.scripts')
        <script src="https://Forfatterskolen.cdn.vooplayer.com/assets/vooplayer.js" defer></script>
        <script src="/js/lang.js"></script>
        <script async>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if ('serviceWorker' in navigator ) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/service-worker.js').then(function(registration) {
                        // Registration was successful
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    }, function(err) {
                        // registration failed :(
                        console.log('ServiceWorker registration failed: ', err);
                    });
                });
            }

            $(function(){
               $(".notification-list > li").hover(function(){
                  let extract   = $(this).prop('id');
                  let id        = parseInt(extract.split('notif-')[1]);
                  let self      = $(this);
                  let notif_badge = $(".notif-badge");
                  if (self.hasClass('unread')) {
                      self.removeClass('unread');
                      let notif_count = parseInt(notif_badge.text()) - 1;
                      notif_badge.text(notif_count);
                      $.post('/account/notification/'+id+'/mark-as-read',{})
                          .then(function(response){
                          })
                          .catch(function(response){
                          })
                  }
               });

               let learnerMenuI = $(".learner-menu").find('li.active').find('i');
               if (learnerMenuI.length) {
                   let learnerMenuCurrentClass = learnerMenuI.attr('class').split(' ')[1];
                   let newMenuClass = learnerMenuCurrentClass+'-red';
                   learnerMenuI.removeClass(learnerMenuCurrentClass).addClass(newMenuClass);
               }

               /*let mobileLearnerMenu = $("#mobile-learner-menu");
               mobileLearnerMenu.find('.navbar-toggler').on('click',function(){
                  $(".mobile-learner-menu").toggleClass('d-block');
               });*/

               $(".portal-menu").find('.navbar-toggler').on('click', function(){
                   let portalTogglerI = $(this).find('i');
                   if (portalTogglerI.hasClass('fa-chevron-down')) {
                       portalTogglerI.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                   } else {
                       portalTogglerI.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                   }
               });

               let portalNavI = $("#portalNav").find('div.active').find('i');
               if(portalNavI.length) {
                   let portalNavCurrentClass = portalNavI.attr('class').split(' ')[1];
                   let newPortalNavClass = portalNavCurrentClass+'-red';
                   portalNavI.removeClass(portalNavCurrentClass).addClass(newPortalNavClass);
               }

               $(".navbar-toggler").click(function(){
                   // opposite of how it usually works
                   if (!$("#mainNav").hasClass('show')) {
                        $(".navbar-default").show();
                   } else {
                       $(".navbar-default").slideUp();
                   }
               });

                $(window).resize(function() {
                    if ($(window).width() > 640) {
                        $("#mainNav").parent(".navbar-expand-md").show();
                    } else {
                        $("#mainNav").parent(".navbar-expand-md").hide();
                    }
                });
            });

            function disableSubmit(t) {
                let submit_btn = $(t).find('[type=submit]');
                submit_btn.text('');
                submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
                submit_btn.attr('disabled', 'disabled');
            }

            function disableSubmitOrigText(t) {
                let submit_btn = $(t).find('[type=submit]');
                submit_btn.attr('disabled', 'disabled');
            }

            function setupGlobalFileUpload(area) {
                const fileUploadArea = document.getElementById(area);
                const fileInput = fileUploadArea.querySelector('.input-file-upload');
                const fileUploadText = fileUploadArea.querySelector('.file-upload-text');

                // Function to open the file input dialog when the file-upload-area is clicked
                const openFileInput = () => {
                    fileInput.click();
                };

                // Function to update the file upload text
                const updateText = (text) => {
                    fileUploadText.innerHTML = text;
                };

                // Function to check if the file input is not empty
                const isFileInputNotEmpty = () => {
                    return fileInput.files.length > 0;
                };

                fileUploadArea.querySelector('.file-upload-btn').addEventListener('mousedown', (e) => {
                    // Check if the mousedown event was triggered by the button inside file-upload-area
                    if (e.target.classList.contains('file-upload-btn')) {
                        openFileInput();
                    }
                });

                // Add a click event for the file-upload-btn in the current modal
                fileUploadArea.querySelector('.file-upload-btn').addEventListener('click', openFileInput);

                const textWithBrowseButton = 'Drag and drop files or <a href="javascript:void(0)" class="file-upload-btn">Klikk her</a>';

                fileUploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    fileUploadArea.classList.add('dragover');
                    updateText('Release to upload');
                });

                fileUploadArea.addEventListener('dragleave', () => {
                    fileUploadArea.classList.remove('dragover');
                    updateText(textWithBrowseButton);
                });

                fileUploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    fileUploadArea.classList.remove('dragover');

                    const files = e.dataTransfer.files;

                    for (let i = 0; i < files.length; i++) {
                        console.log('Dropped file:', files[i].name);
                    }

                    fileInput.files = files;

                    const selectedText = isFileInputNotEmpty() ? fileInput.files[0].name : textWithBrowseButton;
                    updateText(selectedText);
                });

                fileInput.addEventListener('change', () => {
                    const selectedText = isFileInputNotEmpty() ? fileInput.files[0].name : textWithBrowseButton;
                    updateText(selectedText);
                });

                // Add a click event for the file-upload-area to open the file input dialog
                fileUploadArea.addEventListener('click', openFileInput);
            }

            const layoutMethod = {
                removeNotification: function(id) {

                    $("#notif-"+id).remove();
                    $("#all-notif-"+id).remove();
                    $.post('/account/notification/'+id+'/delete',{})
                        .then(function(response){
                        })
                        .catch(function(response){
                        })
                }
            }
        </script>
        @yield('scripts')
    {{--<script type="text/javascript" defer>
        (function(d, src, c) { var t=d.scripts[d.scripts.length - 1],s=d.createElement('script');s.id='la_x2s6df8d';s.async=true;s.src=src;s.onload=s.onreadystatechange=function(){var rs=this.readyState;if(rs&&(rs!='complete')&&(rs!='loaded')){return;}c(this);};t.parentElement.insertBefore(s,t.nextSibling);})(document,
            'https://forfatterskolen.ladesk.com/scripts/track.js',
            function(e){ LiveAgent.createButton('bocb2pt7', e); });
    </script>--}}
    @if (!in_array(Route::currentRouteName(),['front.course.checkout', 'front.shop-manuscript.checkout']))
    <script defer>!function(){window;var e,t=document;e=function(){var e=t.createElement("script");e.type="text/javascript",e.defer=!0,e.src="https://cdn.endorsal.io/widgets/widget.min.js";var n=t.getElementsByTagName("script")[0];n.parentNode.insertBefore(e,n),e.onload=function(){NDRSL.init("5de00781dd95d15fd33a275f")}},"interactive"===t.readyState||"complete"===t.readyState?e():t.addEventListener("DOMContentLoaded",e())}();</script>
    <!-- support chat  -->
    <script>
        helpwiseSettings = {
            widget_id: '60b54b2873539',
            align:'right',
        }
    </script>
    <script src="https://cdn.helpwise.io/assets/js/livechat.js"></script>
    @endif
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PBZBPBN2"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    </body>
</html>
