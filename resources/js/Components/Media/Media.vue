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

                    </div>
                </div>
                <Toolbar></Toolbar>
            </div>
        </div>
    </div>
</template>

<script>
    import { throttle } from './../../Support/Helpers';
    import Closable from './../../Mixins/Closable';
    import Filters from './Filters.vue';
    import Item from './Item.vue';
    import Toolbar from './Toolbar.vue';
    import Uploader from './Uploader.vue';

    export default {
        components: {
            Item,
            Filters,
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
        },

        mounted() {
            //
        },

        data() {
            return {
                //
            };
        },

        methods: {
            //
        },
    }
</script>
