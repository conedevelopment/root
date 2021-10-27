<template>
    <tr :class="{ 'is-selected': selected }">
        <th scope="row">
            <input type="checkbox" v-model="selected">
        </th>
        <td
            v-for="field in item.fields"
            :key="`${item.id}-${field.name}`"
            v-html="field.formatted_value"
        ></td>
    </tr>
</template>

<script>
    export default {
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
        },
    }
</script>
