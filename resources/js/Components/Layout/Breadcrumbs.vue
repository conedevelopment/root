<template>
    <nav class="app-header__breadcrumbs" :aria-label="__('Breadcrumbs')">
        <ol class="breadcrumbs">
            <li v-for="item in items" :key="item.url" class="breadcrumb-item">
                <Link v-if="! item.active" :href="item.url">
                    {{ item.label }}
                </Link>
                <span v-else aria-current="page">{{ item.label }}</span>
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

        props: {
            items: {
                type: Array,
                default: () => [],
            },
        },
    }
</script>
