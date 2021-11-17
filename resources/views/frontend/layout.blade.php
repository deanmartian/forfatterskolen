<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-44061222-1"></script>
        <script async>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-44061222-1');
        </script>

        <!-- Global site tag (gtag.js) - Google Ads: 754620576 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-754620576"></script>
        <script async>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'AW-754620576');
        </script>

        <meta name="google-site-verification" content="PT1CQ7dxKhPpwvuFW6e2o_AVdp10XC-wUvvbHHuY0IE" />
        @include('frontend.partials.frontend-css')

        <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js" defer></script>
        <![endif]-->

        <?php
            $pageMeta = \App\PageMeta::where('url', url()->current())->first();
        ?>

        @if ($pageMeta)
            <meta property="og:title" content="{{ $pageMeta->meta_title }}">
            <meta property="og:description" content="{{ $pageMeta->meta_description }}">
            <meta name="description" content="{{ $pageMeta->meta_description }}">
            <meta property="og:site_name" content="Forfatterskolen">
            <meta property="og:url" content="{{ url()->current() }}">
            <meta property="og:type" content="website" />
            @if ($pageMeta->meta_image)
                <meta property="og:image" content="{{ url($pageMeta->meta_image) }}">
                <meta property="twitter:image" content="{{ url($pageMeta->meta_image) }}">
            @endif

            <meta property="twitter:title" content="{{ $pageMeta->meta_title }}">
            <meta property="twitter:description" content="{{ $pageMeta->meta_description }}">
            <meta name="twitter:card" content="summary" />
            <meta name="twitter:title" content="{{ $pageMeta->meta_title }}" />
            <meta name="twitter:description" content="{{ $pageMeta->meta_description }}" />
            <meta property="fb:app_id" content="300010277156315" />

            <title>
                {{ $pageMeta->meta_title }}
            </title>
        @endif

        <!-- use meta title first before the title on the actual page added-->
        @yield('title')
        <meta name="keywords" content="forfatterskolen,forfatter,forfatter kurs,course,shop manuscript">
        <meta name="nosnippets">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0 maximum-scale=1.0, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="p:domain_verify" content="eca72f9965922b1f82c80a1ef6e62743"/>
        @yield('metas')

        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}" />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css"
              integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
        @yield('styles')

    <!-- ConnectRetarget PowerPixel -->
        {{--<script>
            var CRConfig = {
                'pixel_prefix':'forfatterskolen1',
                'init_fb':false,
                'fb_pixel_id':'216415385555961'
            };
        </script>
        <script src='//connectio.s3.amazonaws.com/connect-retarget.js?v=1.1'></script>
        <noscript><img height='1' width='1' style='display:none' src='https://www.facebook.com/tr?id=216415385555961&ev=PageView&noscript=1' /></noscript>--}}

        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '216415385555961');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                       src="https://www.facebook.com/tr?id=216415385555961&ev=PageView&noscript=1"/></noscript>
        <!-- End ConnectRetarget PowerPixel -->

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
    <body>
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
            'learner.survey', 'learner.private-message'];
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
                @include('frontend.partials.learner-nav')
            @else
                @include('frontend.partials.navbar-new')
            @endif
        @else
            @include('frontend.partials.navbar-new')
        @endif

        @yield('content')

        {{--@if(!in_array(Route::currentRouteName(), $newDesignPages) && !in_array(Route::currentRouteName(), $loggedInPages))
            @include('frontend.partials.footer')
        @else
            @include('frontend.partials.footer-new')
        @endif--}}

        @if (Route::currentRouteName() == 'front.home')
            @include('frontend.partials.home-footer')
        @else
            @include('frontend.partials.footer-new')
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
    <script type="text/javascript" defer>
        (function(d, src, c) { var t=d.scripts[d.scripts.length - 1],s=d.createElement('script');s.id='la_x2s6df8d';s.async=true;s.src=src;s.onload=s.onreadystatechange=function(){var rs=this.readyState;if(rs&&(rs!='complete')&&(rs!='loaded')){return;}c(this);};t.parentElement.insertBefore(s,t.nextSibling);})(document,
            'https://forfatterskolen.ladesk.com/scripts/track.js',
            function(e){ LiveAgent.createButton('bocb2pt7', e); });
    </script>
    <script defer>!function(){window;var e,t=document;e=function(){var e=t.createElement("script");e.type="text/javascript",e.defer=!0,e.src="https://cdn.endorsal.io/widgets/widget.min.js";var n=t.getElementsByTagName("script")[0];n.parentNode.insertBefore(e,n),e.onload=function(){NDRSL.init("5de00781dd95d15fd33a275f")}},"interactive"===t.readyState||"complete"===t.readyState?e():t.addEventListener("DOMContentLoaded",e())}();</script>
    <!-- support chat  -->
    <script>
        helpwiseSettings = {
            widget_id: '60b54b2873539',
            align:'right',
        }
    </script>
    <script src="https://cdn.helpwise.io/assets/js/livechat.js"></script>
    </body>
</html>
