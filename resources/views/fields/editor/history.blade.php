<div class="editor__group">
    <button
        type="button"
        class="btn btn--light btn--sm btn--icon"
        aria-label="{{ __('Undo') }}"
        x-bind:disabled="(() => ! editor().can().undo())(updatedAt)"
        x-on:click="editor().chain().focus().undo().run()"
    >
        <x-root::icon name="history-undo" class="btn__icon" />
    </button>
    <button
        type="button"
        class="btn btn--light btn--sm btn--icon"
        aria-label="{{ __('Redo') }}"
        x-bind:disabled="(() => ! editor().can().redo())(updatedAt)"
        x-on:click="editor().chain().focus().redo().run()"
    >
        <x-root::icon name="history-do" class="btn__icon" />
    </button>
</div>
