<turbo-frame id="widget-{{ $key }}">
    <div {{ $attrs }}>
        <div class="app-widget__column">
            <h2 class="app-widget__title">{{ $name }}</h2>
            <p class="app-widget__data">{{ __('Loading') }}...</p>
        </div>
    </div>
</turbo-frame>

{{-- Script --}}
@pushOnce('scripts')
    {{
        Vite::withEntryPoints('resources/js/chart.js')
            ->useBuildDirectory('vendor/root/build')
            ->useHotFile(public_path('vendor/root/hot'))
    }}
@endpushOnce
