<div class="editor__group">
    <button
        type="button"
        class="btn btn--sm btn--icon"
        aria-label="{{ __('Link') }}"
        x-bind:class="{ 'btn--primary': isActive('link', {}, updatedAt), 'btn--light': ! isActive('link', {}, updatedAt) }"
        x-on:click="_handleLink(editor())"
    >
        <x-root::icon name="format-link" class="btn__icon" />
    </button>
    <button
        type="button"
        class="btn btn--light btn--sm btn--icon"
        aria-label="{{ __('Unlink') }}"
        x-bind:disabled="! isActive('link', {}, updatedAt)"
        x-on:click="editor().chain().focus().unsetLink().run()"
    >
        <x-root::icon name="format-unlink" class="btn__icon" />
    </button>
</div>

{{-- Script --}}
@pushOnce('scripts')
    <script>
        function _handleLink(editor) {
            const previousUrl = editor.getAttributes('link').href;
            const url = window.prompt('URL', previousUrl);

            if (url === null) {
                //
            } else if (url === '') {
                editor.chain().focus().extendMarkRange('link').unsetLink().run();
            } else {
                editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
            }
        };
    </script>
@endpushOnce
