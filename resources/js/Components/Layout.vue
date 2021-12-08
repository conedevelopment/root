<template>
    <main>
        <aside>
            <nav>
                <ul>
                    <li v-for="resource in resources" :key="resource.key">
                        <Link :href="resource.urls.index">
                            {{ resource.name }}
                        </Link>
                    </li>
                </ul>
            </nav>
        </aside>
        <section>
            <slot></slot>
        </section>
    </main>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';

    export default {
        components: {
            Link,
        },

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
