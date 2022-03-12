<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
<head>
    {{-- Meta --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Styles --}}
    <link href="{{ URL::asset('vendor/root/app.css') }}" rel="stylesheet">

    {{-- Title --}}
    <title>@yield('title') - {{ Config::get('app.name') }}</title>
</head>
<body>
    <main class="site-auth">
        <div class="site-auth__inner">
            {{-- Message --}}
            @if(Session::has('message'))
                <div role="alert">
                    {{ Session::get('message') }}
                </div>
            @endif

            {{-- Erros --}}
            @if($errors->isNotEmpty())
                <div role="alert">
                    {{ __('Error!') }}
                </div>
            @endif

            <img class="site-auth__logo" src="{{ asset('/vendor/root/root-logo-dark.svg') }}" alt="Root">

            {{-- Content --}}
            @yield('content')
        </div>
    </main>

    {{-- Icons --}}
    @include('root::icons')
</body>
</html>
