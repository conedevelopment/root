<div class="editor__group">
    <button
        type="button"
        class="btn btn--sm btn--icon"
        aria-label="{{ __('Align Left') }}"
        x-bind:class="{
            'btn--primary': isActive({ textAlign: 'left' }, {}, updatedAt),
            'btn--light': ! isActive({ textAlign: 'left' }, {}, updatedAt)
        }"
        x-on:click="() => {
            isActive({ textAlign: 'left' }, {}, updatedAt)
                ? editor().chain().focus().unsetTextAlign().run()
                : editor().chain().focus().setTextAlign('left').run()
        }"
    >
        <x-root::icon name="format-align-left" class="btn__icon" />
    </button>
    <button
        type="button"
        class="btn btn--sm btn--icon"
        aria-label="{{ __('Align Center') }}"
        x-bind:class="{
            'btn--primary': isActive({ textAlign: 'center' }, {}, updatedAt),
            'btn--light': ! isActive({ textAlign: 'center' }, {}, updatedAt)
        }"
        x-on:click="() => {
            isActive({ textAlign: 'center' }, {}, updatedAt)
                ? editor().chain().focus().unsetTextAlign().run()
                : editor().chain().focus().setTextAlign('center').run()
        }"
    >
        <x-root::icon name="format-align-center" class="btn__icon" />
    </button>
    <button
        type="button"
        class="btn btn--sm btn--icon"
        aria-label="{{ __('Align Right') }}"
        x-bind:class="{
            'btn--primary': isActive({ textAlign: 'right' }, {}, updatedAt),
            'btn--light': ! isActive({ textAlign: 'right' }, {}, updatedAt)
        }"
        x-on:click="() => {
            isActive({ textAlign: 'right' }, {}, updatedAt)
                ? editor().chain().focus().unsetTextAlign().run()
                : editor().chain().focus().setTextAlign('right').run()
        }"
    >
        <x-root::icon name="format-align-right" class="btn__icon" />
    </button>
    <button
        type="button"
        class="btn btn--light btn--sm btn--icon"
        aria-label="{{ __('Align Justify') }}"
        x-bind:class="{
            'btn--primary': isActive({ textAlign: 'justify' }, {}, updatedAt),
            'btn--light': ! isActive({ textAlign: 'justify' }, {}, updatedAt)
        }"
        x-on:click="() => {
            isActive({ textAlign: 'justify' }, {}, updatedAt)
                ? editor().chain().focus().unsetTextAlign().run()
                : editor().chain().focus().setTextAlign('justify').run()
        }"
    >
        <x-root::icon name="format-align-justify" class="btn__icon" />
    </button>
</div>
