<template>
    <div>
        <button class="btn btn--control btn--icon btn--tertiary" type="button" @click="$refs.media.open">
            <Icon name="perm-media" class="btn__icon btn__icon--sm"/>
        </button>
        <Media
            ref="media"
            multiple
            :url="url"
            :title="__('Media')"
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
            url: {
                type: String,
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
                this.$refs.media.close();
            },
        },
    }
</script>
