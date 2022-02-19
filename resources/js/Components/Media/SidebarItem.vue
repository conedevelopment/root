<template>
    <div class="media-accordion">
        <div class="media-accordion__heading">
            <h3 class="media-accordion__title d-flex align-items-center">
                <div class="media-accordion__image-wrapper" :class="{ 'is-loading': loading }" v-if="item.is_image">
                    <img :src="url" class="media-accordion__image" alt="" @error="reload" @load="loading = false">
                </div>
                <Icon v-else name="file" class="media-accordion__icon"></Icon>
                <span style="text-overflow: ellipsis; max-width: 190px; display: inline-block; overflow: hidden; white-space: nowrap;">
                    {{ item.file_name }}
                </span>
            </h3>
            <button
                type="button"
                class="btn btn--secondary media-remove-item"
                :aria-label="__('Remove')"
                @click="deselect"
            >
                <Icon name="close"></Icon>
            </button>
        </div>
        <div class="media-accordion__content is-open">
            <ul class="media-sidebar__list mt-3 mb-3">
                <li><strong>{{ __('Created at') }}</strong>: {{ item.created_at }}</li>
                <li><strong>{{ __('Size') }}</strong>: {{ size }}</li>
                <li v-if="dimensions">
                    <strong>{{ __('Dimensions') }}</strong>: <span v-html="dimensions"></span>
                </li>
                <!-- Pivot fields -->
            </ul>
        </div>
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

        emits: ['deselect'],

        data() {
            return {
                tries: 0,
                loading: false,
                url: this.item.urls.thumb || this.item.urls.original,
            };
        },

        computed: {
            size() {
                if (this.item.size === 0) {
                    return '1 KB';
                }

                const sizes = ['KB', 'MB', 'GB', 'TB'];

                const i = Math.floor(Math.log(this.item.size) / Math.log(1024));

                return (this.item.size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + sizes[i];
            },
            dimensions() {
                if (this.item.width && this.item.height) {
                    return `${this.item.width}&times;${this.item.height} px`;
                }

                return null;
            },
        },

        methods: {
            deselect() {
                this.$emit('deselect', this.item);
            },
            reload() {
                if (this.tries >= 5) {
                    this.loading = false;
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
