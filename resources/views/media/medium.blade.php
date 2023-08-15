<li
    class="media-item"
    role="checkbox"
    tabindex="0"
    x-bind:aria-checked="item.selected"
    x-on:keydown.enter.prevent="toggle(item)"
    x-on:keydown.space.prevent="toggle(item)"
    x-on:click.prevent="toggle(item)"
>
    <span x-show="item.selected" class="media-item__selected">
        <x-root::icon name="check" />
    </span>
    <template x-if="item.processing">
        <span class="media-item__background">
            <span class="preloader--circle media-item__icon"></span>
            <span x-text="item.name" class="media-item__name"></span>
        </span>
    </template>
    <template x-if="! item.processing && item.is_image">
        <img x-bind:src="item.preview_url" x-bind:alt="item.label">
    </template>
    <template x-if="! item.processing && ! item.is_image">
        <span class="media-item__background">
            <x-root::icon name="document" class="media-item__icon" />
            <span x-text="item.label" class="media-item__name"></span>
        </span>
    </template>
</li>
