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
                        <Icon name="close" class="btn__icon btn__icon--sm"></Icon>
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
                    <div class="media-item-list-wrapper">
                        <div class="media-item-list__body">
                            <Item
                                v-for="item in response.data"
                                :key="item.id"
                                :item="item"
                                :selected="selected(item)"
                                @select="select"
                                @deselect="deselect"
                            ></Item>
                        </div>
                    </div>
                </div>
                <Selection
                    :selection="selection"
                    @deselect="deselect"
                    @clear="clear"
                ></Selection>
            </div>
        </div>
    </div>
</template>

<script>
    import { throttle } from './../../Support/Helpers';
    import Closable from './../../Mixins/Closable';
    import Filters from './Filters.vue';
    import Item from './Item.vue';
    import Selection from './Selection.vue';
    import Uploader from './Uploader.vue';

    export default {
        components: {
            Item,
            Filters,
            Selection,
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
        },

        mounted() {
            this.$dispatcher.once('open', this.fetch);
        },

        data() {
            return {
                dragging: false,
                processing: false,
                queue: [],
                selection: [],
                response: { data: [], next_page_url: null, prev_page_url: null },
                form: this.$inertia.form({}),
                editing: null,
            };
        },

        methods: {
            fetch() {
                this.processing = true;

                this.$http.get(this.url, {
                    params: {},
                }).then((response) => {
                    this.response = response.data;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
            select(item) {
                if (this.multiple) {
                    this.selection.push(item);
                } else {
                    this.selection = [item];
                }
            },
            deselect(item) {
                const index = this.selection.findIndex((selected) => selected.id === item.id);

                this.selection.splice(index, 1);
            },
            selected(item) {
                return this.selection.some((selected) => selected.id === item.id);
            },
            clear() {
                this.selection = [];
            },
        },
    }
</script>
