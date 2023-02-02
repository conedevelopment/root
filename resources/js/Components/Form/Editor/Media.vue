<template>
    <div>
        <button class="btn btn--control btn--icon btn--tertiary" type="button" @click="$refs.media.open">
            <Icon name="perm-media" class="btn__icon btn__icon--md"/>
        </button>
        <Media
            ref="media"
            :url="config.url"
            :title="config.label"
            :filters="config.filters"
            :multiple="config.multiple"
            :modelValue="selection"
            @update:modelValue="handle"
        ></Media>
    </div>
</template>

<script>
    import Media from './../../Media/Media.vue';

    export default {
        components: {
            Media,
        },

        props: {
            config: {
                type: Object,
                required: true,
            },
            editor: {
                type: Object,
                required: true,
            },
        },

        data() {
            return {
                selection: [],
            };
        },

        methods: {
            handle(values) {
                values.forEach((value) => {
                    if (value.is_image) {
                        this.editor.chain().focus().setImage({ src: value.urls.original }).run();
                    } else {
                        this.editor.commands.insertContent(`<a href="${value.urls.original}">${value.file_name}</a>`);
                    }
                });

                this.selection = [];
                this.$refs.media.close();
            },
        },
    }
</script>
