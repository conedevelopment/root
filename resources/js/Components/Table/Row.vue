<template>
    <tr>
        <td v-for="field in item.fields" :key="`${item.id}-${field.name}`">
            {{ field.formatted_value }}
        </td>
    </tr>
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
