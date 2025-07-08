let mix = require('laravel-mix');
mix.autoload({
    jquery: ['$', 'window.jQuery', 'jQuery']
});

mix.webpackConfig(
    {
        module: {
            rules: [
                {
                    test: /\.mjs$/, resolve: { fullySpecified: false },
                    include: /node_modules/,
                    type: "javascript/auto"
                }
            ]
        },
    }
);

mix.js('resources/js/admin.js', 'assets/js/student_discount_admin.js').vue({ version: 3 })
    .js('resources/js/student_profile.js', 'assets/js/student_profile.js').vue({ version: 3 })
    .js('resources/js/student_auth.js', 'assets/js/student_auth.js').vue({ version: 3 })
    .sass('resources/css/student_discount_admin.scss', 'assets/css/student_discount_admin.css')
    .sass('resources/css/student_profile.scss', 'assets/css/student_profile.css')
    .sass('resources/css/sa_promotions.scss', 'assets/css/sa_promotions.css')
    .sass('resources/css/student_auth.scss', 'assets/css/student_auth.css')
    .copy('resources/js/media-uploader.js', 'assets/js/media-uploader.js')
    .copy('resources/js/load-more-promotions.js', 'assets/js/load-more-promotions.js')