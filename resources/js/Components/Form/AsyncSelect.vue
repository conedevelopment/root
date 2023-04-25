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
            <span v-for="item in _selection" class="tag" :key="item.value">
                <span class="tag__label">{{ item.formattedValue }}</span>
                <button type="button" class="tag__remove" @click="remove(item)">
                    <Icon name="close"/>
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
                v-html="item.formattedValue"
                tabindex="-1"
                :aria-selected="index === active ? 'true' : 'false'"
                :class="{ 'is-active': index === active, 'is-selected': selected(item) }"
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
            formattedValue: {
                type: [String, Number, Array, Object],
                default: null,
            },
            selection: {
                type: Array,
                default: () => [],
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
                _selection: Array.from(this.selection),
            };
        },

        methods: {
            commit() {
                this.close();

                const item = this.response.data[this.active];

                this.multiple ? this._selection.push(item) : [item];

                this.$emit(
                    'update:modelValue',
                    this.multiple ? this._selection.map((v) => v.value) : value[0].value
                );

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
                this._selection = [];
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
            selected(item) {
                if (this.multiple) {
                    return this.modelValue.includes(item.value);
                }

                return item.value === this.modelValue;
            },
            remove(item) {
                if (! this.multiple) {
                    this.$emit('update:selection', []);
                    this.$emit('update:modelValue', null);
                } else {
                    this._selection.splice(this._selection.findIndex((selected) => selected.value === item.value), 1);

                    this.$emit(
                        'update:modelValue',
                        this.multiple ? this._selection.map((v) => v.value) : (this._selection[0]?.value || null)
                    );
                }
            },
        },
    }
</script>
