<template>
    <div class="media-preview">
        <div>
            <img v-if="modelValue.is_image" class="media-preview__image" :src="modelValue.urls.original">
            <object v-else :src="modelValue.urls.original"></object>
        </div>
        <div class="media-preview__sidebar">
            <div class="media-preview__sidebar-section">
                <h2 class="media-preview__title">{{ __('Details') }}</h2>
                <ul class="media-sidebar__list mt-3 mb-3">
                    <li><strong>{{ __('Name') }}</strong>: {{ modelValue.file_name }}</li>
                    <li><strong>{{ __('Mime type') }}</strong>: {{ modelValue.mime_type }}</li>
                    <li><strong>{{ __('Uploaded at') }}</strong>: {{ modelValue.formatted_created_at }}</li>
                    <li><strong>{{ __('Size') }}</strong>: {{ modelValue.formatted_size }}</li>
                    <li v-if="modelValue.dimensions"><strong>{{ __('Dimensions') }}</strong>: {{ modelValue.dimensions }}</li>
                </ul>
            </div>
            <div v-if="modelValue.fields.length > 0" class="media-preview__sidebar-section">
                <h2 class="media-preview__title">{{ __('Pivot Data') }}</h2>
                <div class="form-group-stack">
                    <FormHandler
                        v-for="(field, index) in modelValue.fields"
                        v-bind="field"
                        v-model="modelValue.fields[index].value"
                        :form="$parent.$parent.$parent.form"
                        :key="`${modelValue.id}-${field.name}`"
                        :id="`${$parent.$parent.name}.${modelValue.id}.${field.name}`"
                        :name="`${$parent.$parent.name}.${modelValue.id}.${field.name}`"
                        :disabled="$parent.processing"
                    ></FormHandler>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: Object,
                required: true,
            },
        },

        watch: {
            modelValue: {
                handler(newValue, oldValue) {
                    this.$emit('update:modelValue', newValue);
                },
                deep: true,
            }
        },

        emits: ['update:modelValue'],
    }
</script>
