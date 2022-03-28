<template>
    <header class="app-header">
        <div class="app-header__heading">
            <h1 class="app-header__title">{{ title }}</h1>
        </div>
        <div class="app-header__actions"></div>
    </header>
</template>

<script>
    export default {
        props: {
            title: {
                type: String,
                required: true,
            },
        },
    }
</script>
