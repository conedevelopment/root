<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <div ref="input" class="editor" spellcheck="false"></div>
        <Media
            v-if="with_media"
            ref="media"
            :url="media_url"
            :title="__('Media')"
            :select-resolver="selectResolver"
        ></Media>
        <span class="field-feedback field-feedback--invalid" v-if="error">{{ error }}</span>
    </div>
</template>

<script>
    import Quill from 'quill';
    import Media from './../Media/Media';

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
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        mounted() {
            const config = JSON.parse(JSON.stringify(
                Object.assign({}, this.config, { placeholder: this.placeholder })
            ));

            if (this.with_media) {
                config.modules.toolbar.handlers.image = () => {
                    this.$refs.media.open();
                };
            }

            const editor = new Quill(this.$refs.input, config);

            editor.root.innerHTML = this.modelValue;
            editor.enable(! this.$attrs.disabled);
            editor.on('text-change', () => {
                this.$emit('update:modelValue', editor.root.innerHTML === '<p><br></p>' ? '' : editor.root.innerHTML);
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

                this.$refs.media.clearSelection();
            },
        },
    }
</script>
