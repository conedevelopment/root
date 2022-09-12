<template>
    <div class="media-preview">
        <div>
            <img v-if="item.is_image" class="media-preview__image" :src="item.urls.original">
            <object v-else :src="item.urls.original"></object>
        </div>
        <div class="media-preview__sidebar">
            <div class="media-preview__sidebar-section">
                <h2 class="media-preview__title">{{ __('Details') }}</h2>
                <ul class="media-sidebar__list mt-3 mb-3">
                    <li><strong>{{ __('Name') }}</strong>: {{ item.file_name }}</li>
                    <li><strong>{{ __('Mime type') }}</strong>: {{ item.mime_type }}</li>
                    <li><strong>{{ __('Uploaded at') }}</strong>: {{ item.formatted_created_at }}</li>
                    <li><strong>{{ __('Size') }}</strong>: {{ item.formatted_size }}</li>
                    <li v-if="item.dimensions"><strong>{{ __('Dimensions') }}</strong>: {{ item.dimensions }}</li>
                </ul>
            </div>
            <div v-if="item.fields.length > 0" class="media-preview__sidebar-section">
                <h2 class="media-preview__title">{{ __('Edit') }}</h2>
                <div class="form-group-stack">
                    <FormHandler
                        v-for="field in item.fields"
                        v-bind="field"
                        v-model="$parent.modelValue[item.id][field.name]"
                        :form="$parent.$parent.$parent.form"
                        :key="`${item.id}-${field.name}`"
                        :id="`${$parent.$parent.name}.${item.id}.${field.name}`"
                        :name="`${$parent.$parent.name}.${item.id}.${field.name}`"
                        :disabled="processing"
                    ></FormHandler>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            item: {
                type: Object,
                required: true,
            },
        },
    }
</script>
