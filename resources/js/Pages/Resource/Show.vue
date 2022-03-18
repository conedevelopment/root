<template>
    <div>
        <div class="app-operation">
            <Actions
                v-if="actions.length > 0"
                :selection="[model.id]"
                :actions="actions"
                @success="clearSelection"
            ></Actions>
            <div class="app-operation__edit">
                <button
                    type="button"
                    class="btn btn--icon btn--delete"
                    v-if="model.abilities.delete"
                    @click="destroy(url)"
                    :aria-label="__('Delete')"
                >
                    <Icon class="btn__icon" name="delete"></Icon>
                </button>
                <Link v-if="model.abilities.update" :href="`${url}/edit`" class="btn btn--icon btn--primary" :aria-label="__('Edit')">
                    <Icon class="btn__icon" name="edit"></Icon>
                </Link>
            </div>
        </div>
        <div class="app-card card">
            <ul class="clear-list">
                <li v-for="field in model.fields" :key="field.name">
                    <strong>{{ field.label }}</strong>: {{ field.formatted_value }}
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';
    import Actions from './../../Components/Actions/Actions';

    export default {
        components: {
            Actions,
            Link,
        },

        props: {
            actions: {
                type: Array,
                default: () => [],
            },
            model: {
                type: Object,
                required: true,
            },
        },

        layout: function (h, page) {
            return h(this.resolveDefaultLayout(), () => page);
        },

        computed: {
            url() {
                return window.location.pathname;
            },
        },

        methods: {
            destroy(url) {
                this.$inertia.delete(url, {
                    onBefore: () => confirm(this.__('Are you sure?')),
                });
            },
        },
    }
</script>
