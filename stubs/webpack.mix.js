mix.js('resources/js/vendor/root/app.js', 'public/vendor/root')
    .vue({ version: 3, runtimeOnly: true })
    .sass('resources/sass/vendor/root/app.scss', 'public/vendor/root')
    .options({ processCssUrls: false })
    .sourceMaps()
    .version();
