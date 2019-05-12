let mix = require('laravel-mix');
mix.disableNotifications();

// Optimize npm CPU usage while using watch option
mix.webpackConfig({
    watchOptions: {
        aggregateTimeout: 2000,
        poll: 2000,
        ignored: /node_modules/
    }
});

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

if (process.env.NODE_ENV === 'production') {
    mix.js('resources/js/vendor.js', 'public/js/vendor.min.js')
        .js('resources/js/app.js', 'public/js/app.min.js')
        .sass('resources/sass/vendor.scss', 'public/css/vendor.min.css')
        .sass('resources/sass/app.scss', 'public/css/app.min.css')
        .version();
} else {
    // Disable mix-manifest.json on development
    Mix.manifest.refresh = function () {};

    mix.js('resources/js/vendor.js', 'public/js')
        .js('resources/js/app.js', 'public/js')
        .sass('resources/sass/vendor.scss', 'public/css')
        .sass('resources/sass/app.scss', 'public/css')
        .sourceMaps();
}