<template>
    <tr :class="{ 'is-selected': selected }">
        <th v-if="$parent.actions.length > 0" scope="row">
            <input type="checkbox" v-model="selected">
        </th>
        <td
            v-for="field in item.fields"
            :key="`${item.id}-${field.name}`"
            v-html="field.formatted_value"
        ></td>
        <td>
            <button type="button" v-if="item.abilities.delete" @click="destroy(item.urls.show)">
                Delete
            </button>
            <Link v-if="item.abilities.view" :href="item.urls.show">
                View
            </Link>
            <Link v-if="item.abilities.update" :href="item.urls.edit">
                Edit
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
                    onBefore: () => confirm('Are you sure?'),
                    onStart: (visit) => this.deselect(),
                });
            },
        },
    }
</script>
