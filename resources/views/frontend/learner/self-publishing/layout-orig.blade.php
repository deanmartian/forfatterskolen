<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="alternate" href="{{ config('app.url') }}" hreflang="no" />
        <link rel="alternate" href="{{ config('app.url') }}/en" hreflang="en" />
        <link rel="canonical" href="{{ url()->current() }}">
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-PBZBPBN2');</script>
        <!-- End Google Tag Manager -->

        <meta name="google-site-verification" content="PT1CQ7dxKhPpwvuFW6e2o_AVdp10XC-wUvvbHHuY0IE" />
        @include('frontend.partials.frontend-css')
        <link rel="stylesheet" href="{{asset('css/self-publishing.css?v='.time())}}">

        <!-- use meta title first before the title on the actual page added-->
        @yield('title')
        <meta name="keywords" content="forfatterskolen, forfatter, kurs, manusutvikling, manus, manuskript, kikt, sakprosa, serieroman, krim, roman">
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

    <body>

        <header class="self-publishing-header">
            @include('frontend.partials._self_publishing_header')
        </header>

        <main id="app-container" class="self-publishing-learner-container container-fluid">
            <div class="row">
                <div class="col-md-2 p-0 learner-menu">
                    @include('frontend.partials._self_publishing_menu')
                </div>
    
                <!-- Content -->
                <div class="col-md-10">
                    @yield('content')
                </div>
            </div>
        </main>

        <footer>
            @include('frontend.partials._self_publishing_footer')
        </footer>

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