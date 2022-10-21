<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <div ref="editor"></div>
        <Media
            v-if="with_media"
            ref="media"
            :url="media_url"
            :title="__('Media')"
            :modelValue="selection"
            multiple
            @update:modelValue="selectResolver"
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
                    this.$emit('update:modelValue', value);
                },
            });

            this.selectResolver = (values, selection) => {
                this.insertMedia(editor, selection);

                return values;
            };
        },

        data() {
            return {
                selectResolver: (values) => values,
            };
        },

        methods: {
            insertMedia(editor, values) {
                const range = editor.getSelection(true);

                values.forEach((value) => {
                    if (value.is_image) {
                        editor.editor.insertEmbed(range.index, 'image', value.urls.original, Quill.sources.USER)
                        editor.setSelection(range.index + 1, 0, Quill.sources.SILENT);
                    } else {
                        editor.editor.insertText(range.index, value.name, 'link', value.urls.original, Quill.sources.USER);
                        editor.setSelection(range.index + value.name.length, 0, Quill.sources.SILENT);
                    }
                });

                editor.emitter.emit('text-change');

                this.$refs.media.clearSelection();
            },
        },
    }
</script>
