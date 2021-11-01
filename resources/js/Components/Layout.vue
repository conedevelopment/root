<template>
    <div>
        <slot></slot>
    </div>
</template>

<script>
    export default {
        computed: {
            resources() {
                return window.Root.resources;
            },
            user() {
                return window.Root.user;
            },
        },
    }
</script>
