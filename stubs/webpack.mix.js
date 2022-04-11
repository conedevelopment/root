mix.js('resources/js/vendor/root/app.js', 'public/vendor/root')
    .vue({ runtimeOnly: true })
    .sass('resources/sass/vendor/root/app.scss', 'public/vendor/root')
    .options({ processCssUrls: false })
    .sourceMaps()
    .version();
