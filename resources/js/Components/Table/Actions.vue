<template>
    <form @submit.prevent="submit" @reset.prevent>
        <select v-model="_action">
            <option :value="null" disabled>Action</option>
            <option v-for="action in actions" :key="action.key" :value="action.key">
                {{ action.name }}
            </option>
        </select>
        <button type="submit" :disabled="form.processing || _action === null || models.length === 0">
            Run
        </button>
    </form>
</template>

<script>
    export default {
        props: {
            actions: {
                type: Array,
                default: () => [],
            },
            models: {
                type: Array,
                default: () => [],
            },
        },

        emits: ['update:models'],

        data() {
            return {
                _action: null,
                form: this.$inertia.form(window.location.pathname + '-actions', {}),
            };
        },

        methods: {
            submit() {
                const action = this.actions.find((action) => {
                    return action.key = this._action;
                });

                this.form.transform((data) => ({
                    ...data,
                    models: this.models,
                    all: false,
                })).post(action.url, {
                    onSuccess: () => {
                        this.$emit('update:models', []);
                    },
                });
            },
        },
    }
</script>
