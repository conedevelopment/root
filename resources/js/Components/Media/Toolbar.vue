<template>
    <div class="modal__action">
        <div>
            <span v-show="$parent.selection.length" class="modal-help-text">
                {{ __(':files files selected', { files: $parent.selection.length }) }}
            </span>
        </div>
        <div>
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
