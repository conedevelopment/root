<template>
    <button class="btn btn--sm btn--icon btn--tertiary btn--control" type="button" @click="handle">
        <Icon name="horizontal-rule" class="btn__icon"/>
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
                this.editor.chain().focus().setHorizontalRule().run();
            },
        },
    }
</script>
