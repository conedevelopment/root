<template>
    <div class="tiptap__group">
        <button
            class="btn btn--icon btn--control"
            type="button"
            :class="{ 'btn--primary': isActive, 'btn--tertiary': ! isActive }"
            @click="handle"
        >
            <Icon name="insert-link" class="btn__icon"/>
        </button>
        <button
            class="btn btn--icon btn--tertiary btn--control"
            type="button"
            :disabled="! isActive"
            @click="editor.chain().focus().unsetLink().run()"
        >
            <Icon name="remove-link" class="btn__icon"/>
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

        methods: {
            handle() {
                const previousUrl = this.editor.getAttributes('link').href;
                const url = window.prompt('URL', previousUrl);

                if (url === null) {
                    return;
                }

                if (url === '') {
                    this.editor
                        .chain()
                        .focus()
                        .extendMarkRange('link')
                        .unsetLink()
                        .run();

                    return;
                }

                this.editor
                    .chain()
                    .focus()
                    .extendMarkRange('link')
                    .setLink({ href: url })
                    .run();
            },
        },

        computed: {
            isActive() {
                return this.editor.isActive('link');
            },
        },
    }
</script>
