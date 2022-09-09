<template>
    <div class="media-item" style="cursor: pointer;" :class="classNames" @click.prevent="toggle">
        <div class="media-item__actions">
            <button type="button" class="btn btn--primary btn--icon">
                <Icon name="edit" class="btn__icon--sm"></Icon>
            </button>
            <button type="button" class="btn btn--delete btn--icon">
                <Icon name="delete" class="btn__icon--sm"></Icon>
            </button>
        </div>
        <img v-if="item.is_image" :src="url" :alt="item.name" @error="reload" @load="loading = false">
        <span v-else class="media-item__caption">
            <Icon name="description"></Icon>
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
        },

        data() {
            return {
                tries: 0,
                loading: false,
                url: this.item.urls.thumb || this.item.urls.original,
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
            selected() {
                return this.$parent.selection.some((item) => item.id === this.item.id);
            },
        },

        methods: {
            toggle() {
                if (! this.$parent.processing) {
                    this.selected ? this.$parent.deselect(this.item) : this.$parent.select(this.item);
                }
            },
            reload() {
                if (this.tries >= 5) {
                    return;
                }

                this.loading = true;

                const interval = setInterval(() => {
                    const url = new URL(this.url);
                    url.searchParams.set('key', (new Date()).getTime());

                    this.url = url.toString();
                    this.tries++;

                    clearInterval(interval);
                }, 5000);
            },
        },
    }
</script>
