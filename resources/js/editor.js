import { Editor } from '@tiptap/core';
import Highlight from '@tiptap/extension-highlight';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import StarterKit from '@tiptap/starter-kit';
import TextAlign from '@tiptap/extension-text-align';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('editor', (config = {}) => {
        let _editor;

        return {
            updatedAt: Date.now(),

            init() {
                const _this = this;

                _editor = new Editor({
                    content: this.$refs.input.value,
                    element: this.$refs.editor,
                    extensions: [
                        StarterKit,
                        Link.configure({ ...(config.link || {}) }),
                        Highlight.configure({ ...(config.highlight || {}) }),
                        Image.configure({ ...(config.image || {}) }),
                        TextAlign.configure({ ...(config.textAlign || {}) }),
                    ],
                    editorProps: {
                        attributes: {
                            class: 'focus:outline-none',
                            style: 'height: 100%; width: 100%; min-height: 100px;',
                        },
                    },
                    onCreate() {
                        _this.updatedAt = Date.now();
                    },
                    onUpdate({ editor }) {
                        _this.$refs.input.value = editor.isEmpty ? '' : editor.getHTML();

                        _this.updatedAt = Date.now();
                    },
                    onSelectionUpdate() {
                        _this.updatedAt = Date.now();
                    },
                });
            },
            editor() {
                return _editor;
            },
            isActive(type, opts = {}, updatedAt) {
                return _editor.isActive(type, opts);
            },
        };
    });
});
