<template>
    <Accordion ref="accordion" :title="item.file_name">
        <template #header>
            <div v-if="item.is_image" class="media-accordion__image-wrapper" :class="{ 'is-loading': loading }">
                <img :src="url" class="media-accordion__image" alt="" @error="reload" @load="loading = false">
            </div>
            <span v-else class="media-accordion__icon">
                <Icon name="description"></Icon>
            </span>
            <span
                class="accordion__caption"
                :style="{ color: invalid ? 'var(--root-btn-color-delete-foreground)' : null }"
            >
                {{ item.file_name }}
            </span>
        </template>
        <ul class="media-sidebar__list mt-3 mb-3">
            <li><strong>{{ __('Name') }}</strong>: {{ item.file_name }}</li>
            <li><strong>{{ __('Mime type') }}</strong>: {{ item.mime_type }}</li>
            <li><strong>{{ __('Uploaded at') }}</strong>: {{ item.formatted_created_at }}</li>
            <li><strong>{{ __('Size') }}</strong>: {{ item.formatted_size }}</li>
            <li v-if="item.dimensions"><strong>{{ __('Dimensions') }}</strong>: {{ item.dimensions }}</li>
        </ul>
        <div class="form-group-stack">
            <FormHandler
                v-for="(field, index) in item.fields"
                v-bind="field"
                v-model="item.fields[index].value"
                :form="$parent.$parent.$parent.$parent.form"
                :key="`${item.id}-${field.name}`"
                :id="`${$parent.$parent.$parent.name}.${item.id}.${field.name}`"
                :name="`${$parent.$parent.$parent.name}.${item.id}.${field.name}`"
            ></FormHandler>
            <div class="form-group">
                <button type="button" class="btn btn--delete btn--sm btn--tertiary" @click="deselect">
                    {{ __('Remove from Selection') }}
                </button>
            </div>
        </div>
    </Accordion>
</template>

<script>
    export default {
        props: {
            item: {
                type: Object,
                required: true,
            },
        },

        emits: ['deselect'],

        mounted() {
            if (this.invalid) {
                this.$refs.accordion.open();
            }
        },

        data() {
            return {
                tries: 0,
                loading: false,
                url: this.item.urls.original,
            };
        },

        computed: {
            invalid() {
                return Object.keys(this.$parent.$parent.$parent.$parent.form.errors).some((key) => {
                    return key.startsWith(`${this.$parent.$parent.$parent.name}.${this.item.id}.`);
                });
            },
        },

        methods: {
            deselect() {
                this.$emit('deselect', this.item);
            },
            reload() {
                if (this.tries >= 5) {
                    this.loading = false;
                    return;
                }

                this.loading = true;

                const interval = setInterval(() => {
                    const url = new URL(this.url);
                    url.searchParams.set('key', (new Date()).getTime());

                    this.url = url.toString();
                    this.tries++;

                    clearInterval(interval);
                }, 5000);
            },
        },
    }
</script>
