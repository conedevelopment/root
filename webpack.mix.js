const mix = require('laravel-mix');

mix.webpackConfig({
    output: {
        publicPath: '/vendor/root/',
        chunkFilename: '[name].js',
    },
    devtool: 'inline-source-map',
});

mix.setPublicPath('./public')
    .js('resources/js/app.js', 'app.js')
    .vue({ runtimeOnly: true })
    .extract(['vue', 'quill'])
    .sass('resources/sass/app.scss', 'app.css')
    .options({ processCssUrls: false })
    .sourceMaps(! mix.inProduction())
    .version();
