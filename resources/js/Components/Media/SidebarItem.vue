<template>
    <Accordion ref="accordion" :title="item.file_name" :class="{ 'is-invalid': invalid }">
        <template #header>
            <div class="media-accordion__image-wrapper" :class="{ 'is-loading': loading }" v-if="item.is_image">
                <img :src="url" class="media-accordion__image" alt="" @error="reload" @load="loading = false">
            </div>
            <span v-else class="media-accordion__icon">
                <Icon name="description"></Icon>
            </span>
            <span class="media-accrodion__caption">
                {{ item.file_name }}
            </span>
        </template>
        <ul class="media-sidebar__list mt-3 mb-3">
            <li><strong>{{ __('Created At') }}</strong>: {{ item.created_at }}</li>
            <li><strong>{{ __('Size') }}</strong>: {{ size }}</li>
            <li v-if="dimensions">
                <strong>{{ __('Dimensions') }}</strong>: <span v-html="dimensions"></span>
            </li>
        </ul>
        <div class="form-group-stack">
            <FormHandler
                v-for="field in item.fields"
                v-bind="field"
                v-model="$parent.$parent.value[item.id][field.name]"
                :form="$parent.$parent.$parent.$parent.form"
                :key="`${item.id}-${field.name}`"
                :id="`${$parent.$parent.$parent.name}.${item.id}.${field.name}`"
                :name="`${$parent.$parent.$parent.name}.${item.id}.${field.name}`"
                :disabled="processing"
            ></FormHandler>
            <div class="form-group" style="display: flex; justify-content: space-between;">
                <button type="button" class="btn btn--delete btn--sm btn--tertiary" :disabled="processing" @click="deselect">
                    {{ __('Remove from Selection') }}
                </button>
                <button type="button" class="btn btn--delete btn--sm" :disabled="processing" @click="destroy">
                    {{ __('Delete') }}
                </button>
            </div>
        </div>
    </Accordion>
</template>

<script>
    import Accordion from './../Accordion';

    export default {
        components: {
            Accordion,
        },

        props: {
            item: {
                type: Object,
                required: true,
            },
        },

        emits: ['deselect'],

        data() {
            return {
                tries: 0,
                loading: false,
                processing: false,
                url: this.item.urls.thumb || this.item.urls.original,
            };
        },

        computed: {
            invalid() {
                return Object.keys(this.$parent.$parent.$parent.$parent.form.errors).some((key) => {
                    return key.startsWith(`${this.$parent.$parent.$parent.name}.${this.item.id}.`);
                });
            },
            size() {
                if (this.item.size === 0) {
                    return '1 KB';
                }

                const sizes = ['KB', 'MB', 'GB', 'TB'];

                const i = Math.floor(Math.log(this.item.size) / Math.log(1024));

                return (this.item.size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + sizes[i];
            },
            dimensions() {
                if (this.item.width && this.item.height) {
                    return `${this.item.width}&times;${this.item.height} px`;
                }

                return null;
            },
        },

        methods: {
            deselect() {
                this.$emit('deselect', this.item);
            },
            destroy() {
                const confirm = window.confirm(this.__('Are you sure?'));

                if (confirm) {
                    this.processing = true;

                    this.$http.delete(this.$parent.$parent.url, {
                        data: {
                            models: [this.item.id],
                        },
                    }).then((response) => {
                        this.deselect();

                        const index = this.$parent.$parent.response.data.findIndex((item) => item.id === this.item.id);

                        this.$parent.$parent.response.data.splice(index, 1);
                    }).catch((error) => {
                        //
                    }).finally(() => {
                        this.processing = false;
                    });
                }
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
