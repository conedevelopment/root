<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <div>
            <div v-if="editor" class="editor-menu">
                <Bold :editor="editor"></Bold>
                <Italic :editor="editor"></Italic>
                <Link :editor="editor"></Link>
                <!-- Code -->
                <!-- Highlight -->
            </div>
            <div ref="editor"></div>
        </div>
        <span
            class="field-feedback"
            :class="{ 'field-feedback--invalid': error !== null }"
            v-if="error !== null || help"
            v-html="error || help"
        ></span>
    </div>
</template>

<script>
    import { Editor } from '@tiptap/vue-3'
    import Bold from './Bold.vue';
    import Italic from './Italic.vue';
    import Link from './Link.vue';
    import StarterKit from '@tiptap/starter-kit'

    export default {
        components: {
            Bold,
            Italic,
            Link,
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
                type: String,
                default: '',
            },
            value: {
                type: String,
                default: '',
            },
            formatted_value: {
                type: String,
                default: '',
            },
            with_media: {
                type: Boolean,
                default: false,
            },
            media_url: {
                type: String,
                default: null,
            },
            label: {
                type: String,
                required: true,
            },
            error: {
                type: String,
                default: null,
            },
            placeholder: {
                type: String,
                default: '',
            },
            config: {
                type: Object,
                requried: true,
            },
            help: {
                type: String,
                default: '',
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        mounted() {
            this.editor = new Editor({
                element: this.$refs.editor,
                content: this.modelValue,
                extensions: [
                    StarterKit,
                ],
                onUpdate: (value) => {
                    // this.$emit('update:modelValue', value);
                },
            });
        },

        beforeUnmount() {
            this.editor.destroy();
        },

        data() {
            return {
                editor: null,
            };
        },
    }
</script>
