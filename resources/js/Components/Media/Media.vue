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
                        class="modal-close btn btn--secondary btn--icon"
                        :aria-label="__('Close modal')"
                        @click="close"
                    >
                        <Icon name="close" class="btn__icon btn__icon--sm"/>
                    </button>
                </div>
                <div
                    ref="container"
                    class="modal-body modal-body--media"
                    :class="{ 'has-active-dropzone': dragging }"
                    :data-dropzone-text="__('Drop your files here')"
                    @dragstart.prevent
                    @dragend.prevent="dragging = false"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="handleFiles($event.dataTransfer.files)"
                >
                    <Filters :query="query" :filters="filters" @update:query="fetch"/>
                    <div class="media-item-list-wrapper" :class="{ 'is-sidebar-open': value.length > 0 }">
                        <div class="media-item-list__body">
                            <Queue ref="queue" :url="url" @processed="handleProcessed"/>
                            <Item
                                v-for="item in response.data"
                                :key="item.id"
                                :item="item"
                                :selected="selected(item)"
                                @select="select"
                                @deselect="deselect"
                            />
                        </div>
                        <div v-show="value.length > 0" class="media-item-list__sidebar">
                            <Selection v-model:selection="value" @deselect="deselect" @clear="clear"/>
                        </div>
                    </div>
                </div>
                <Toolbar @upload="handleFiles" @update="update"/>
            </div>
        </div>
    </div>
</template>

<script>
    import { throttle } from './../../Support/Helpers';
    import Closable from './../../Mixins/Closable';
    import Filters from './Filters.vue';
    import Item from './Item.vue';
    import Queue from './Queue.vue';
    import Toolbar from './Toolbar.vue';
    import Selection from './Selection.vue';

    export default {
        components: {
            Filters,
            Item,
            Queue,
            Toolbar,
            Selection,
        },

        mixins: [Closable],

        props: {
            modelValue: {
                type: Array,
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
            filters: {
                type: Array,
                default: () => [],
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue', 'change'],

        watch: {
            isOpen(newValue, oldValue) {
                document.body.classList.toggle('has-modal-open', newValue);
            },
            value: {
                handler(newValue, oldValue) {
                    this.$emit('change', newValue);
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
                query: this.$inertia.form(
                    this.filters.reduce((value, filter) => ({...value, [filter.key]: filter.default}), {})
                ),
                processing: false,
                response: { data: [], next_page_url: null, prev_page_url: null },
                value: Array.from(this.modelValue),
            };
        },

        methods: {
            fetch() {
                this.processing = true;
                this.query.processing = true;

                this.$http.get(this.url, {
                    params: this.query.data(),
                }).then((response) => {
                    this.response = response.data;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                    this.query.processing = false;
                });
            },
            paginate() {
                this.processing = true;
                this.query.processing = true;

                this.$http.get(this.response.next_page_url).then((response) => {
                    this.response.data.push(...response.data.data);
                    this.response.next_page_url = response.data.next_page_url;
                    this.response.prev_page_url = response.data.prev_page_url;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                    this.query.processing = false;
                });
            },
            handleFiles(files) {
                this.dragging = false;

                for (let i = 0; i < files.length; i++) {
                    this.$refs.queue.push(files.item(i));
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
                    this.value.push(item);
                } else {
                    this.value = [item];
                }
            },
            deselect(item) {
                this.value.splice(
                    this.value.findIndex((selected) => selected.id === item.id), 1
                );
            },
            selected(item) {
                return this.value.some((selected) => selected.id === item.id);
            },
            clear() {
                this.value = [];
            },
            update() {
                this.$emit('update:modelValue', this.value);
            },
            handleProcessed(item) {
                this.response.total++;
                this.response.data.unshift(item);
            },
        },
    }
</script>
