<template>
    <fieldset :class="class" :style="style">
        <legend>{{ label }}</legend>
        <div></div>
    </fieldset>
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
        }
    }
</script>
