<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <input
            type="text"
            class="form-control"
            autocomplete="off"
            v-bind="$attrs"
            v-model.lazy="search"
            v-debounce="300"
            :class="{ 'form-control--invalid': error !== null }"
            @focus="open"
            @keydown.up="highlightPrev"
            @keydown.down="highlightNext"
            @keydown.enter.prevent="commit"
            @change="fetch"
        >
        <span class="field-feedback field-feedback--invalid" v-if="error">{{ error }}</span>
        <div style="max-height: 200px; width: 100%; z-index: 1000;">
            <div v-show="isOpen">
                <div
                    ref="option"
                    v-for="(item, index) in response.data"
                    v-html="item.formatted_value"
                    :key="item.value"
                    :class="[index === active ? 'is-active' : '']"
                    @mousedown="select(item.value)"
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
            class: {
                type: [String, Array, Object],
                default: null,
            },
            style: {
                type: [String, Array, Object],
                default: null,
            },
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
            selectResolver: {
                type: Function,
                default: (value, options) => value,
            },
            value: {
                default: null,
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
                search: null,
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

                this.$emit(
                    'update:modelValue',
                    this.selectResolver(value, JSON.parse(JSON.stringify(this.response.data)))
                );

                this.$nextTick(() => this.fetch());
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
            fetch() {
                this.processing = true;

                const exclude = Array.isArray(this.modelValue) ? this.modelValue : [this.modelValue];

                this.$http.get(this.url, { params: {
                    search: this.search,
                    exclude: exclude.filter((item) => item),
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
