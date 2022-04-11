const mix = require('laravel-mix');

mix.webpackConfig({
    output: {
        publicPath: '/vendor/root/',
        chunkFilename: '[name].js',
    },
});

mix.setPublicPath('./public')
    .js('resources/js/app.js', 'app.js')
    .vue({ runtimeOnly: true })
    .extract(['vue'])
    .sass('resources/sass/app.scss', 'app.css')
    .options({ processCssUrls: false })
    .sourceMaps()
    .version();
