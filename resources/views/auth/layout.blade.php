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
            <img class="site-auth__logo" src="{{ Config::get('root.branding.logo') }}" alt="">

            {{-- Message --}}
            @if(Session::has('message'))
                <div role="alert" class="alert alert--danger">
                    {{ Session::get('message') }}
                </div>
            @endif

            {{-- Errors --}}
            @if($errors->isNotEmpty())
                <div role="alert" class="alert alert--danger">
                    {{ __('Error!') }}
                </div>
            @endif

            <div class="site-auth__panel">
                {{-- Content --}}
                @yield('content')
            </div>
        </div>
    </main>
</body>
</html>
