<template>
    <div class="form-group form-group--autocomplete" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <div
            class="form-control tag-control"
            :class="{ 'form-control--invalid': error !== null }"
            @click.self="$refs.input.focus"
        >
            <span v-for="(item, key) in formattedValue" class="tag" :key="key">
                <span class="tag__label">{{ item }}</span>
                <button type="button" class="tag__remove" @click="remove(key)">
                    <Icon name="close"></Icon>
                </button>
            </span>
            <input
                ref="input"
                type="text"
                style="width: 150px;"
                autocomplete="off"
                v-bind="$attrs"
                v-model.lazy="search"
                v-debounce="300"
                :class="{ 'form-control--invalid': error !== null }"
                @focus="open"
                @blur="close"
                @keydown.up="highlightPrev"
                @keydown.down="highlightNext"
                @keydown.enter.prevent="commit"
                @change="fetch"
            >
        </div>
        <span class="field-feedback field-feedback--invalid" v-if="error">{{ error }}</span>
        <ul v-show="isOpen" role="listbox" style="z-index: 1000;">
            <li
                ref="option"
                v-for="(item, index) in response.data"
                v-html="item.formatted_value"
                tabindex="-1"
                :aria-selected="index === active ? 'true' : 'false'"
                :class="{ 'is-active': index === active, 'is-selected': selected(item.value) }"
                :key="item.value"
                @mousedown="select(index)"
            ></li>
            <li
                v-if="response.data.length === 0"
                aria-live="polite"
                role="status"
                class="field-feedback field-feedback--invalid"
            >
                {{ __('No items found for the given keyword.') }}
            </li>
        </ul>
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
                type: [String, Number, Array, Object],
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
                type: [String, Number, Array, Object],
                default: null,
            },
            formatted_value: {
                type: [String, Number, Array, Object],
                default: null,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        mounted() {
            this.$dispatcher.once('open', this.fetch);

            this.formattedValue = this.formatted_value === null
                ? {}
                : Object.assign({}, this.multiple ? this.formatted_value : { [this.modelValue]: this.formatted_value });
        },

        data() {
            return {
                active: -1,
                processing: false,
                response: { data: [] },
                search: null,
                formattedValue: null,
            };
        },

        methods: {
            commit() {
                this.close();

                const item = this.response.data[this.active].value;

                const value = this.multiple ? this.modelValue.concat([item]) : [item];

                this.$emit(
                    'update:modelValue',
                    this.selectResolver(this.multiple ? value : value[0], JSON.parse(JSON.stringify(this.response.data)))
                );

                this.updateFormattedValue(value);

                this.search = null;
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
                this.search = null;

                this.$emit('update:modelValue', this.multiple ? [] : null);
            },
            fetch() {
                this.processing = true;

                this.$http.get(this.url, { params: {
                    search: this.search,
                } }).then((response) => {
                    this.response = response.data;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
            selected(value) {
                if (this.multiple) {
                    return this.modelValue.includes(value);
                }

                return value === this.modelValue;
            },
            remove(value) {
                if (! this.multiple) {
                    this.$emit('update:modelValue', null);
                    this.formattedValue = {};
                } else {
                    const values = Array.from(this.modelValue);

                    values.splice(values.findIndex((item) => item === value), 1);

                    this.updateFormattedValue(values);

                    this.$emit('update:modelValue', values);
                }
            },
            updateFormattedValue(value) {
                this.formattedValue = value.reduce((fields, key) => {
                    return Object.assign(fields, {
                        [key]: this.formattedValue.hasOwnProperty(key)
                            ? this.formattedValue[key]
                            : this.response.data.find((option) => option.value === key).formatted_value,
                    });
                }, {});
            },
        },
    }
</script>
