<template>
    <button type="button" @click="handle" :class="{ 'is-active': isActive }">
        Link
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
