<template>
    <thead>
        <tr>
            <th v-for="column in columns" :key="column.name">
                {{ column.label }}
            </th>
        </tr>
    </thead>
</template>

<script>
    export default {
        props: {
            columns: {
                type: Array,
                required: true,
            },
        },
    }
</script>
