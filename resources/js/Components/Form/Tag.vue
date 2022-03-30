<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <div
            class="form-control tag-control"
            :class="{ 'form-control--invalid': error !== null }"
            @click.self="$refs.input.focus"
        >
            <span v-for="(item, index) in modelValue" class="tag" :key="index">
                <span class="tag__label">{{ item }}</span>
                <button type="button" class="tag__remove" @click="remove(index)">
                    <Icon name="close"></Icon>
                </button>
            </span>
            <input
                ref="input"
                type="text"
                style="width: 150px;"
                v-bind="$attrs"
                v-model="tag"
                @blur="add"
                @keydown.enter.prevent="add"
                @keydown.backspace="removeLast"
            >
        </div>
        <span class="field-feedback field-feedback--invalid" v-if="error">{{ error }}</span>
    </div>
</template>

<script>
    export default {
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
            value: {
                type: Array,
                default: () => [],
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        data() {
            return {
                tag: null,
            };
        },

        methods: {
            add() {
                const value = Array.from(this.modelValue || []);

                if (this.tag && ! value.includes(this.tag)) {
                    value.push(this.tag);

                    this.$emit('update:modelValue', value);

                    this.tag = null;
                }
            },
            remove(index) {
                const value = Array.from(this.modelValue || []);

                value.splice(index, 1);

                this.$emit('update:modelValue', value);
            },
            removeLast() {
                if (Array.isArray(this.modelValue) && ! this.tag) {
                    this.remove(this.modelValue.length - 1);
                }
            },
        },
    }
</script>
