<div class="editor__group">
    <button
        type="button"
        class="btn btn--light btn--sm btn--icon"
        aria-label="{{ __('Horizontal Rule') }}"
        x-on:click="editor().chain().focus().setHorizontalRule().run()"
    >
        <x-root::icon name="minus" class="btn__icon" />
    </button>
    <button
        type="button"
        class="btn btn--light btn--sm btn--icon"
        aria-label="{{ __('Blockquote') }}"
        x-bind:class="{ 'btn--primary': isActive('blockquote', {}, updatedAt), 'btn--light': ! isActive('blockquote', {}, updatedAt) }"
        x-on:click="editor().chain().focus().toggleBlockquote().run()"
    >
        <x-root::icon name="chevron-right" class="btn__icon" />
    </button>
</div>
