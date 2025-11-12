<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}" data-theme-mode="{{ Cookie::get('__root_theme', 'light') }}">
<head>
    {{-- Meta --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="turbo-prefetch" content="false">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Styles --}}
    <link rel="icon" href="{{ URL::asset('vendor/root/favicon.png') }}" sizes="32x32">
    <link rel="preload" as="font" type="font/woff2" href="{{ URL::asset('vendor/root/fonts/manrope-v14-latin-regular.woff2') }}" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="{{ URL::asset('vendor/root/fonts/manrope-v14-latin-500.woff2') }}" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="{{ URL::asset('vendor/root/fonts/manrope-v14-latin-600.woff2') }}" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="{{ URL::asset('vendor/root/fonts/manrope-v14-latin-800.woff2') }}" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="{{ URL::asset('vendor/root/fonts/open-sans-v35-latin-regular.woff2') }}" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="{{ URL::asset('vendor/root/fonts/open-sans-v35-latin-700.woff2') }}" crossorigin>
    {{
        Vite::withEntryPoints('resources/sass/app.scss')
            ->useBuildDirectory('vendor/root/build')
            ->useHotFile(public_path('vendor/root/hot'))
    }}
    @stack('styles')

    {{-- Title --}}
    <title>{{ Config::get('app.name') }}</title>
</head>
<body>
    <a class="btn btn--primary skip-link" href="#content">{{ __('Skip to content') }}</a>
    <div class="l-main" x-data="{ sidebarOpen: false }">
        <x-root::layout.sidebar />
        <main id="content" class="l-main__body" data-item="body">
            <x-root::layout.header />
            <div class="app-heading">
                <div class="container">
                    <div class="app-heading__inner">
                        <div class="app-heading__caption">
                            <h1 class="app-heading__title">@yield('title')</h1>
                            @hasSection('subtitle')
                                <div class="app-heading__description">
                                    <p>@yield('subtitle')</p>
                                </div>
                            @endif
                        </div>
                        @hasSection('actions')
                            <div class="app-heading__actions">
                                @yield('actions')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="app-body">
                    @if($errors->isNotEmpty())
                        <x-root::alert type="danger">
                            {{ __('Some error occurred when submitting the form!') }}
                        </x-root::alert>
                    @endif
                    @foreach($alerts as $alert)
                        <x-root::alert :type="$alert['type']">
                            {!! $alert['message'] !!}
                        </x-root::alert>
                    @endforeach
                    @yield('content')
                </div>
            </div>
            <x-root::layout.footer />
        </main>
    </div>

    {{-- Modals --}}
    <div id="modals"></div>

    {{-- Scripts --}}
    {{
        Vite::withEntryPoints('resources/js/app.js')
            ->useBuildDirectory('vendor/root/build')
            ->useHotFile(public_path('vendor/root/hot'))
    }}
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.Alpine.start();
        });
    </script>
    {{-- Logout Form --}}
    <form id="logout-form" style="display:none" method="POST" action="{{ URL::route('root.auth.logout') }}" data-turbo="false">
        @csrf
    </form>
</body>
</html>
