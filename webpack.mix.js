const mix = require('laravel-mix');

mix.webpackConfig({
    output: {
        publicPath: '/vendor/root/',
        chunkFilename: '[name].js',
    },
    stats: {
        children: true
    },
});

mix.setPublicPath('./public')
    .js('resources/js/app.js', 'app.js')
    .vue({ version: 3, runtimeOnly: true })
    .sass('resources/sass/app.scss', 'app.css')
    .options({ processCssUrls: false })
    .sourceMaps()
    .version();
