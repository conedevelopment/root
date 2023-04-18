<template>
    <nav class="app-header__breadcrumbs" :aria-label="__('Breadcrumbs')">
        <ol class="breadcrumbs">
            <li v-for="(item, index) in items" :key="uri" class="breadcrumb-item">
                <Link v-if="index < items.length - 1" :href="item.uri">
                    {{ item.label }}
                </Link>
                <span v-else aria-current="page">{{ item.label }}</span>
            </li>
        </ol>
    </nav>
</template>

<script>
    import { Link } from '@inertiajs/vue3';

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
