<template>
    <div>
        <div class="app-widget">
            <Widget
                v-for="widget in widgets"
                :key="widget.key"
                v-bind="widget"
            ></Widget>
        </div>
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
                    class="btn btn--icon btn--sm btn--delete"
                    v-if="model.abilities.delete"
                    @click="destroy"
                    :aria-label="__('Delete')"
                >
                    <Icon class="btn__icon" name="delete"></Icon>
                </button>
                <Link
                    v-if="model.abilities.update"
                    class="btn btn--icon btn--sm btn--tertiary"
                    :href="model.urls.edit"
                    :aria-label="__('Edit')"
                >
                    <Icon class="btn__icon" name="edit"></Icon>
                </Link>
            </div>
        </div>
        <div class="app-card card">
            <ul class="preview-list">
                <li v-for="field in model.fields" :key="field.name">
                    <strong>{{ field.label }}</strong>: <span v-html="field.formatted_value"></span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';
    import Actions from './../../Components/Actions/Actions';
    import Widget from './../../Components/Widgets/Handler';

    export default {
        components: {
            Actions,
            Link,
            Widget,
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
            widgets: {
                type: Array,
                default: () => [],
            },
        },

        layout: function (h, page) {
            return h(this.resolveDefaultLayout(), () => page);
        },

        methods: {
            destroy() {
                this.$inertia.delete(this.model.urls.destroy, {
                    onBefore: () => confirm(this.__('Are you sure?')),
                });
            },
        },
    }
</script>
