<template>
    <div class="tiptap__group">
        <button
            class="btn btn--icon btn--control"
            type="button"
            :class="{ 'btn--primary': isActive('left'), 'btn--tertiary': ! isActive('left') }"
            @click="toggle('left')"
        >
            <Icon name="format-align-left" class="btn__icon btn__icon--sm"/>
        </button>
        <button
            class="btn btn--icon btn--control"
            type="button"
            :class="{ 'btn--primary': isActive('center'), 'btn--tertiary': ! isActive('center') }"
            @click="toggle('center')"
        >
            <Icon name="format-align-center" class="btn__icon btn__icon--sm"/>
        </button>
        <button
            class="btn btn--icon btn--control"
            type="button"
            :class="{ 'btn--primary': isActive('right'), 'btn--tertiary': ! isActive('right') }"
            @click="toggle('right')"
        >
            <Icon name="format-align-right" class="btn__icon btn__icon--sm"/>
        </button>
        <button
            class="btn btn--icon btn--control"
            type="button"
            :class="{ 'btn--primary': isActive('justify'), 'btn--tertiary': ! isActive('justify') }"
            @click="toggle('justify')"
        >
            <Icon name="format-align-justify" class="btn__icon btn__icon--sm"/>
        </button>
    </div>
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
            isActive(align) {
                return this.editor.isActive({ textAlign: align });
            },
            toggle(align) {
                this.isActive(align)
                    ? this.editor.chain().focus().unsetTextAlign().run()
                    : this.editor.chain().focus().setTextAlign(align).run();
            },
        },
    }
</script>
