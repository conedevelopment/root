<template>
    <tr :class="{ 'is-selected': selected, 'is-trashed': item.trashed }">
        <th v-if="$parent.actions.length > 0" scope="row">
            <label class="form-check" style="position: relative; top: 3px;">
                <input class="form-check__control" type="checkbox" v-model="selected">
                <span class="form-label form-check__label" aria-label=""></span>
            </label>
        </th>
        <td
            v-for="field in item.fields"
            :key="`${item.id}-${field.name}`"
            v-html="field.formatted_value"
        ></td>
        <td>
            <div class="table__actions">
                <button
                    type="button"
                    class="btn btn--delete btn--sm btn--icon"
                    v-if="item.abilities.delete || item.abilities.forceDelete"
                    :aria-label="item.trashed ? __('Delete permanently') : __('Delete')"
                    :title="item.trashed ? __('Delete permanently') : __('Delete')"
                    @click="destroy"
                >
                    <Icon class="btn__icon" :name="item.trashed ? 'delete-forever' : 'delete'"></Icon>
                </button>
                <button
                    type="button"
                    class="btn btn--warning btn--sm btn--icon"
                    v-if="item.trashed && item.abilities.restore"
                    :aria-label="__('Restore')"
                    :title="__('Restore')"
                    @click="restore"
                >
                    <Icon class="btn__icon" name="restore-from-trash"></Icon>
                </button>
                <Link
                    v-if="item.abilities.view"
                    class="btn btn--tertiary btn--sm btn--icon"
                    :href="item.url"
                    :aria-label="__('View')"
                    :title="__('View')"
                >
                    <Icon class="btn__icon" name="view"></Icon>
                </Link>
                <Link
                    v-if="item.abilities.update"
                    class="btn btn--tertiary btn--sm btn--icon"
                    :href="`${item.url}/edit`"
                    :aria-label="__('Edit')"
                    :title="__('Edit')"
                >
                    <Icon class="btn__icon" name="edit"></Icon>
                </Link>
            </div>
        </td>
    </tr>
</template>

<script>
    import { Link } from '@inertiajs/vue3';

    export default {
        components: {
            Link,
        },

        props: {
            item: {
                type: Object,
                required: true,
            },
        },

        computed: {
            selected: {
                get() {
                    return this.$parent.selected(this.item);
                },
                set(value) {
                    value ? this.$parent.select(this.item) : this.$parent.deselect(this.item);
                },
            },
        },

        methods: {
            destroy() {
                this.$inertia.delete(this.item.url, {
                    onBefore: () => window.confirm(this.__('Are you sure?')),
                    onStart: (visit) => this.$parent.deselect(this.item),
                });
            },
            restore() {
                this.$inertia.post(`${this.item.url}/restore`, {
                    onBefore: () => window.confirm(this.__('Are you sure?')),
                });
            },
        },
    }
</script>
