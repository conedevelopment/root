<template>
    <div class="media-item" style="cursor: pointer;" :class="classNames" @click.prevent="select">
        <div v-if="selected" class="media-item__actions">
            <button type="button" class="btn btn--delete btn--icon" @click.stop="deselect">
                <Icon name="close" class="btn__icon--sm"/>
            </button>
        </div>
        <img v-if="item.is_image" :src="url" :alt="item.file_name" @error.once="reload" @load="loading = false">
        <span v-else class="media-item__caption">
            <Icon name="description"/>
            <span>{{ item.file_name }}</span>
        </span>
    </div>
</template>

<script>
    export default {
        props: {
            item: {
                type: Object,
                required: true,
            },
            selected: {
                type: Boolean,
                default: false,
            },
        },

        emits: ['select', 'deselect'],

        data() {
            return {
                loading: false,
                url: this.item.urls.original,
            };
        },

        computed: {
            classNames() {
                return {
                    'is-image': this.item.is_image,
                    'is-svg': this.item.mime_type.startsWith('image/svg'),
                    'is-document': ! this.item.is_image,
                    'is-selected': this.selected,
                    'is-loading': this.loading,
                };
            },
        },

        methods: {
            select() {
                if (! this.selected) {
                    this.$emit('select', this.item);
                }
            },
            deselect() {
                this.$emit('deselect', this.item);
            },
            reload() {
                this.loading = true;

                const interval = setInterval(() => {
                    const url = new URL(this.url);
                    url.searchParams.set('key', (new Date()).getTime());

                    this.url = url.toString();

                    clearInterval(interval);
                }, 10000);
            },
        },
    }
</script>
