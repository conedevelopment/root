<template>
    <form @submit.prevent="submit" @reset.prevent>
        <select v-model="form.action">
            <option :value="null" disabled>Action</option>
            <option v-for="action in actions" :key="action.key" :value="action.key">
                {{ action.name }}
            </option>
        </select>
        <button type="submit" :disabled="form.processing || form.action === null">
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
                form: this.$inertia.form(window.location.pathname + '-actions', {
                    action: null,
                }),
            };
        },

        methods: {
            submit() {
                this.form.transform((data) => ({
                    ...data,
                    models: this.$parent.selection,
                })).post(`${this.$parent.query.path}/action`);
            },
        },
    }
</script>
