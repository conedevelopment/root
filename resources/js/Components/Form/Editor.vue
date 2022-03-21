<template>
    <div class="form-group" :class="class" :style="style">
        <div ref="input" class="editor" spellcheck="false"></div>
        <Media ref="media" :url="media_url" :title="__('Media')"></Media>
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
            media_url: {
                type: String,
                required: true,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        mounted() {
            const editor = new Quill(this.$refs.input, {
                modules: {
                    toolbar: {
                        container: [
                            [{ header: [1, 2, 3, 4, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ list: 'ordered'}, { list: 'bullet' }, { align: [] }],
                            ['link', 'image'],
                            ['clean'],
                        ],
                        handlers: {
                            image: () => this.$refs.media.open(),
                        },
                    },
                    clipboard: {
                        matchVisual: false,
                    },
                },
                theme: 'snow',
                formats: ['header', 'align', 'bold', 'underline', 'italic', 'list', 'link', 'image'],
                placeholder: this.$attrs.placeholder || '',
            });

            editor.root.innerHTML = this.modelValue;
            editor.enable(! this.$attrs.disabled);
            editor.on('text-change', this.update);

            this.$dispatcher.on('insertMedia', (event) => {
                const range = editor.getSelection(true);
                event.detail.forEach((value) => {
                    if (value.is_image) {
                        editor.insertEmbed(range.index, 'image', value.urls.original, Quill.sources.USER)
                        editor.setSelection(range.index + 1, 0, Quill.sources.SILENT);
                    } else {
                        editor.insertText(range.index, value.name, 'link', value.urls.original, Quill.sources.USER);
                        editor.setSelection(range.index + value.name.length, 0, Quill.sources.SILENT);
                    }
                });
                this.$refs.media.selection = [];
            });

            this.quill = editor;
        },

        data() {
            return {
                quill: null,
            };
        },

        methods: {
            update() {
                const value = this.quill.root.innerHTML === '<p><br></p>' ? '' : this.quill.root.innerHTML;

                this.$emit('update:modelValue', value);
            },
            insertMedia(values) {
                this.$dispatcher.emit('insertMedia', values);
            },
        },
    }
</script>
