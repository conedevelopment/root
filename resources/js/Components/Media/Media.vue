<template>
    <div class="modal-backdrop" :class="{ 'modal-backdrop--visible': isOpen }" @click.self="close">
        <div
            role="dialog"
            aria-modal="true"
            class="modal modal--media"
            :aria-label="title"
            :class="{ 'hidden': ! isOpen }"
        >
            <div class="modal-inner">
                <div class="modal-header">
                    <h2 class="modal-title">{{ title }}</h2>
                    <button
                        type="button"
                        class="modal-close btn btn--secondary btn--sm btn--icon"
                        :aria-label="__('Close modal')"
                        @click="close"
                    >
                        <Icon name="close" class="btn__icon"></Icon>
                    </button>
                </div>
                <div
                    class="modal-body modal-body--media"
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
                                <Uploader
                                    v-for="(file, index) in queue"
                                    :key="`uploader-${index}`"
                                    :file="file"
                                    :url="url"
                                ></Uploader>
                                <Item
                                    v-for="(item, index) in response.data"
                                    :key="`${item.file_name}-${index}`"
                                    :item="item"
                                ></Item>
                            </div>
                            <div v-show="selection.length" class="media-item-list__sidebar">
                                <Sidebar :items="selection"></Sidebar>
                            </div>
                        </div>
                        <div v-else class="alert alert--info" role="alert">
                            {{ __('No results found.') }}
                        </div>
                    </div>
                </div>
                <Toolbar></Toolbar>
            </div>
        </div>
    </div>
</template>

<script>
    import Item from './Item.vue';
    import Filters from './Filters.vue';
    import Sidebar from './Sidebar.vue';
    import Toolbar from './Toolbar.vue';
    import Uploader from './Uploader.vue';
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
            title: {
                type: String,
                default: function () {
                    return this.__('Media');
                },
            },
            selectResolver: {
                type: Function,
                default: (value, options) => value,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        watch: {
            isOpen(newValue, oldValue) {
                document.body.classList.toggle('has-modal-open', newValue);
            },
            query: {
                handler(newValue, oldValue) {
                    this.fetch();
                },
                deep: true,
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
                query: {
                    type: null,
                    search: null,
                },
                queue: [],
                response: { data: [], next_page_url: null, prev_page_url: null },
                selection: [],
                value: Object.assign({}, JSON.parse(JSON.stringify(this.modelValue))),
            };
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
            select(item) {
                if (this.multiple) {
                    this.selection.push(item);
                } else {
                    this.value = {};
                    this.selection = [item];
                }

                this.value = Object.assign(this.value, {
                    [item.id]: item.fields.reduce((pivotValues, field) => {
                        return Object.assign(pivotValues, { [field.name]: field.value });
                    }, {}),
                });
            },
            deselect(item) {
                const index = this.selection.findIndex((selected) => selected.id === item.id);

                this.selection.splice(index, 1);

                delete this.value[item.id];
            },
            updateSelection() {
                this.update();
                this.close();
            },
            update() {
                this.$emit(
                    'update:modelValue',
                    this.selectResolver(this.value, this.selection)
                );
            },
            clearSelection() {
                this.value = {};
                this.selection = [];
                this.$emit('update:modelValue', this.value);
            },
        },
    }
</script>
