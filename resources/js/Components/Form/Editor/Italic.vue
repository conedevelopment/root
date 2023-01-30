<template>
    <button
        class="btn btn--sm btn--icon"
        type="button"
        :class="{ 'btn--primary': isActive, 'btn--tertiary': ! isActive }"
        @click="handle"
    >
        <Icon name="format-italic" class="btn__icon btn__icon--sm"></Icon>
    </button>
</template>

<script>
    export default {
        props: {
            editor: {
                type: Object,
                required: true,
            },
        },

        methods: {
            handle() {
                this.editor.chain().focus().toggleItalic().run();
            },
        },

        computed: {
            isActive() {
                return this.editor.isActive('italic');
            },
        },
    }
</script>
