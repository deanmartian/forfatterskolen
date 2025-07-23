const mix = require('laravel-mix');
const { GenerateSW } = require('workbox-webpack-plugin');

mix.js('resources/assets/js/app.js', 'public/js')
   .vue()
   .sass('resources/assets/sass/app.scss', 'public/css')
   .disableNotifications();

if (mix.inProduction()) {
    mix.webpackConfig({
        plugins: [
            new GenerateSW({
                swDest: 'service-worker.js',
                maximumFileSizeToCacheInBytes: 10 * 1024 * 1024, // optional: increase cache file size limit
                skipWaiting: true,
                clientsClaim: true,
            })
        ]
    });
}
