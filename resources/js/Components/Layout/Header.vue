<template>
    <header class="app-header">
        <div class="app-header__heading">
            <h1 class="app-header__title">{{ title }}</h1>
            <Breadcrumbs :items="breadcrumbs"></Breadcrumbs>
        </div>
        <div class="app-header__actions"></div>
    </header>
</template>

<script>
    import Breadcrumbs from './Breadcrumbs.vue';

    export default {
        components: {
            Breadcrumbs,
        },

        props: {
            title: {
                type: String,
                required: true,
            },
            breadcrumbs: {
                type: Object,
                default: () => {},
            },
        },
    }
</script>
