<template>
    <div>
        <label :for="$attrs.id">{{ label }}</label>
        <input
            type="text"
            autocomplete="off"
            v-bind="$attrs"
            v-debounce="300"
            @focus="open"
            @keydown.up="highlightPrev"
            @keydown.down="highlightNext"
            @keydown.enter.prevent="commit"
            @change="fetch"
        >
        <span v-if="error">{{ error }}</span>
        <div style="max-height: 200px; width: 100%; z-index: 1000;">
            <div v-show="isOpen">
                <div
                    ref="option"
                    v-for="(item, index) in response.data"
                    v-html="item.label"
                    :key="item.id"
                    :class="[index === active ? 'active' : '']"
                    @mousedown="select(item.id)"
                ></div>
                <div v-if="response.data.length === 0" aria-disabled="true">
                    {{ __('No items found for the given keyword.') }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Closable from './../../Mixins/Closable';

    export default {
        mixins: [Closable],

        props: {
            modelValue: {
                type: [String, Number, Array],
                default: null,
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
            url: {
                type: String,
                required: true,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        mounted() {
            this.$dispatcher.once('open', this.fetch);
        },

        data() {
            return {
                active: -1,
                processing: false,
                response: { data: [] },
            };
        },

        methods: {
            commit() {
                this.close();

                let value = [];

                if (this.multiple) {
                    value = Array.from(this.modelValue);

                    value.push(this.active);
                } else {
                    value = this.active;
                }

                this.$emit('update:modelValue', value);
            },
            select(index) {
                this.highlight(index);

                this.commit();
            },
            highlight(index) {
                this.open();

                this.active = index;

                if (this.$refs.option && this.$refs.option[index]) {
                    this.$nextTick(() => {
                        this.$refs.option[index].scrollIntoView({ block: 'nearest' });
                    });
                }
            },
            highlightNext() {
                if (this.isOpen) {
                    this.highlight(
                        this.active + 1 >= this.response.data.length ? 0 : this.active + 1
                    );
                }
            },
            highlightPrev() {
                if (this.isOpen) {
                    this.highlight(
                        this.active === 0 ? this.response.data.length - 1 : this.active - 1
                    );
                }
            },
            clear() {
                this.$emit('update:modelValue', []);
            },
            fetch(event) {
                this.processing = true;

                this.$http.get(this.url, { params: {
                    search: event.target.value,
                    exclude: Array.isArray(this.modelValue) ? this.modelValue : [this.modelValue],
                } }).then((response) => {
                    this.response = response.data;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
        },
    }
</script>
