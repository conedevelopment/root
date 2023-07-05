<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ Vite::asset('resources/js/app.css', 'vendor/root/build') }}">

    <title>@yield('title') - {{ Config::get('app.name') }}</title>
</head>
<body>
    <a class="btn btn--primary skip-link" href="#content">{{ __('Skip to content') }}</a>
    <main id="content" class="l-auth">
        <div class="l-auth__inner">
            <div class="l-auth__form">
                <a class="l-auth__logo" href="#" aria-label="{{ Config::get('app.name') }}">
                    <img src="{{ URL::asset('vendor/root/img/root-logo.svg') }}" alt="">
                </a>
                <div class="auth-form">
                    <h1 class="auth-form__title">@yield('title')</h1>
                    @if(Session::has('status'))
                        <x-root::alert type="success">
                            {{ Session::get('status') }}
                        </x-root::alert>
                    @endif

                    @if($errors->isNotEmpty())
                        <x-root::alert type="danger">
                            {{ __('Some error occured when submitting the form!') }}
                        </x-root::alert>
                    @endif

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
</body>
</html>
