<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
<head>
    {{-- Meta --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Styles --}}
    <link href="{{ URL::asset('vendor/root/favicon.png') }}" rel="icon" sizes="32x32">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&amp;family=IBM+Plex+Sans:wght@400;700&amp;family=Inter:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
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
    {{-- App --}}
    <div class="app">
        <main class="app-body">
            <div class="app-body__inner">
                {{-- @if($alerts->isNotEmpty())
                    <div class="app-alert">
                        @foreach($alerts as $alert)
                            <x-root::alert :alert="$alert"/>
                        @endforeach
                    </div>
                @endif --}}
                @yield('content')
            </div>
        </main>
        <form id="logout-form" action="/logout" method="POST" style="display: none;">
            @csrf
        </form>
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
</body>
</html>
