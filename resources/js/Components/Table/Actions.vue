<template>
    <form @submit.prevent="submit" @reset.prevent>
        <select v-model="action">
            <option :value="null" disabled>Action</option>
            <option v-for="action in actions" :key="action.key" :value="action.key">
                {{ action.name }}
            </option>
        </select>
        <button type="submit" :disabled="form.processing || action === null">
            Run
        </button>
    </form>
</template>

<script>
    export default {
        props: {
            actions: {
                type: Array,
                required: true,
            },
        },

        data() {
            return {
                action: null,
                form: this.$inertia.form(window.location.pathname + '-actions', {
                    // action fields
                }),
            };
        },

        methods: {
            submit() {
                this.form.transform((data) => ({
                    ...data,
                    selection: this.$parent.selection,
                })).post(`${this.$parent.query.path}/actions/${this.action}`);
            },
        },
    }
</script>
