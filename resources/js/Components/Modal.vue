<template>
    <div class="modal-backdrop" :class="{ 'modal-backdrop--visible': isOpen }" @click.self="close">
        <div
            role="dialog"
            aria-modal="true"
            class="modal"
            :aria-label="title"
            :class="{ 'hidden': ! isOpen }"
        >
            <h2 class="modal__title">The title of the modal</h2>
            <div class="modal__inner">
                <slot></slot>
            </div>
            <div class="modal__action">
                <slot name="footer"></slot>
                <button type="button" @click="close" class="btn btn--secondary">{{ __('Cancel') }}</button>
            </div>
        </div>
    </div>
</template>

<script>
    import Closable from './../Mixins/Closable';

    export default {
        mixins: [Closable],

        props: {
            title: {
                type: String,
                required: true,
            },
        },

        watch: {
            isOpen(newValue, oldValue) {
                document.body.classList.toggle('has-modal-open', newValue);
            },
        },
    }
</script>
