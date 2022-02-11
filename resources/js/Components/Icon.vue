<template>
    <svg class="icon" aria-hidden="true" focusable="false" role="img" fill="currentColor" viewBox="0 0 24 24" width="24" height="24">
        <use :xlink:href="`#icon-${name}`"></use>
    </svg>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                required: true,
            },
        },
    }
</script>
