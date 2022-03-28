<template>
    <div class="modal-backdrop" :class="{ 'modal-backdrop--visible': isOpen }" @click.self="close">
        <div
            role="dialog"
            aria-modal="true"
            class="modal"
            :aria-label="title"
            :class="{ 'hidden': ! isOpen }"
        >
            <h2 class="modal-title">{{ title }}</h2>
            <div class="modal-inner">
                <slot></slot>
            </div>
            <div class="modal-action">
                <slot name="footer"></slot>
                <button type="button" class="btn btn--secondary" @click="close">{{ __('Close') }}</button>
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
