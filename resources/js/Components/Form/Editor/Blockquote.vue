<template>
    <button
        class="btn btn--icon"
        type="button"
        :class="{ 'btn--primary': isActive, 'btn--tertiary': ! isActive }"
        @click="handle"
    >
        <Icon name="format-quote" class="btn__icon btn__icon--sm"/>
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
                this.editor.chain().focus().toggleBlockquote().run();
            },
        },

        computed: {
            isActive() {
                return this.editor.isActive('blockquote');
            },
        },
    }
</script>
