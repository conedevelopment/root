<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <div class="tiptap" style="display: flex; flex-direction: column; flex: 1;">
            <div v-if="editor" class="tiptap__controls">
                <Bold :editor="editor"/>
                <Italic :editor="editor"/>
                <Link :editor="editor"/>
            </div>
            <div ref="editor" class="tiptap__editor" style="flex: 1;"></div>
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
    import BoldHandler from './Bold.vue';
    import ItalicHandler from './Italic.vue';
    import Link from '@tiptap/extension-link';
    import LinkHandler from './Link.vue';
    import StarterKit from '@tiptap/starter-kit'

    export default {
        components: {
            Bold: BoldHandler,
            Italic: ItalicHandler,
            Link: LinkHandler,
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
                    Link.configure({
                        openOnClick: false,
                    }),
                ],
                onUpdate: (value) => {
                    // this.$emit('update:modelValue', value);
                },
                onCreate: (editor) => {
                    this.$refs.editor.querySelector('.ProseMirror').style.height = '100%';
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
