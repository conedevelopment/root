<template>
    <button class="btn btn--icon" type="button" @click="handle" :class="{ 'btn--primary': isActive, 'btn--tertiary': !isActive }">
        <Icon name="insert-link" class="btn__icon"></Icon>
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
                return this.editor.isActive('a');
            },
        },
    }
</script>
