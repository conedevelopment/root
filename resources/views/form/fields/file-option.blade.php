<li class="file-list-item">
    <div class="file-list-item__column">
        <template x-if="item.is_image">
            <img class="file-list-item__thumbnail" x-bind:src="item.preview_url" x-bind:alt="item.label">
        </template>
        <template x-if="! item.is_image">
            <span class="file-list-item__icon">
                <x-root::icon name="document" class="media-item__icon" />
            </span>
        </template>
        <span x-bind:id="item.uuid" class="file-list-item__name" x-text="item.label"></span>
        <input type="hidden" x-bind:name="item.attrs.name" x-bind:value="item.value">
    </div>
    <div class="file-list-item__actions">
        <button
            type="button"
            class="btn btn--light btn--sm btn--icon"
            aria-label="{{ __('Edit') }}"
            x-bind:aria-describedby="item.uuid"
        >
            <x-root::icon name="edit" class="btn__icon" />
        </a>
        <button
            type="button"
            class="btn btn--delete btn--sm btn--icon"
            aria-label="{{ __('Remove') }}"
            x-bind:aria-describedby="item.uuid"
            x-on:click="() => { selection.splice(index, 1); item.selected = false; }"
        >
            <x-root::icon name="close" class="btn__icon" />
        </button>
    </div>
</li>
