<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
<head>
    {{-- Meta --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Styles --}}
    <link rel="stylesheet" href="{{ Vite::asset('resources/js/app.css', 'vendor/root/build') }}">

    {{-- Title --}}
    <title>@yield('title') - {{ Config::get('app.name') }}</title>
</head>
<body>
    <a class="btn btn--primary skip-link" href="#content">Skip to content</a>
    <main id="content" class="l-auth">
        <div class="l-auth__inner">
            <div class="l-auth__form">
                <a class="l-auth__logo" href="/" aria-label="Spruce CSS Eleventy Admin Template">
                    <svg width='75' height='20' viewBox='0 0 120 32' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' xml:space='preserve' xmlns:serif='http://www.serif.com/' style='fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;' class='icon'>
                        <style>
                            .caption {
                                fill: #001b30;
                            }

                            [data-theme-mode="dark"] .caption {
                                fill: #fff;
                            }

                        </style>
                        <path class='caption' d='M77.107,0c2.865,0 5.474,0.635 7.826,1.948c2.352,1.312 4.234,3.175 5.603,5.504c1.411,2.328 2.095,4.996 2.095,7.917c0,2.921 -0.684,5.589 -2.095,7.917c-1.369,2.329 -3.251,4.192 -5.646,5.504c-2.352,1.313 -4.96,1.99 -7.783,1.99c-2.865,0 -5.474,-0.677 -7.869,-1.99c-2.352,-1.312 -4.234,-3.175 -5.645,-5.504c-1.411,-2.328 -2.095,-4.996 -2.095,-7.917c-0,-2.921 0.684,-5.589 2.095,-7.917c1.411,-2.329 3.293,-4.192 5.645,-5.504c2.395,-1.313 5.004,-1.948 7.869,-1.948Zm-61.882,30.484l-5.73,-10.754l-0,10.754l-9.495,-0l0,-29.976l14.113,0c2.438,0 4.533,0.424 6.287,1.27c1.753,0.89 3.036,2.033 3.934,3.515c0.856,1.481 1.283,3.175 1.283,5.038c0,1.99 -0.556,3.768 -1.668,5.334c-1.154,1.567 -2.779,2.668 -4.875,3.345l6.586,11.474l-10.435,-0Zm104.605,-29.976l0,7.452l-8.04,-0l0,22.524l-9.494,-0l0,-22.524l-7.954,-0l-0,-7.452l25.488,0Zm-42.723,8.172c-1.924,-0 -3.378,0.592 -4.405,1.778c-1.026,1.228 -1.539,2.836 -1.539,4.911c-0,2.032 0.513,3.641 1.539,4.827c1.027,1.227 2.481,1.82 4.405,1.82c1.882,0 3.336,-0.593 4.362,-1.82c1.027,-1.186 1.54,-2.795 1.54,-4.827c-0,-2.075 -0.513,-3.683 -1.54,-4.911c-1.026,-1.186 -2.48,-1.778 -4.362,-1.778Zm-67.612,4.784l3.763,-0c0.898,-0 1.582,-0.212 2.053,-0.635c0.47,-0.381 0.684,-1.016 0.684,-1.905c-0,-0.805 -0.257,-1.398 -0.727,-1.863c-0.428,-0.466 -1.112,-0.678 -2.01,-0.678l-3.763,0l-0,5.081Z'></path>
                        <g>
                            <path d='M58.585,12.171c-1.863,-8.374 -10.173,-13.66 -18.547,-11.798c-8.374,1.863 -13.661,10.174 -11.798,18.548c1.863,8.374 10.173,13.66 18.547,11.798c8.374,-1.863 13.661,-10.174 11.798,-18.548Z' style='fill:url(#_Linear1);'></path>
                            <g>
                                <clippath id='_clip2'>
                                    <path d='M58.585,12.171c-1.863,-8.374 -10.173,-13.66 -18.547,-11.798c-8.374,1.863 -13.661,10.174 -11.798,18.548c1.863,8.374 10.173,13.66 18.547,11.798c8.374,-1.863 13.661,-10.174 11.798,-18.548Z'></path>
                                </clippath>
                                <g clip-path='url(#_clip2)'>
                                    <path d='M53.421,16.259c-0.646,0 -1.171,-0.524 -1.171,-1.171c0,-1.937 -1.576,-3.514 -3.514,-3.514c-0.646,0 -1.171,-0.524 -1.171,-1.171c0,-0.646 0.525,-1.171 1.171,-1.171c3.231,0 5.856,2.626 5.856,5.856c0,0.647 -0.524,1.171 -1.171,1.171Z' style='fill:url(#_Linear3);fill-rule:nonzero;'></path>
                                    <g>
                                        <g id='right'>
                                            <path d='M47.564,8.988c0,-1.936 -1.576,-3.513 -3.514,-3.513c-0.646,0 -1.171,-0.524 -1.171,-1.172c0,-0.646 0.525,-1.171 1.171,-1.171c2.968,0 5.499,1.944 5.805,5.079c0.033,0.052 0.001,10.979 0.001,10.979c0,0.645 0.577,1.171 1.221,1.171c0.648,0 1.172,0.525 1.172,1.171c0,0.648 -0.524,1.172 -1.172,1.172c-1.779,0 -3.256,-1.332 -3.483,-3.052c-0.02,-0.041 -0.03,-0.083 -0.03,-0.127l0,-10.537Z' style='fill:url(#_Linear4);fill-rule:nonzero;'></path>
                                        </g>
                                        <g id='left'>
                                            <path d='M33.064,16.248c-0.561,-0.323 -0.753,-1.039 -0.429,-1.6c0.323,-0.559 1.039,-0.752 1.6,-0.428c0.557,0.322 1.277,0.13 1.6,-0.429c0.323,-0.56 1.039,-0.752 1.599,-0.429c0.56,0.324 0.752,1.041 0.429,1.6c-0.969,1.678 -3.123,2.254 -4.799,1.286Z' style='fill:url(#_Linear5);fill-rule:nonzero;'></path>
                                            <path d='M35.846,12.821c0.34,-2.908 2.817,-5.171 5.816,-5.171c0.646,0 1.171,0.525 1.171,1.171c-0,0.647 -0.525,1.171 -1.171,1.171c-1.938,0 -3.514,1.577 -3.514,3.514l-0,6.05c-0.167,1.637 -1.846,2.981 -3.513,2.981c-0.647,0 -1.171,-0.524 -1.171,-1.172c-0,-0.646 0.524,-1.171 1.171,-1.171c0.644,0 1.22,-0.526 1.22,-1.171c-0,0 -0.035,-6.148 -0.009,-6.202Z' style='fill:url(#_Linear6);fill-rule:nonzero;'></path>
                                        </g>
                                        <path d='M42.94,27.035c-0.647,0 -1.171,-0.367 -1.171,-0.821l0,-41.059c0,-0.453 0.524,-0.821 1.171,-0.821c0.646,0 1.171,0.368 1.171,0.821l0,41.059c0,0.454 -0.525,0.821 -1.171,0.821Z' style='fill:#fff;fill-rule:nonzero;'></path>
                                    </g>
                                </g>
                            </g>
                        </g>
                        <defs>
                            <lineargradient id='_Linear1' x1='0' y1='0' x2='1' y2='0' gradientUnits='userSpaceOnUse' gradientTransform='matrix(-16.6518,22.7584,-22.7584,-16.6518,47.6537,6.30617)'>
                                <stop offset='0' style='stop-color:#1da1ff;stop-opacity:1'></stop>
                                <stop offset='1' style='stop-color:#0268fc;stop-opacity:1'></stop>
                            </lineargradient>
                            <lineargradient id='_Linear3' x1='0' y1='0' x2='1' y2='0' gradientUnits='userSpaceOnUse' gradientTransform='matrix(-8.67681,-1.31342,1.31342,-8.67681,57.1955,11.5717)'>
                                <stop offset='0' style='stop-color:#fff;stop-opacity:1'></stop>
                                <stop offset='0.58' style='stop-color:#f1f1f1;stop-opacity:1'></stop>
                                <stop offset='0.8' style='stop-color:#d2d2d2;stop-opacity:1'></stop>
                                <stop offset='1' style='stop-color:#b3b3b3;stop-opacity:1'></stop>
                            </lineargradient>
                            <lineargradient id='_Linear4' x1='0' y1='0' x2='1' y2='0' gradientUnits='userSpaceOnUse' gradientTransform='matrix(-8.64383,-2.77633,2.77633,-8.64383,51.5231,5.90863)'>
                                <stop offset='0' style='stop-color:#fff;stop-opacity:1'></stop>
                                <stop offset='0.52' style='stop-color:#ededed;stop-opacity:1'></stop>
                                <stop offset='1' style='stop-color:#b2b2b2;stop-opacity:1'></stop>
                            </lineargradient>
                            <lineargradient id='_Linear5' x1='0' y1='0' x2='1' y2='0' gradientUnits='userSpaceOnUse' gradientTransform='matrix(3.8029,-0.930653,0.930653,3.8029,32.8683,16.1073)'>
                                <stop offset='0' style='stop-color:#fff;stop-opacity:1'></stop>
                                <stop offset='0.48' style='stop-color:#ebebeb;stop-opacity:1'></stop>
                                <stop offset='1' style='stop-color:#b2b2b2;stop-opacity:1'></stop>
                            </lineargradient>
                            <lineargradient id='_Linear6' x1='0' y1='0' x2='1' y2='0' gradientUnits='userSpaceOnUse' gradientTransform='matrix(9.9501,0.104624,-0.104624,9.9501,32.9292,8.34263)'>
                                <stop offset='0' style='stop-color:#fff;stop-opacity:1'></stop>
                                <stop offset='0.56' style='stop-color:#efefef;stop-opacity:1'></stop>
                                <stop offset='1' style='stop-color:#b2b2b2;stop-opacity:1'></stop>
                            </lineargradient>
                        </defs>
                    </svg>
                </a>
                <div class="auth-form">
                    <h1 class="auth-form__title">@yield('title')</h1>
                    @yield('content')
                </div>
                @hasSection('footer')
                    <div class="l-auth__footer">
                        @yield('footer')
                    </div>
                @endif
            </div>
            <div class="l-auth__sidebar"></div>
        </div>
    </main>
    {{-- Status --}}
    {{-- Errors --}}
</body>
</html>
