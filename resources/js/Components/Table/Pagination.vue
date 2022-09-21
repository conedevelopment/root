<template>
    <div class="list-footer">
        <div class="list-footer__pager">
            <select
                class="form-control form-control--sm"
                id="per-page"
                v-model.number="query.per_page"
                @change="emit"
            >
                <option v-if="! counts.includes(query.per_page)" disabled :value="query.per_page">
                    {{ __('Custom (:count)', { count: query.per_page }) }}
                </option>
                <option v-for="count in counts" :key="count" :value="count">
                    {{ count }}
                </option>
            </select>
            <label for="per-page">{{ __('of :count items', { count: items.total }) }}</label>
        </div>
        <ul class="list-footer__pagination">
            <li v-for="(link, index) in items.links" :key="index">
                <Link
                    class="btn btn--primary btn--sm"
                    type="button"
                    :as="link.url === null || link.active ? 'button' : 'a'"
                    :disabled="link.url === null || link.active"
                    :href="link.url"
                    :aria-current="link.active ? 'page' : ''"
                    v-html="link.label"
                ></Link>
            </li>
        </ul>
    </div>
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
                required: true,
            },
            query: {
                type: Object,
                required: true,
            },
        },

        emits: ['update:query'],

        data() {
            return {
                counts: [15, 25, 50, 100],
            };
        },

        methods: {
            emit() {
                this.$emit('update:query');
            },
        },
    }
</script>
