<template>
    <nav class="app-header__breadcrumbs" :aria-label="__('Breadcrumbs')">
        <ol class="breadcrumbs">
            <li v-for="(label, path) in items" :key="path" class="breadcrumb-item">
                <Link v-if="! isActive(path)" :href="path">
                    {{ label }}
                </Link>
                <span v-else aria-current="page">{{ label }}</span>
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
                type: Object,
                default: () => {},
            },
        },

        methods: {
            isActive(path) {
                const paths = Object.keys(this.items);

                return paths.indexOf(path) === paths.length - 1;
            },
        },
    }
</script>
