<li
    class="media-item"
    role="checkbox"
    tabindex="0"
    x-bind:aria-checked="item.selected"
    x-on:keydown.enter.prevent="item.selected = ! item.selected"
    x-on:keydown.space.prevent="item.selected = ! item.selected"
    x-on:click.prevent="item.selected = ! item.selected"
>
    <span x-show="item.selected" class="media-item__selected">
        <x-root::icon name="check" />
    </span>

    <template x-if="item.type === 'processing' || item.type === 'uploading'">
        <span class="media-item__background">
            <span class="preloader--circle media-item__icon"></span>
            <span x-text="item.name" class="media-item__name"></span>
        </span>
    </template>

    <template x-if="item.type === 'image'">
        <img :src=`${item.thumbnail}?random=${index}` :alt="item.name">
    </template>

    <template x-if="item.type === 'document'">
        <span class="media-item__background">
            <x-root::icon name="archive" class="media-item__icon" />
            <span x-text="item.name" class="media-item__name"></span>
        </span>
    </template>
</li>
