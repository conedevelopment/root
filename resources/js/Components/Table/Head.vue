<template>
    <thead>
        <tr>
            <th v-if="$parent.actions.length > 0" scope="col">
                <label class="form-check" style="position: relative; top: 3px;">
                    <input ref="input" class="form-check__control" type="checkbox" v-model="selected">
                    <span class="form-label form-check__label" aria-label=""></span>
                </label>
            </th>
            <th v-for="column in columns" :key="column.name" scope="col">
                {{ column.label }}
            </th>
            <th scope="col">&nbsp;</th>
        </tr>
    </thead>
</template>

<script>
    export default {
        props: {
            items: {
                type: Array,
                required: true,
            },
            selection: {
                type: Array,
                required: true,
            },
            form: {
                type: Object,
                required: true,
            },
        },

        emits: ['update:selection'],

        watch: {
            indeterminate(newValue, oldValue) {
                this.$refs.input.indeterminate = newValue;
            },
        },

        computed: {
            columns() {
                const fields = this.items?.[0]?.fields || [];

                return fields.map((field) => ({
                    label: field.label,
                    name: field.name,
                    sortable: field.sortable,
                }));
            },
            selected: {
                get() {
                    return this.selection.length > 0
                        && this.selection.length === this.items.length;
                },
                set(value) {
                    value ? this.selectAll() : this.clearSelection();
                },
            },
            indeterminate() {
                return this.selection.length > 0
                    && this.selection.length < this.items.length;
            },
        },

        methods: {
            selectAll(matching = false) {
                // append all matching to query string
                this.$emit(
                    'update:selection',
                    this.items.map((item) => item.id)
                );
            },
            clearSelection() {
                this.$emit('update:selection', []);
            },
        },
    }
</script>
