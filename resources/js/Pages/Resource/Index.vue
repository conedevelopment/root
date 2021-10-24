<template>
    <div>
        <table>
            <thead>
                <tr></tr>
            </thead>
            <tbody>
                <tr v-for="item in query.data" :key="item.id">
                    <td v-for="field in item.fields" :key="`${item.id}-${field.name}`">
                        {{ field.formatted_value }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
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
    }
</script>
