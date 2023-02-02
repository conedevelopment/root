<template>
    <button
        class="btn btn--icon btn--control"
        type="button"
        :class="{ 'btn--primary': isActive, 'btn--tertiary': ! isActive }"
        @click="handle"
    >
        <Icon name="format-ink-highlighter" class="btn__icon n btn__icon--md"/>
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
                this.editor.chain().focus().toggleHighlight().run();
            },
        },

        computed: {
            isActive() {
                return this.editor.isActive('highlight');
            },
        },
    }
</script>
