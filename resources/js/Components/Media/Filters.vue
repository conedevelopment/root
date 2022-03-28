<template>
    <div class="modal-filter">
        <div class="form-group--inline">
            <label for="media-search" class="sr-only">{{ __('Search') }}</label>
            <input
                id="media-search"
                type="text"
                class="form-control form-control--sm"
                style="width: 15rem;"
                :placeholder="__('Search')"
                :readonly="$parent.processing"
                v-model.lazy="$parent.query.search"
                v-debounce="500"
            >
        </div>
        <div class="form-group--inline">
            <label for="media-type" class="form-group__label is-prepend">
                <span class="form-group-text">{{ __('Type') }}</span>
            </label>
            <div class="custom-select">
                <select
                    id="media-type"
                    class="form-control form-control--sm"
                    :disabled="$parent.processing"
                    v-model="$parent.query.type"
                >
                    <option value="">{{ __('All') }}</option>
                    <option value="file">{{ __('File') }}</option>
                    <option value="image">{{ __('Image') }}</option>
                </select>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        //
    }
</script>
