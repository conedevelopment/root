<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
<head>
    {{-- Meta --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Styles --}}
    <link rel="icon" href="{{ URL::asset('vendor/root/favicon.png') }}" sizes="32x32">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;800&family=Open+Sans:wght@400;700&display=swap">
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
    <a class="btn btn--primary skip-link" href="#content">Skip to content</a>
    <div class="l-main">
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
                    @yield('content')
                </div>
            </div>
            <x-root::layout.footer />
        </main>
    </div>

    {{-- SVG Icons --}}
    @include('root::icons')

    {{-- Scripts --}}
    {{
        Vite::withEntryPoints('resources/js/app.js')
            ->useBuildDirectory('vendor/root/build')
            ->useHotFile(public_path('vendor/root/hot'))
    }}
    @stack('scripts')

    {{-- Logout Form --}}
    <form id="logout-form" style="display:none" method="POST" action="{{ URL::route('root.auth.logout') }}">
        @csrf
    </form>
</body>
</html>
