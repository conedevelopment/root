<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <div>
            <button
                type="button"
                class="btn btn--sm btn--tertiary"
                :class="{ 'btn--delete': invalid }"
                @click="$refs.media.open"
            >
                {{ __('Select :label', { label }) }}
            </button>
            <Media
                ref="media"
                :url="url"
                :title="label"
                :filters="filters"
                :modelValue="items"
                :multiple="multiple"
                @update:modelValue="update"
            ></Media>
        </div>
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
                items: JSON.parse(JSON.stringify(this.selection)),
            };
        },

        computed: {
            invalid() {
                return Object.keys(this.$parent.form.errors).some((key) => {
                    return key.startsWith(`${this.name}.`);
                });
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
            },
        },
    }
</script>
