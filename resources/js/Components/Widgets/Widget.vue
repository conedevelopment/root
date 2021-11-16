<template>
    <div v-html="template"></div>
</template>

<script>
    export default {
        props: {
            template: {
                type: String,
                required: true,
            },
        },
    }
</script>
