<template>
    <nav class="app-header__breadcrumbs" aria-label="Breadcrumbs">
        <ol class="breadcrumbs">
            <li class="breadcrumb-item">
                <a href="#">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <span aria-current="page">Posts</span>
            </li>
        </ol>
    </nav>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';

    export default {
        components: {
            Link,
        },
    }
</script>
