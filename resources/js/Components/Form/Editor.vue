<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <div>
            <div v-if="editor" class="editor-menu">
                <button
                    type="button"
                    @click="editor.chain().focus().toggleBold().run()"
                    :class="{ 'is-active': editor.isActive('bold') }"
                >
                    B
                </button>
                <button
                    type="button"
                    @click="editor.chain().focus().toggleItalic().run()"
                    :class="{ 'is-active': editor.isActive('italic') }"
                >
                    I
                </button>
                <button
                    type="button"
                    @click="editor.chain().focus().toggleLink({  }).run()"
                    :class="{ 'is-active': editor.isActive('a') }"
                >
                    Link
                </button>
            </div>
            <div ref="editor"></div>
        </div>
        <Media
            v-if="with_media"
            ref="media"
            :url="media_url"
            :title="__('Media')"
            multiple
        ></Media>
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
    import Media from './../Media/Media.vue';
    import StarterKit from '@tiptap/starter-kit'

    export default {
        components: {
            Media,
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

        data() {
            return {
                editor: null,
            };
        },
    }
</script>
