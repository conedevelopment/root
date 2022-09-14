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
                    <button
                        v-if="preview"
                        type="button"
                        class="modal-close btn btn--secondary btn--icon"
                        :aria-label="__('Back')"
                        @click="current = null"
                    >
                        <Icon name="arrow-back" class="btn__icon btn__icon--sm"></Icon>
                    </button>
                    <h2 class="modal-title">{{ preview ? preview.name : title }}</h2>
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
                    <Preview
                        v-if="preview !== null"
                        :modelValue="preview"
                        @update:modelValue="() => update(modelValue)"
                    ></Preview>
                    <div v-show="preview === null" class="media-item-list-wrapper">
                        <div class="media-item-list__body">
                            <Item
                                v-for="item in response.data"
                                :key="item.id"
                                :item="item"
                                :selected="selected(item)"
                                @select="select"
                                @deselect="deselect"
                                @preview="($event) => current = $event.id"
                            ></Item>
                        </div>
                    </div>
                </div>
                <Selection
                    :selection="modelValue"
                    @deselect="deselect"
                    @clear="clear"
                    @preview="($event) => current = $event.id"
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
    import Preview from './Preview.vue';
    import Selection from './Selection.vue';
    import Uploader from './Uploader.vue';

    export default {
        components: {
            Filters,
            Item,
            Preview,
            Selection,
            Uploader,
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
                response: { data: [], next_page_url: null, prev_page_url: null },
                form: this.$inertia.form({}),
                current: null,
            };
        },

        computed: {
            preview() {
                if (this.current === null) {
                    return null;
                }

                return this.modelValue.find((item) => item.id === this.current);
            },
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
                    const value = Array.from(this.modelValue);

                    value.push(item);

                    this.update(value);
                } else {
                    this.update([item]);
                }
            },
            deselect(item) {
                if (item.id === this.current) {
                    this.current = null;
                }

                const value = Array.from(this.modelValue);
                const index = value.findIndex((selected) => selected.id === item.id);

                value.splice(index, 1);

                this.update(value);
            },
            selected(item) {
                return this.modelValue.some((selected) => selected.id === item.id);
            },
            clear() {
                this.update([]);
            },
            update(value) {
                this.$emit('update:modelValue', value);
            },
        },
    }
</script>
