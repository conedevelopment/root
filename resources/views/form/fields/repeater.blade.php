<div class="form-group" x-data="repeater('{{ $url }}', {{ json_encode($options) }})">
    <span class="form-label">{{ $label }}</span>
    <div class="repeater-container">
        <template x-for="(option, index) in options">
            <div x-html="option.html"></div>
        </template>
    </div>
    <div class="btn-dropdown">
        <button
            type="button"
            class="btn btn--primary btn--icon"
            x-bind:disabled="processing || options.length >= {{ $max }}"
            x-on:click="add"
        >
            {{ $addNewLabel }}
            <x-root::icon name="plus" class="btn__icon" />
        </button>
    </div>
</div>

{{-- Script --}}
@pushOnce('scripts')
    {{
        Vite::withEntryPoints('resources/js/repeater.js')
            ->useBuildDirectory('vendor/root/build')
            ->useHotFile(public_path('vendor/root/hot'))
    }}
@endpushOnce
