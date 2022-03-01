<template>
    <div class="list-footer">
        <div class="list-footer__pager">
            <select
                class="form-control form-control--sm"
                id="per-page"
                :value="modelValue.per_page"
                @change="update"
            >
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
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
            modelValue: {
                type: Object,
                default: () => {},
            },
            items: {
                type: Object,
                required: true,
            },
        },

        emits: ['update:modelValue'],

        methods: {
            update(event) {
                this.$emit(
                    'update:modelValue',
                    Object.assign({}, this.modelValue, { 'per_page': event.target.value })
                );
            },
        },
    }
</script>
