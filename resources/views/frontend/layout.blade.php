<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        @include('partials.sw-cleanup-script')

        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#862736">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <link rel="apple-touch-icon" href="/icons/icon-192.png">
        <link rel="alternate" href="{{ config('app.url') }}" hreflang="no" />
        <link rel="alternate" href="{{ config('app.url') }}/en" hreflang="en" />
        <link rel="alternate" href="{{ url()->current() }}" hreflang="{{ app()->getLocale() }}" />
        <link rel="alternate" href="{{ url()->current() }}" hreflang="x-default" />
        <link rel="canonical" href="{{ url()->current() }}">


        <meta name="google-site-verification" content="PT1CQ7dxKhPpwvuFW6e2o_AVdp10XC-wUvvbHHuY0IE" />
        @include('frontend.partials.frontend-css')

        <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js" defer></script>
        <![endif]-->

        <?php
            $pageMeta = \App\PageMeta::where('url', url()->current())->first();

            $defaultTitle = 'Forfatterskolen — for deg som vil gjøre alvor av skrivedrømmen';
            $defaultDescription = 'Skrivekurs på nett med erfarne forfattere og redaktører. Fra idé til ferdig manus — roman, barnebok, sakprosa. 5000+ kursdeltagere siden 2015.';
            $defaultImage = asset('images-new/forfatterskolen-og.jpg');

            $meta_title = $pageMeta ? $pageMeta->meta_title : $defaultTitle;
            $meta_description = $pageMeta ? $pageMeta->meta_description : $defaultDescription;
            $meta_image = ($pageMeta && $pageMeta->meta_image) ? url($pageMeta->meta_image) : $defaultImage;

            $defaultKeywords = 'skrivekurs, forfatterkurs, romankurs, skriveverksted, manusutvikling, forfatterskolen, lær å skrive bok, skrivekurs på nett';
            $meta_keywords = $pageMeta && $pageMeta->meta_keywords ? $pageMeta->meta_keywords : $defaultKeywords;
        ?>

        <meta property="og:title" content="{{ $meta_title }}">
        <meta property="og:description" content="{{ $meta_description }}">
        <meta name="description" content="@yield('meta_desc', $meta_description)">
        <meta property="og:site_name" content="Forfatterskolen">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:type" content="website">
        <meta property="og:image" content="{{ $meta_image }}">
        <meta property="og:locale" content="nb_NO">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@forfatterskolen">
        <meta name="twitter:title" content="{{ $meta_title }}">
        <meta name="twitter:description" content="{{ $meta_description }}">
        <meta name="twitter:image" content="{{ $meta_image }}">
        <meta property="fb:app_id" content="300010277156315">

        {{-- JSON-LD Structured Data — Organization (global) + per-side via @yield --}}
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "EducationalOrganization",
            "name": "Forfatterskolen",
            "alternateName": "Forfatterskolen.no",
            "url": "{{ config('app.url') }}",
            "logo": "{{ asset('photos/logos/fs-logo.png') }}",
            "description": "{{ $meta_description }}",
            "foundingDate": "2015",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "Lihagen 21",
                "postalCode": "3029",
                "addressLocality": "Drammen",
                "addressCountry": "NO"
            },
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+47-411-23-555",
                "email": "post@forfatterskolen.no",
                "contactType": "customer service",
                "availableLanguage": "Norwegian"
            },
            "sameAs": [
                "https://www.facebook.com/forfatterskolen",
                "https://www.instagram.com/forfatterskolen_norge"
            ]
        }
        </script>
        @yield('jsonld')

        <title>@yield('page_title', $meta_title)</title>
        @php
            $noindexPaths = [
                'checkout', 'svea-checkout', 'place_order', 'cancelled-order',
                'auth/login', 'auth/register', 'auth/passwordreset', 'auth/vipps',
                'account/', 'learner/', 'self-publishing/',
                'upgrade-', 'coaching-timer-checkout', 'coaching-timer-login',
                'opt-in-thanks', 'thank-you', 'thankyou', 'confirmation',
                'competition/innlevering', 'competition/thank',
                'gift/redeem', 'gift/course-checkout', 'gift/shop-manuscript-checkout',
                'personal-trainer/checkout', 'personal-trainer/thank',
                'publising-service/checkout', 'publising-service/thankyou',
                'shop-manuscript/login', 'shop-manuscript/checkout', 'shop-manuscript/upgrade',
                'shop-manuscript/cancelled', 'shop-manuscript/payment',
                'manual-invoice', 'email-tracking', 'chat/', 'subscribe-success',
                'upviral-campaign', 'pilot-reader/', 'community/',
                'workshop/checkout', 'blog?page=',
            ];
            $currentPath = request()->path();
            $shouldNoindex = false;
            foreach ($noindexPaths as $path) {
                if (str_contains($currentPath, $path)) {
                    $shouldNoindex = true;
                    break;
                }
            }
        @endphp
        @if($shouldNoindex)
        <meta name="robots" content="noindex, follow">
        @endif
        <meta name="keywords" content="{{ $meta_keywords }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        @auth
        <meta name="user-id" content="{{ Auth::id() }}">
        @endauth
        <meta name="p:domain_verify" content="eca72f9965922b1f82c80a1ef6e62743"/>
        @yield('metas')

        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}" />
        <link rel="preconnect" href="https://use.fontawesome.com" crossorigin>
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
        <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css"
              integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
        @yield('styles')

        <script  async>
            window.Laravel = '{{ json_encode(['csrfToken' => csrf_token()]) }}';
        </script>

        {{-- Gumlet fjernet — erstattet med 3-linje polyfill som konverterer
             data-src til src med native lazy loading. Dekker alle legacy-
             templates som brukte Gumlets data-src-mønster uten å måtte
             endre 20+ individuelle Blade-filer. --}}
        <script>document.addEventListener('DOMContentLoaded',function(){document.querySelectorAll('img[data-src]').forEach(function(i){i.src=i.dataset.src;i.loading='lazy';})});</script>

        @if(config('services.tracking.enabled'))
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_ads.id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ config('services.google_ads.id') }}');
        </script>

        <!-- Meta Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ config('services.meta_pixel.id') }}');
        fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ config('services.meta_pixel.id') }}&ev=PageView&noscript=1"
        /></noscript>
        <!-- End Meta Pixel Code -->
        @endif
    </head>
    <body>{{-- class="dark-mode"--}}
    @include('partials.impersonation-banner')
    @include('partials.login-help-banner')
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
                <a href="#" class="close" data-bs-dismiss="alert" aria-label="close" title="close">×</a>
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
                <a href="#" class="close" data-bs-dismiss="alert" aria-label="close" title="close">×</a>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('frontend.partials.scripts')
        {{-- vooplayer fjernet - ikke lenger i bruk --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const cacheValue = Date.now().toString();
                const links = document.querySelectorAll('a[href]');

                links.forEach((link) => {
                    const href = link.getAttribute('href');

                    if (!href || href.startsWith('#')) {
                        return;
                    }

                    const lowerHref = href.toLowerCase();
                    if (
                        lowerHref.startsWith('mailto:') ||
                        lowerHref.startsWith('tel:') ||
                        lowerHref.startsWith('javascript:')
                    ) {
                        return;
                    }

                    const hasDownloadAttr = link.hasAttribute('download');
                    const hasDownloadClass = String(link.className || '').toLowerCase().includes('download');
                    const isDownloadHref =
                        lowerHref.includes('/download') ||
                        lowerHref.includes('dropbox/download') ||
                        lowerHref.includes('/storage/') ||
                        /\.[a-z0-9]{2,5}([?#]|$)/i.test(lowerHref);

                    if (!hasDownloadAttr && !hasDownloadClass && !isDownloadHref) {
                        return;
                    }

                    const url = new URL(href, window.location.origin);
                    url.searchParams.set('v', cacheValue);
                    link.href = url.toString();
                });
            });
        </script>
        <script src="/js/lang.js"></script>
        <script async>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Service worker registrering er MIDLERTIDIG SKRUDD AV (08.04.2026)
            // på grunn av cache-trøbbel der gamle SW-er ble hengende fast.
            // Cleanup-koden øverst i <head> sørger for at alle stuck-brukere
            // blir "unstuck" automatisk. Re-enable dette om ~2 uker når vi er
            // sikre på at alle klienter har kjørt cleanup. Når vi re-enabler,
            // bruk { updateViaCache: 'none' } og kall reg.update() på load.

            // if ('serviceWorker' in navigator) {
            //     navigator.serviceWorker.register('/service-worker.js', { updateViaCache: 'none' })
            //         .then(function(reg) { try { reg.update(); } catch (e) {} })
            //         .catch(function(err) { console.log('SW registration failed', err); });
            // }

            // Push-varsler
            if ('Notification' in window && 'PushManager' in window) {
                navigator.serviceWorker.ready.then(function(reg) {
                    reg.pushManager.getSubscription().then(function(sub) {
                        if (!sub) {
                            if (document.querySelector('meta[name="user-id"]')) {
                                Notification.requestPermission().then(function(permission) {
                                    if (permission === 'granted') {
                                        subscribePush(reg);
                                    }
                                });
                            }
                        }
                    });
                });
            }

            function subscribePush(reg) {
                var vapidKey = '{{ config("webpush.vapid.public_key") }}';
                reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(vapidKey)
                }).then(function(sub) {
                    fetch('/push/subscribe', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        body: JSON.stringify(sub)
                    });
                });
            }

            function urlBase64ToUint8Array(base64String) {
                var padding = '='.repeat((4 - base64String.length % 4) % 4);
                var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                var rawData = atob(base64);
                var outputArray = new Uint8Array(rawData.length);
                for (var i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
                return outputArray;
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
    {{-- Endorsal testimonial-widget + Helpwise chat fjernet 10.04.2026.
         Endorsal: ikke i bruk lenger.
         Helpwise: erstattet av eget Inbox CRM-system (inbox:poll + InboxService).
         Begge lastet ekstern JS på HVER sidevisning uten grunn. --}}
    </body>
</html>
