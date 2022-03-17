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
                    class="btn btn--icon btn--tertiary btn--sm"
                    v-if="item.abilities.delete"
                    @click="destroy(url)"
                >
                    <Icon class="btn__icon" name="delete"></Icon>
                </button>
                <Link v-if="item.abilities.view" :href="url" class="btn btn--icon btn--tertiary btn--sm" :aria-label="__('View')">
                    <Icon class="btn__icon" name="view"></Icon>
                </Link>
                <Link v-if="item.abilities.update" :href="`${url}/edit`" class="btn btn--icon btn--tertiary btn--sm" :aria-label="__('Edit')">
                    <Icon class="btn__icon" name="edit"></Icon>
                </Link>
            </div>
        </td>
    </tr>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';

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
            url() {
                return `${this.$parent.urls.index}/${this.item.id}`;
            },
        },

        methods: {
            destroy(url) {
                this.$inertia.delete(url, {
                    onBefore: () => confirm(this.__('Are you sure?')),
                    onStart: (visit) => this.$parent.deselect(this.item),
                });
            },
        },
    }
</script>
