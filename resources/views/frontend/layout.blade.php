<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-121932549-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-121932549-1');
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
        <script src='//connectio.s3.amazonaws.com/connect-retarget.js?v=1.1'></script>
        <noscript><img height='1' width='1' style='display:none' src='https://www.facebook.com/tr?id=216415385555961&ev=PageView&noscript=1' /></noscript>
        <!-- End ConnectRetarget PowerPixel -->
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
        $newDesignPages = ['front.shop-manuscript.index', 'front.publishing', 'front.blog', 'front.shop.thankyou',
            'front.thank-you', 'front.course.index', 'front.course.show', 'front.opt-in.thanks', 'front.opt-in.referral',
            'front.contact-us', 'front.faq', 'front.read-blog', 'front.coaching-timer', 'front.support',
            'front.support-articles', 'front.support-article', 'front.course.checkout', 'front.home',
            'front.free-manuscript.success', 'front.workshop.index', 'front.workshop.show', 'front.course.apply-discount',
            'front.shop-manuscript.checkout', 'front.workshop.checkout', 'front.copy-editing']
        ?>
        @if(!in_array(Route::currentRouteName(), $newDesignPages))
            @include('frontend.partials.navbar')
        @else
            @include('frontend.partials.navbar-new')
        @endif

        @yield('content')

        @if(!in_array(Route::currentRouteName(), $newDesignPages))
            @include('frontend.partials.footer')
        @else
            @include('frontend.partials.footer-new')
        @endif

        @include('frontend.partials.scripts')
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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

               $(".navbar-toggler").click(function(){
                   // opposite of how it usually works
                   if (!$("#mainNav").hasClass('show')) {
                        $(".navbar-default").show();
                   } else {
                       $(".navbar-default").slideUp();
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
