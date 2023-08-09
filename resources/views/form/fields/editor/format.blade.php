<div class="editor__group">
    <button
        type="button"
        class="btn btn--sm btn--icon"
        aria-label="{{ __('Bold') }}"
        x-bind:class="{ 'btn--primary': isActive('bold', {}, updatedAt), 'btn--light': ! isActive('bold', {}, updatedAt) }"
        x-on:click="editor().chain().focus().toggleBold().run()"
    >
        <x-root::icon name="format-bold" class="btn__icon" />
    </button>
    <button
        type="button"
        class="btn btn--sm btn--icon"
        aria-label="{{ __('Italic') }}"
        x-bind:class="{ 'btn--primary': isActive('italic', {}, updatedAt), 'btn--light': ! isActive('italic', {}, updatedAt) }"
        x-on:click="editor().chain().focus().toggleItalic().run()"
    >
        <x-root::icon name="format-italic" class="btn__icon" />
    </button>
    <button
        type="button"
        class="btn btn--sm btn--icon"
        aria-label="{{ __('Strikethrough') }}"
        x-bind:class="{ 'btn--primary': isActive('strike', {}, updatedAt), 'btn--light': ! isActive('strike', {}, updatedAt) }"
        x-on:click="editor().chain().focus().toggleStrike().run()"
    >
        <x-root::icon name="format-strike" class="btn__icon" />
    </button>
    <button
        type="button"
        class="btn btn--light btn--sm btn--icon"
        aria-label="{{ __('Clear') }}"
        x-on:click="editor().chain().focus().clearNodes().unsetAllMarks().run()"
    >
        <x-root::icon name="format-clear" class="btn__icon" />
    </button>
</div>
