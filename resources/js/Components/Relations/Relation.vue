<template>
    <div>
        <h4>{{ label }}</h4>
        <div class="card app-card">
            <table v-if="items.data.length > 0" class="table">
                <thead>
                    <tr>
                        <td v-for="column in items.data[0]" :key="column.name">
                            {{ column.label }}
                        </td>
                    </tr>
                </thead>
                <tbody></tbody>
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
