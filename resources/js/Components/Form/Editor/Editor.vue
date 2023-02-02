<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <div class="tiptap">
            <div v-if="editor" class="tiptap__controls">
                <Heading :editor="editor"/>
                <div class="tiptap__group">
                    <Bold :editor="editor"/>
                    <Italic :editor="editor"/>
                    <Strike :editor="editor"/>
                    <Highlight :editor="editor"/>
                </div>
                <Link :editor="editor"/>
                <div class="tiptap__group">
                    <OrderedList :editor="editor"/>
                    <UnorderedList :editor="editor"/>
                    <HorizontalRule :editor="editor"/>
                    <CodeBlock :editor="editor"/>
                    <Blockquote :editor="editor"/>
                    <Media v-if="with_media" :editor="editor" :url="media_url"/>
                </div>
                <History :editor="editor"/>
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
    import BlockquoteHandler from './Blockquote.vue';
    import CodeBlockHandler from './CodeBlock.vue';
    import HeadingHandler from './Heading.vue';
    import Highlight from '@tiptap/extension-highlight';
    import HighlightHandler from './Highlight.vue';
    import HistoryHandler from './History.vue';
    import HorizontalRuleHandler from './HorizontalRule.vue';
    import ItalicHandler from './Italic.vue';
    import Image from '@tiptap/extension-image';
    import Link from '@tiptap/extension-link';
    import LinkHandler from './Link.vue';
    import MediaHandler from './Media.vue';
    import OrderedListHandler from './OrderedList.vue';
    import StarterKit from '@tiptap/starter-kit'
    import StrikeHandler from './Strike.vue';
    import UnorderedListHandler from './UnorderedList.vue';

    export default {
        components: {
            Blockquote: BlockquoteHandler,
            Bold: BoldHandler,
            CodeBlock: CodeBlockHandler,
            Heading: HeadingHandler,
            Highlight: HighlightHandler,
            History: HistoryHandler,
            HorizontalRule: HorizontalRuleHandler,
            Italic: ItalicHandler,
            Link: LinkHandler,
            Media: MediaHandler,
            OrderedList: OrderedListHandler,
            Strike: StrikeHandler,
            UnorderedList: UnorderedListHandler,
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
                    Link.configure({ ...(this.config.link || {}) }),
                    Highlight.configure({ ...(this.config.highlight || {}) }),
                    Image.configure({ ...(this.config.image || {}) }),
                ],
                onUpdate: (value) => {
                    this.$emit('update:modelValue', this.editor.isEmpty ? '' : this.editor.getHTML());
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

        computed: {
            form() {
                return this.$parent.form;
            },
        },
    }
</script>
