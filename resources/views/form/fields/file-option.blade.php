<li class="file-list-item">
    <div class="file-list-item__column">
        <img class="file-list-item__thumbnail" src="https://picsum.photos/80/80" x-bind:alt="item.label">
        <span x-bind:id="item.uuid" class="file-list-item__name" x-text="item.label"></span>
        <input type="hidden" x-bind:name="item.attrs.name" x-bind:value="item.value">
    </div>
    <div class="file-list-item__actions">
        <button
            type="button"
            class="btn btn--delete btn--sm btn--icon"
            aria-label="{{ __('Remove') }}"
            x-bind:aria-describedby="item.uuid"
        >
            <x-root::icon name="close" class="btn__icon" />
        </button>
    </div>
</li>
