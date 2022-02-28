<template>
    <div class="modal-footer">
        <div class="modal-footer-actions">
            <span v-show="$parent.selection.length" class="modal-help-text">
                {{ __(':files files selected', { files: $parent.selection.length }) }}
            </span>
        </div>
        <div class="modal-footer-actions">
            <button type="button" class="btn btn--sm btn--secondary-ghost" :disabled="$parent.processing" @click="$parent.close">
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
