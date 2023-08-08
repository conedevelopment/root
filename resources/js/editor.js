import { Editor } from '@tiptap/core';
import Highlight from '@tiptap/extension-highlight';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import StarterKit from '@tiptap/starter-kit';
import TextAlign from '@tiptap/extension-text-align';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('editor', (content, config = {}) => {
        let editor;

        return {
            updatedAt: Date.now(),

            content: content,

            init() {
                const _this = this;

                editor = new Editor({
                    content: content,
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
                    onUpdate(delta) {
                        _this.content = delta.editor.getHTML();

                        _this.updatedAt = Date.now();
                    },
                    onSelectionUpdate() {
                        _this.updatedAt = Date.now();
                    },
                });
            },
            editor() {
                return editor;
            },
            isActive(type, opts = {}) {
                return editor.isActive(type, opts);
            },
        };
    });
});
