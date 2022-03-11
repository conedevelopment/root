<template>
    <div class="form-group" :class="class" :style="style"></div>
</template>

<script>
    export default {
        props: {
            class: {
                type: [String, Array, Object],
                default: null,
            },
            style: {
                type: [String, Array, Object],
                default: null,
            },
        },
    }
</script>
