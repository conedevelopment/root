<div class="app-card" x-data="table()">
    <div class="app-card__header">
        <h2 class="app-card__title">{{ $title }}</h2>
        <div class="app-card__actions">
            @include('root::table.filters')
        </div>
    </div>
    @include('root::table.body')
</div>

{{-- Script --}}
@pushOnce('scripts')
    {{
        Vite::withEntryPoints('resources/js/table.js')
            ->useBuildDirectory('vendor/root/build')
            ->useHotFile(public_path('vendor/root/hot'))
    }}
@endpushOnce
