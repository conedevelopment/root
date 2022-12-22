<template>
    <button type="button" @click="handle" :class="{ 'is-active': isActive }">
        I
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
