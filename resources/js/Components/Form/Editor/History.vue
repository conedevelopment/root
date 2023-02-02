<template>
    <div class="tiptap__group">
        <button
            class="btn btn--icon btn--tertiary btn--control"
            type="button"
            :disabled="! editor.can().undo()"
            @click="editor.chain().focus().undo().run()"
        >
            <Icon name="undo" class="btn__icon btn__icon--md"/>
        </button>
        <button
            class="btn btn--icon btn--tertiary btn--control"
            type="button"
            :disabled="! editor.can().redo()"
            @click="editor.chain().focus().redo().run()"
        >
            <Icon name="redo" class="btn__icon btn__icon--md"/>
        </button>
    </div>
</template>

<script>
    export default {
        props: {
            editor: {
                type: Object,
                required: true,
            },
        },
    }
</script>
