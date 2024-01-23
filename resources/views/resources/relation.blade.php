<turbo-frame id="relation-{{ $attribute }}">
    <div class="app-card" x-data="table({ models: {{ $data->pluck('id')->toJson() }} })">
        <div class="app-card__header">
            <h2 class="app-card__title">
                <a href="{{ $url }}" data-turbo-frame="_top">{{ $title }}</a>
            </h2>
            <div class="app-card__actions">
                @if($abilities['create'])
                    <a href="{{ $url }}/create" class="btn btn--primary btn--icon" data-turbo-frame="_top">
                        <x-root::icon name="plus" class="btn__icon" />
                        {{ __('Add :resource', ['resource' => $modelName]) }}
                    </a>
                @endif
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
</turbo-frame>
