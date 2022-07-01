<template>
    <div class="modal-footer">
        <div>
            <input
                multiple
                type="file"
                :disabled="$parent.processing"
                @change="$parent.handleFiles($event.target.files)"
            >
        </div>
        <div class="modal-footer-actions">
            <button type="button" class="btn btn--primary" :disabled="$parent.processing" @click="$parent.updateSelection">
                {{ __('Select') }}
            </button>
            <button type="button" class="btn btn--tertiary" :disabled="$parent.processing" @click="$parent.close">
                {{ __('Close') }}
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        //
    }
</script>
