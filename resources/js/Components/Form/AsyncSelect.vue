<template>
    <div>
        <label :for="$attrs.id">{{ label }}</label>
        <span v-if="error">{{ error }}</span>
    </div>
</template>

<script>
    import Closable from './../../Mixins/Closalbe';

    export default {
        mixins: [Closable],

        props: {
            modelValue: {
                type: Array,
                default: () => [],
            },
            label: {
                type: String,
                required: true,
            },
            name: {
                type: String,
                required: true,
            },
            error: {
                type: String,
                default: null,
            },
            multiple: {
                type: Boolean,
                default: false,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        mounted() {
            // this.$dispatcher.once('open', this.fetch);

            window.addEventListener('keyup', (event) => {
                if (this.isOpen && event.code === 'Escape') {
                    this.close();
                }
            });

            window.addEventListener('click', (event) => {
                if (this.isOpen && ! this.$el.contains(event.target)) {
                    this.close();
                }
            });
        },

        data() {
            return {
                active: -1,
            };
        },

        methods: {
            clear() {
                this.$emit('update:modelValue', []);
            },
        },
    }
</script>
