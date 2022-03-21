<template>
    <div class="modal-backdrop" :class="{ 'modal-backdrop--visible': isOpen }" @click.self="close">
        <div
            role="dialog"
            aria-modal="true"
            class="modal"
            :aria-label="title"
            :class="{ 'hidden': ! isOpen }"
        >
            <h2 class="modal__title">{{ __('Media') }}</h2>
            <div
                class="modal__inner"
                :class="{ 'has-active-dropzone': dragging }"
                :data-dropzone-text="__('Drop your files here')"
                @dragstart.prevent
                @dragend.prevent="dragging = false"
                @dragover.prevent="dragging = true"
                @dragleave.prevent="dragging = false"
                @drop.prevent="handleFiles($event.dataTransfer.files)"
            >
                <div ref="container">
                    <Filters></Filters>
                    <div
                        v-if="queue.length || response.data.length"
                        class="media-item-list-wrapper "
                        :class="{ 'is-sidebar-open': selection.length > 0 }"
                    >
                        <div class="media-item-list__body">
                            <Uploader v-for="(file, index) in queue" :key="`uploader-${index}`" :file="file"></Uploader>
                            <Item
                                v-for="(item, index) in response.data"
                                :key="`${item.file_name}-${index}`"
                                :item="item"
                            ></Item>
                        </div>
                        <div v-show="selection.length" class="media-item-list__sidebar">
                            <Sidebar></Sidebar>
                        </div>
                    </div>
                    <div v-else class="alert alert--info" role="alert">
                        {{ __('No results found.') }}
                    </div>
                </div>
                <Toolbar></Toolbar>
            </div>
        </div>
    </div>
</template>

<script>
    import Item from './Item';
    import Filters from './Filters';
    import Sidebar from './Sidebar';
    import Toolbar from './Toolbar';
    import Uploader from './Uploader';
    import Closable from './../../Mixins/Closable';
    import { throttle } from './../../Support/Helpers';

    export default {
        components: {
            Item,
            Filters,
            Sidebar,
            Toolbar,
            Uploader,
        },

        mixins: [Closable],

        props: {
            modelValue: {
                type: [Array, Object],
                default: () => [],
            },
            multiple: {
                type: Boolean,
                default: false,
            },
            url: {
                type: String,
                required: true,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        watch: {
            isOpen(newValue, oldValue) {
                document.body.classList.toggle('has-modal-open', newValue);
            },
        },

        mounted() {
            this.$dispatcher.once('open', this.fetch);

            window.addEventListener('keyup', (event) => {
                if (this.isOpen && event.code === 'Escape') {
                    this.close();
                }
            });

            this.$refs.container.addEventListener('scroll', throttle((event) => {
                if (this.shouldPaginate()) {
                    this.paginate();
                }
            }, 300));
        },

        data() {
            return {
                dragging: false,
                processing: false,
                query: {},
                queue: [],
                response: { data: [], next_page_url: null, prev_page_url: null },
            };
        },

        computed: {
            selection: {
                get() {
                    return Array.from(this.modelValue || []);
                },
                set(value) {
                    this.$emit('update:modelValue', value);
                },
            },
        },

        methods: {
            fetch() {
                this.processing = true;

                this.$http.get(this.url, { params: this.query }).then((response) => {
                    this.response = response.data;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
            paginate() {
                this.processing = true;

                this.$http.get(this.response.next_page_url).then((response) => {
                    this.response.data.push(...response.data.data);
                    this.response.next_page_url = response.data.next_page_url;
                    this.response.prev_page_url = response.data.prev_page_url;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
            handleFiles(files) {
                this.dragging = false;

                for (let i = 0; i < files.length; i++) {
                    this.queue.unshift(files.item(i));
                }
            },
            shouldPaginate() {
                const el = this.$refs.container;

                return ! this.processing
                    && this.response.next_page_url !== null
                    && this.response.data.length > 0
                    && (el.scrollHeight - el.scrollTop - el.clientHeight) < 1;
            },
        },
    }
</script>
