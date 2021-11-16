<template>
    <form @submit.prevent="submit" @reset.prevent>
        <select v-model="form._action">
            <option :value="null" disabled>Action</option>
            <option v-for="action in actions" :key="action.key" :value="action.key">
                {{ action.name }}
            </option>
        </select>
        <button type="submit" :disabled="form.processing || form._action === null || models.length === 0">
            Run
        </button>
    </form>
</template>

<script>
    export default {
        props: {
            url: {
                type: String,
                required: true,
            },
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
                form: this.$inertia.form(window.location.pathname + '-actions', {
                    _action: null,
                }),
            };
        },

        methods: {
            submit(event) {
                this.form.transform((data) => ({
                    ...data,
                    models: this.models,
                    all: false,
                })).post(this.url, {
                    onSuccess: () => {
                        this.$emit('update:models', []);
                    },
                });
            },
        },
    }
</script>
