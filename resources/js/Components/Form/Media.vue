<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <div>
            <button type="button" class="btn btn--sm btn--tertiary" :class="{ 'btn--delete': invalid }" @click="$refs.media.open">
                {{ __('Select :label', { label }) }}
            </button>
            <Media
                ref="media"
                :url="url"
                :title="label"
                :modelValue="modelValue"
                :select-resolver="selectResolver"
                :multiple="multiple"
                @update:modelValue="update"
            ></Media>
        </div>
        <span
            class="field-feedback"
            :class="{ 'field-feedback--invalid': invalid }"
            v-if="invalid || help"
            v-html="invalid ? __('The given pivot data is invalid!') : help"
        ></span>
        <div class="selected-media-item-list">
            <div v-for="medium in items" class="selected-media-item" :key="medium.id">
                <button type="button" class="selected-media-item__remove" @click="remove(medium)">
                    <Icon name="close"></Icon>
                </button>
                <img
                    v-if="medium.is_image"
                    :src="medium.urls.thumb || medium.urls.original"
                    :alt="medium.file_name"
                >
                <span v-else class="selected-media-item__document" :title="medium.file_name">
                    <Icon name="description"></Icon>
                    <span style="max-width: 100%; overflow: hidden; text-overflow: ellipsis; text-align: center;">
                        {{ medium.file_name }}
                    </span>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    import Media from './../Media/Media';

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
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        mounted() {
            this.$refs.media.selection = Array.from(this.items);
        },

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
            remove(item) {
                this.$refs.media.deselect(item);
                this.items = this.$refs.media.selection;
            },
            selectResolver(value, selection) {
                this.items = selection;

                return value;
            },
            update(value) {
                this.$emit('update:modelValue', value);
            },
        },
    }
</script>
