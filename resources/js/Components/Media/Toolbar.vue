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
            <button type="button" class="btn btn--primary" @click="update">
                {{ __('Save Selection') }}
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        emits: ['update', 'upload'],

        methods: {
            update() {
                this.$emit('update');
            },
            upload(event) {
                this.$emit('upload', event.target.files);
            },
        },
    }
</script>
