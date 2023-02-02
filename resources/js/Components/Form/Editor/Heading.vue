<template>
    <select @change="(event) => handle(event.target.value)" class="form-control form-control--sm">
        <option value="" :selected="! editor.isActive('heading')">{{ __('Paragraph') }}</option>
        <option value="1" :selected="editor.isActive('heading', { level: 1 })">{{ __('H1') }}</option>
        <option value="2" :selected="editor.isActive('heading', { level: 2 })">{{ __('H2') }}</option>
        <option value="3" :selected="editor.isActive('heading', { level: 3 })">{{ __('H3') }}</option>
        <option value="4" :selected="editor.isActive('heading', { level: 4 })">{{ __('H4') }}</option>
        <option value="5" :selected="editor.isActive('heading', { level: 5 })">{{ __('H5') }}</option>
        <option value="6" :selected="editor.isActive('heading', { level: 6 })">{{ __('H6') }}</option>
    </select>
</template>

<script>
    export default {
        props: {
            editor: {
                type: Object,
                required: true,
            },
        },

        methods: {
            handle(level) {
                if (level === '') {
                    this.editor.commands.setParagraph();
                } else {
                    this.editor.chain().focus().setHeading({ level: parseInt(level) }).run();
                }
            },
        },
    }
</script>
