<div class="editor__group">
    <button
        type="button"
        class="btn btn--sm btn--icon"
        aria-label="{{ __('Bold') }}"
        x-bind:class="{ 'btn--primary': isActive('bold', updatedAt), 'btn--light': ! isActive('bold', updatedAt) }"
        x-on:click="editor().chain().focus().toggleBold().run()"
    >
        <x-root::icon name="format-bold" class="btn__icon" />
    </button>
    <button type="button" class="btn btn--light btn--sm btn--icon" title="{{ __('Italic') }}">
        <x-root::icon name="format-italic" class="btn__icon" />
    </button>
    <button type="button" class="btn btn--light btn--sm btn--icon" title="{{ __('Strikethrough') }}">
        <x-root::icon name="format-strike" class="btn__icon" />
    </button>
    <button type="button" class="btn btn--light btn--sm btn--icon" title="{{ __('Clear') }}">
        <x-root::icon name="format-clear" class="btn__icon" />
    </button>
</div>
