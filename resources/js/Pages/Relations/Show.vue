<template>
    <div>
        <div class="app-operation">
            <div class="app-operation__edit">
                <Link v-if="model.abilities.update" class="btn btn--sm btn--tertiary" :href="`${model.url}/edit`">
                    {{ __('Edit') }}
                </Link>
                <button v-if="model.abilities.delete" type="button" class="btn btn--sm btn--delete" @click="destroy">
                    {{ __('Delete') }}
                </button>
            </div>
        </div>
        <div class="app-card card">
            <div class="table-responsive">
                <table class="table table--striped table--clear-border table--rounded table--sm">
                    <tbody>
                        <tr v-for="field in model.fields" :key="field.name">
                            <th style="width: 10rem; text-align: end;">{{ field.label }}</th>
                            <td>
                                <div v-html="field.formatted_value"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';

    export default {
        components: {
            Link,
        },

        props: {
            model: {
                type: Object,
                required: true,
            },
            resource: {
                type: Object,
                required: true,
            },
            field: {
                type: Object,
                required: true,
            },
        },

        layout: function (h, page) {
            return h(this.resolveDefaultLayout(), () => page);
        },

        methods: {
            destroy() {
                this.$inertia.delete(this.model.url, {
                    onBefore: () => confirm(this.__('Are you sure?')),
                });
            },
        },
    }
</script>
