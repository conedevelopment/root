<template>
    <button
        class="btn btn--sm btn--icon btn--control"
        type="button"
        :class="{ 'btn--primary': isActive, 'btn--tertiary': ! isActive }"
        @click="handle"
    >
        <Icon name="format-list-numbered" class="btn__icon btn__icon--sm"/>
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
                this.editor.chain().focus().toggleOrderedList().run();
            },
        },

        computed: {
            isActive() {
                return this.editor.isActive('orderedList');
            },
        },
    }
</script>
