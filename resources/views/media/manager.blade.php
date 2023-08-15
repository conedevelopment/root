<x-root::modal
    :title="$label"
    :key="$modalKey"
    class="modal--media"
    data-dropzone="{{ __('Drag & drop your images and files here') }}"
    x-data="mediaManager('{{ $url }}', {{ json_encode($config) }})"
    x-bind:class="{ 'modal--dropzone': dragging }"
    x-on:dragstart.prevent=""
    x-on:dragend.prevent="dragging = false"
    x-on:dragover.prevent="dragging = true"
    x-on:dragleave.prevent="dragging = false"
    x-on:drop.prevent="($event) => { queueFiles($event.dataTransfer.files); dragging = false; }"
>
    <x-slot:header>
        @include('root::media.filters')
    </x-slot:header>
    <template x-if="items.length === 0">
        <x-root::alert type="info">
            {{ __('No Media items are found!') }}
        </x-root::alert>
    </template>
    <ol
        class="media-list"
        tabindex="-1"
        x-on:open-{{ $modalKey }}.window.once="fetch()"
    >
        <template x-for="item in queue.reverse()" :key="item.hash">
            @include('root::media.queued-medium')
        </template>
        <template x-for="item in items" :key="item.uuid">
            @include('root::media.medium')
        </template>
    </ol>
    <x-slot:footer class="modal__footer--space-between">
        <input
            type="file"
            class="form-file"
            accept="{{ $config['accept'] }}"
            multiple
            x-bind:disabled="processing"
            x-on:change="queueFiles($event.target.files)"
        >
        <div class="modal__column">
            <button type="button" class="btn btn--outline-primary">
                {{ __('Cancel') }}
            </button>
            <button type="button" class="btn btn--primary">
                {{ __('Select') }}
            </button>
        </div>
    </x-slot:footer>
</x-root::modal>

{{-- Script --}}
@pushOnce('scripts')
    {{
        Vite::withEntryPoints('resources/js/media-manager.js')
            ->useBuildDirectory('vendor/root/build')
            ->useHotFile(public_path('vendor/root/hot'))
    }}
@endpushOnce
