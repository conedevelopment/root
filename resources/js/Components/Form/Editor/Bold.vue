<template>
    <button type="button" @click="handle" :class="{ 'is-active': isActive }">
        B
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
                this.editor.chain().focus().toggleBold().run();
            },
        },

        computed: {
            isActive() {
                return this.editor.isActive('bold');
            },
        },
    }
</script>
