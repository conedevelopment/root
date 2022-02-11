<template>
    <thead>
        <tr v-if="columns.length > 0">
            <th v-if="$parent.actions.length > 0" scope="col">
                <label class="form-check" style="position: relative; top: 3px;">
                    <input ref="input" class="form-check__control" type="checkbox" v-model="selected">
                    <span class="form-label form-check__label" aria-label="Select/unselect [title here]"></span>
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
        watch: {
            indeterminate(value, oldValue) {
                this.$refs.input.indeterminate = value;
            },
        },

        computed: {
            columns() {
                const fields = this.$parent.query.data?.[0]?.fields || [];

                return fields.map((field) => {
                    return {
                        label: field.label,
                        name: field.name,
                        sortable: field.sortable,
                    };
                });
            },
            selected: {
                get() {
                    return this.$parent.selection.length > 0
                        && this.$parent.selection.length === this.$parent.query.data.length;
                },
                set(value) {
                    value ? this.selectAll() : this.clearSelection();
                },
            },
            indeterminate() {
                return this.$parent.selection.length > 0
                    && this.$parent.selection.length < this.$parent.query.data.length;
            },
        },

        methods: {
            selectAll(matching = false) {
                // append all matching to query string

                this.$parent.selection = this.$parent.query.data.map((item) => item.id);
            },
            clearSelection() {
                this.$parent.selection = [];
            },
        },
    }
</script>
