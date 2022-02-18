<template>
    <tr :class="{ 'is-selected': selected }">
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
            <button
                type="button"
                class="btn btn--icon btn--tertiary btn--sm"
                v-if="item.abilities.delete"
                @click="destroy(url)"
            >
                <Icon class="btn__icon" name="delete"></Icon>
            </button>
            <Link v-if="item.abilities.view" :href="url" class="btn btn--icon btn--tertiary btn--sm">
                <Icon class="btn__icon" name="view"></Icon>
            </Link>
            <Link v-if="item.abilities.update" :href="`${url}/edit`" class="btn btn--icon btn--tertiary btn--sm">
                <Icon class="btn__icon" name="edit"></Icon>
            </Link>
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
                    return this.$parent.selection.includes(this.item.id);
                },
                set(value) {
                    value ? this.select() : this.deselect();
                },
            },
            url() {
                return `${this.$parent.urls.index}/${this.item.id}`;
            },
        },

        methods: {
            select() {
                if (! this.selected) {
                    this.$parent.selection.push(this.item.id);
                }
            },
            deselect() {
                const index = this.$parent.selection.indexOf(this.item.id);

                if (index !== -1) {
                    this.$parent.selection.splice(index, 1);
                }
            },
            destroy(url) {
                this.$inertia.delete(url, {
                    onBefore: () => confirm(this.__('Are you sure?')),
                    onStart: (visit) => this.deselect(),
                });
            },
        },
    }
</script>
