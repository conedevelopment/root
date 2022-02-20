<template>
    <div>
        <slot></slot>
    </div>
</template>

<script>
    import Closable from './../Mixins/Closable';

    export default {
        mixins: [Closable],
    }
</script>
