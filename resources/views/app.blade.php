<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
<head>
    {{-- Meta --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Styles --}}
    <link href="{{ URL::asset('vendor/root/app.css') }}" rel="stylesheet">
    @foreach (Cone\Root\Support\Facades\Asset::styles() as $key => $style)
        <link id="{{ $key }}" href="{{ $style->getUrl() }}" rel="stylesheet">
    @endforeach

    {{-- Title --}}
    <title>Root</title>
</head>
<body>
    {{-- App --}}
    @inertia

    {{-- SVG Icons --}}
    @include('root::icons')

    {{-- Scripts --}}
    <script>
        window.Root = @json($root);
    </script>
    @foreach (Cone\Root\Support\Facades\Asset::scripts() as $key => $script)
        <script id="script-{{ $key }}" src="{{ $script->getUrl() }}"></script>
    @endforeach
    <script src="{{ URL::asset('vendor/root/app.js') }}" defer></script>
</body>
</html>
