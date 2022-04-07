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
                <div class="th-helper">
                    {{ column.label }}
                    <button v-if="column.sortable" type="button" @click="sort(column.name)" class="table-sort-btn">
                        <Icon :name="icon(column.name)"></Icon>
                    </button>
                </div>
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
            query: {
                type: Object,
                required: true,
            },
            columns: {
                type: Array,
                required: true,
            },
        },

        emits: ['update:query'],

        watch: {
            indeterminate(newValue, oldValue) {
                this.$refs.input.indeterminate = newValue;
            },
        },

        computed: {
            selected: {
                get() {
                    return this.selection.length > 0
                        && this.selection.length === this.items.length;
                },
                set(value) {
                    value ? this.$parent.selectAll() : this.$parent.clearSelection();
                },
            },
            indeterminate() {
                return this.selection.length > 0
                    && this.selection.length < this.items.length;
            },
        },

        methods: {
            icon(name) {
                if (this.query.sort.by !== name) {
                    return 'unfold-more';
                }

                return this.query.sort.order === 'desc' ? 'unfold-more-top' : 'unfold-more-bottom';
            },
            sort(name) {
                this.query.sort.by = name;
                this.query.sort.order = this.query.sort.order === 'desc' ? 'asc' : 'desc';

                this.$emit('update:query');
            },
        },
    }
</script>
