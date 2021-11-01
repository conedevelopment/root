<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
<head>
    {{-- Meta --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Styles --}}
    <link href="{{ URL::asset('vendor/root/app.css') }}" rel="stylesheet">

    {{-- Title --}}
    <title>Root</title>
</head>
<body>
    {{-- App --}}
    @inertia

    {{-- SVG Icons --}}
    @include('root::icons')

    {{-- Scripts --}}
    <script>window.Root = @json($root);</script>
    <script src="{{ URL::asset('vendor/root/app.js') }}" defer></script>
</body>
</html>
