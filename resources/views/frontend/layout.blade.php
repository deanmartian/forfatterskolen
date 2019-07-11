<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-44061222-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-44061222-1');
        </script>

        <!-- Global site tag (gtag.js) - Google Ads: 754620576 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-754620576"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'AW-754620576');
        </script>

        <meta name="google-site-verification" content="PT1CQ7dxKhPpwvuFW6e2o_AVdp10XC-wUvvbHHuY0IE" />
        @yield('title')
        @include('frontend.partials.frontend-css')

        <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <?php
            $pageMeta = \App\PageMeta::where('url', url()->current())->first();
        ?>

        @if ($pageMeta)
            <meta name="title" content="{{ $pageMeta->meta_title }}">
            <meta name="description" content="{{ $pageMeta->meta_description }}">
            @if ($pageMeta->meta_image)
                <meta property="og:image" content="{{ asset($pageMeta->meta_image) }}">
            @endif
        @endif
        <meta name="keywords" content="forfatterskolen,forfatter,forfatter kurs,course,shop manuscript">
        <meta name="nosnippets">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0 maximum-scale=1.0, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="p:domain_verify" content="eca72f9965922b1f82c80a1ef6e62743"/>
        @yield('metas')

        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}" />
        @yield('styles')

        <!-- ConnectRetarget PowerPixel -->
        <script>
            let CRConfig = {
                'pixel_prefix':'forfatterskolen',
                'init_fb':true,
                'fb_pixel_id':'216415385555961'
            };
        </script>
        {{--<script src='//connectio.s3.amazonaws.com/connect-retarget.js?v=1.1' defer></script>--}}
        <script src="{{ asset('js/amazonaws-connect-retarget.min.js') }}" defer></script>
        <noscript><img height='1' width='1' style='display:none' src='https://www.facebook.com/tr?id=216415385555961&ev=PageView&noscript=1' /></noscript>
        <!-- End ConnectRetarget PowerPixel -->

        <script>
            window.Laravel = '{{ json_encode(['csrfToken' => csrf_token()]) }}';
        </script>
    </head>
    <body>

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
            'learner.survey'];
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

        @include('frontend.partials.scripts')
        <script src="/js/lang.js?v="{{ time() }}></script>
        <script>
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
    </body>
</html>
