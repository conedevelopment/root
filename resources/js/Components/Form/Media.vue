<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }} <small v-if="isDirty"><em>{{ __('Unsaved selection') }}</em></small></span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <div>
            <button
                type="button"
                class="btn btn--sm btn--tertiary"
                :class="{ 'btn--delete': invalid }"
                @click="$refs.media.open"
            >
                <span v-if="items.length === 0">
                    {{ __('Select :label', { label }) }}
                </span>
                <span v-else>
                    {{ __(':count :label selected', { count: items.length, label }) }}
                </span>
            </button>
            <Media
                ref="media"
                :url="url"
                :title="label"
                :filters="filters"
                :modelValue="items"
                :multiple="multiple"
                @change="isDirty = true"
                @update:modelValue="update"
            ></Media>
        </div>
        <span
            class="field-feedback"
            :class="{ 'field-feedback--invalid': invalid }"
            v-if="invalid || help"
            v-html="(error || errors[0]) || help"
        ></span>
    </div>
</template>

<script>
    import Media from './../Media/Media.vue';

    export default {
        components: {
            Media
        },

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
                type: [Array, Object],
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
            url: {
                type: String,
                required: true,
            },
            selection: {
                type: Array,
                default: () => [],
            },
            multiple: {
                type: Boolean,
                default: false,
            },
            help: {
                type: String,
                default: null,
            },
            filters: {
                type: Array,
                default: () => [],
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        data() {
            return {
                isDirty: false,
                items: JSON.parse(JSON.stringify(this.selection)),
            };
        },

        computed: {
            errors() {
                return Object.entries(this.$parent.form.errors).filter((pair) => {
                    return pair[0].startsWith(`${this.name}.`)
                }).map((pair) => {
                    return pair[1];
                });
            },
            invalid() {
                return this.error !== null || this.errors.length > 0;
            },
        },

        methods: {
            update(selection) {
                this.items = selection;

                const value = selection.reduce((data, item) => ({
                    ...data,
                    [item.id]: item.fields.reduce((pivotValues, field) => ({
                        ...pivotValues,
                        [field.name]: field.value
                    }), {}),
                }), {});

                this.$emit('update:modelValue', value);

                this.$refs.media.close();
                this.isDirty = false;
            },
        },
    }
</script>
