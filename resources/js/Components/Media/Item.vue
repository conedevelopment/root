<template>
    <div class="media-item" style="cursor: pointer;" :class="classNames" @click.prevent="toggle">
        <img v-if="item.is_image" :src="url" :alt="item.name" @error="reload" @load="loading = false">
        <span v-else class="media-item__caption">
            <Icon name="file"></Icon>
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
            select() {
                if (this.$parent.multiple) {
                    this.$parent.selection.push(this.item)
                } else {
                    this.$parent.selection = [this.item];
                }
            },
            deselect() {
                const index = this.$parent.selection.findIndex((item) => item.id === this.item.id);

                this.$parent.selection.splice(index, 1);
            },
            toggle() {
                if (! this.$parent.processing) {
                    this.selected ? this.deselect() : this.select();
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
