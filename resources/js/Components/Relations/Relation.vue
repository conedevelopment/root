<template>
    <div>
        <h4>{{ label }}</h4>
        <div class="card app-card">
            <table v-if="items.data.length > 0" class="table">
                <thead>
                    <tr>
                        <th v-for="column in items.data[0].fields" :key="column.name">
                            {{ column.label }}
                        </th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in items.data" :key="row.id">
                        <td v-for="column in row.fields" :key="`${row.id}.${column.name}`" v-html="column.formattedValue"></td>
                        <td>
                            <div class="table__actions">
                                <button
                                    type="button"
                                    class="btn btn--delete btn--sm btn--icon"
                                    v-if="row.abilities.delete"
                                    :aria-label="__('Delete')"
                                    :title="__('Delete')"
                                    @click="destroy"
                                >
                                    <Icon class="btn__icon" name="delete"/>
                                </button>
                                <Link
                                    v-if="row.abilities.view"
                                    class="btn btn--tertiary btn--sm btn--icon"
                                    :href="row.url"
                                    :aria-label="__('View')"
                                    :title="__('View')"
                                >
                                    <Icon class="btn__icon" name="view"/>
                                </Link>
                                <Link
                                    v-if="row.abilities.update"
                                    class="btn btn--tertiary btn--sm btn--icon"
                                    :href="`${row.url}/edit`"
                                    :aria-label="__('Edit')"
                                    :title="__('Edit')"
                                >
                                    <Icon class="btn__icon" name="edit"/>
                                </Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="alert alert--info">
                {{ __('No related records found.') }}
            </div>
        </div>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/vue3';

    export default {
        components: {
            Link,
        },

        props: {
            label: {
                type: String,
                required: true,
            },
            relatedName: {
                type: String,
                required: true,
            },
            url: {
                type: String,
                required: true,
            },
            items: {
                type: Object,
                required: true,
            },
        },

        inheritAttrs: false,
    }
</script>
