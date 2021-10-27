<template>
    <table>
        <Head :columns="columns"></Head>
        <tbody>
            <tr v-for="item in query.data" :key="item.id">
                <td v-for="field in item.fields" :key="`${item.id}-${field.name}`">
                    {{ field.formatted_value }}
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
    import Head from './Head';

    export default {
        components: {
            Head,
        },

        props: {
            query: {
                type: Object,
                required: true,
            },
            filters: {
                type: Array,
                required: true,
            },
            actions: {
                type: Array,
                required: true,
            },
        },

        computed: {
            columns() {
                const fields = this.query.data?.[0]?.fields || [];

                return fields.map((field) => {
                    return {
                        label: field.label,
                        name: field.name,
                        sortable: field.sortable,
                    };
                });
            },
        },
    }
</script>
