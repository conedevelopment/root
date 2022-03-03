<template>
    <div class="alert" :class="`alert--${type}`" v-show="isOpen">
        <div class="alert__message" v-html="message"></div>
        <button type="button" class="alert__close" @click="close">&times;</button>
    </div>
</template>

<script>
    import Closable from './../Mixins/Closable';

    export default {
        mixins: [Closable],

        props: {
            message: {
                type: String,
                required: true,
            },
            type: {
                type: String,
                default: 'info',
            },
        },

        data() {
            return {
                isOpen: true,
            };
        },
    }
</script>
