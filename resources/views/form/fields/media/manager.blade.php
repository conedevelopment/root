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
    x-on:drop.prevent="handleFiles($event.dataTransfer.files)"
>
    <x-slot:header>
        <div class="modal__filter">
            <select class="form-control">
                <option value="all">All media items</option>
                <option value="images">Images</option>
                <option value="video">Video</option>
                <option value="audio">Audio</option>
                <option value="documents">Documents</option>
            </select>
            <select class="form-control">
                <option value="all">All dates</option>
                <option value="july-2023">July 2023</option>
                <option value="juni-2023">Juni 2023</option>
                <option value="may-2023">May 2023</option>
            </select>
            <div class="search-form">
                <input class="form-control  search-form__control" type="text" placeholder="Search..." title="Search" />
                <button type="button" class="search-form__submit">
                    <span class="sr-only">Search</span>
                    <x-root::icon name="search" class="search-form__icon" />
                </button>
            </div>
        </div>
    </x-slot:header>

    <ol class="media-list" tabindex="-1" x-on:open-{{ $modalKey }}.window.once="fetch()">
        {{-- <template x-for="(item, index) in items" :key="item.id"></template> --}}
    </ol>

    <x-slot:footer class="modal__footer--space-between">
        <input
            type="file"
            class="form-file"
            accept="image/png, image/jpeg"
            multiple
            x-bind:disabled="processing"
            x-on:change="handleFiles($event.target.files)"
        >
        <div class="modal__column">
            <button type="button" class="btn btn--outline-primary">Cancel</button>
            <button type="button" class="btn btn--primary">Insert</button>
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
