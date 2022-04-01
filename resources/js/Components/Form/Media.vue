<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <div>
            <button type="button" class="btn btn--sm btn--tertiary" @click="$refs.media.open">
                {{ __('Select :label', { label }) }}
            </button>
            <Media
                ref="media"
                :url="url"
                :title="label"
                :modelValue="modelValue"
                @update:modelValue="$emit('update:modelValue', $event)"
            ></Media>
        </div>
        <span class="field-feedback field-feedback--invalid" v-if="error">{{ error }}</span>
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
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        methods: {
            //
        },
    }
</script>
