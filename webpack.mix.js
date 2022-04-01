const { mix } = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.disableNotifications();

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css');

let SWPrecacheWebpackPlugin = require('sw-precache-webpack-plugin');
mix.webpackConfig({
    plugins: [
        new SWPrecacheWebpackPlugin({
            cacheId: 'pwa',
            filename: 'service-worker.js',
            staticFileGlobs: ['public/**/*.{css,eot,svg,ttf,woff,woff2,js,html}'],
            minify: true,
            stripPrefix: 'public/',
            handleFetch: true,
            /*dynamicUrlToDependencies: { //you should add the path to your blade files here so they can be cached
                //and have full support for offline first (example below)
                '/': ['resources/views/frontend/home.blade.php'],
                '/course': ['resources/views/frontend/course/index.blade.php'],
                '/shop-manuscript': ['resources/views/frontend/shop-manuscript/index.blade.php'],
                '/contact-us': ['resources/views/frontend/contact-us.blade.php'],
                '/faq': ['resources/views/frontend/faq.blade.php'],
                '/support': ['resources/views/frontend/solution.blade.php'],
                '/support/!*!/articles': ['resources/views/frontend/solution-articles.blade.php'],
                '/support/!*!/articles/!*': ['resources/views/frontend/solution-article.blade.php'],
                '/free-webinar/!*': ['resources/views/frontend/free-webinar.blade.php'],
                '/free-webinar/!*!/thank-you': ['resources/views/frontend/free-webinar-success.blade.php'],
                '/webinartakk': ['resources/views/frontend/webinar-thanks.blade.php'],
                '/children': ['resources/views/frontend/children.blade.php'],
                '/subscribe-success': ['resources/views/frontend/subscribe-success.blade.php'],
                '/blog': ['resources/views/frontend/blog-new.blade.php'],
                '/blog/!*': ['resources/views/frontend/blog-read.blade.php'],
                '/publishing': ['resources/views/frontend/publishing.blade.php'],
                '/coaching-timer': ['resources/views/frontend/coaching-timer.blade.php'],
                '/coaching-timer/checkout/!*': ['resources/views/frontend/coaching-timer-checkout.blade.php',
                    'resources/views/frontend/coaching-timer.blade.php'],
                '/copy-editing': ['resources/views/frontend/copy-editing.blade.php'],
                '/other-services': ['resources/views/frontend/other-services.blade.php'],
                '/other-services/checkout/!*!/!*': ['resources/views/frontend/other-service-checkout.blade.php'],
                '/thank-you': ['resources/views/frontend/thank-you.blade.php'],
                '/correction': ['resources/views/frontend/correction.blade.php'],
                '/gratis-tekstvurdering': ['resources/views/frontend/shop-manuscript/free-manuscript.blade.php'],
                '/gratis-tekstvurdering/success': ['resources/views/frontend/shop-manuscript/free-manuscript-success.blade.php'],
                '/opt-in/!*': ['resources/views/frontend/opt-in.blade.php'],
                '/opt-in/thanks/!*': ['resources/views/frontend/opt-in-thanks/dikt.blade.php',
                    'resources/views/frontend/opt-in-thanks/crime.blade.php',
                    'resources/views/frontend/opt-in-thanks/children.blade.php',
                    'resources/views/frontend/opt-in-thanks/fiction.blade.php'],
                '/opt-in/ref/!*': ['resources/views/frontend/opt-in-thanks/referral.blade.php'],
                '/opt-in-terms': ['resources/views/frontend/opt-in-terms.blade.php'],
                '/terms/!*': ['resources/views/frontend/terms.blade.php'],
                '/shop-manuscript/!*!/checkout': ['resources/views/frontend/shop-manuscript/checkout.blade.php'],
                '/upgrade-manuscript/!*!/checkout': ['resources/views/frontend/shop-manuscript/upgrade.blade.php'],
                '/email/confirmation/!*': ['resources/views/frontend/learner/email/confirm.blade.php',
                    'resources/views/frontend/learner/email/invalid.blade.php'],
                '/henrik-langeland': ['resources/views/frontend/henrik-langeland.blade.php'],
                '/poems': ['resources/views/frontend/poems.blade.php'],
                '/webinar-pakke-campaign': ['resources/views/frontend/upviral-campaign/webinar-pakke.blade.php'],
                '/test-campaign': ['resources/views/frontend/upviral-campaign/test.blade.php']
            },*/
            staticFileGlobsIgnorePatterns: [/\.map$/, /mix-manifest\.json$/, /manifest\.json$/, /service-worker\.js$/],
            navigateFallback: '/',
            runtimeCaching: [
                {
                    urlPattern: /^https:\/\/fonts\.googleapis\.com\//,
                    handler: 'cacheFirst'
                }
            ],
            // importScripts: ['./js/push_message.js']
        })
    ]
});
