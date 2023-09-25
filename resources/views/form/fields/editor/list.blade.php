<div class="editor__group">
    <button
        type="button"
        class="btn btn--sm btn--icon"
        aria-label="{{ __('Bullet List') }}"
        x-bind:class="{ 'btn--primary': isActive('bulletList', {}, updatedAt), 'btn--light': ! isActive('bulletList', {}, updatedAt) }"
        x-on:click="editor().chain().focus().toggleBulletList().run()"
    >
        <x-root::icon name="format-bullet-list" class="btn__icon" />
    </button>
    <button
        type="button"
        class="btn btn--sm btn--icon"
        aria-label="{{ __('Ordered List') }}"
        x-bind:class="{ 'btn--primary': isActive('orderedList', {}, updatedAt), 'btn--light': ! isActive('orderedList', {}, updatedAt) }"
        x-on:click="editor().chain().focus().toggleOrderedList().run()"
    >
        <x-root::icon name="format-ordered-list" class="btn__icon" />
    </button>
</div>
