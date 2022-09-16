<template>
    <div class="modal-footer modal-footer--column">
        <div class="modal-footer-actions">
            <input
                multiple
                type="file"
                class="form-file"
                :disabled="$parent.processing"
                @change="upload"
            >
        </div>
        <div class="modal-footer-actions">
            <button type="button" class="btn btn--primary">
                {{ __('Save Selection') }}
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        emits: ['save', 'upload'],

        methods: {
            save() {
                this.$emit('save');
            },
            upload(event) {
                this.$emit('upload', event.target.files);
            },
        },
    }
</script>
