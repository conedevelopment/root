<template>
    <form @submit.prevent="submit" @reset.prevent>
        <FormHandler
            nullable
            component="Select"
            v-model="_action"
            :name="name"
            :form="form"
            :label="__('Action')"
            :options="options"
        ></FormHandler>
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
                form: this.$inertia.form(this.name, {}),
            };
        },

        computed: {
            name() {
                return window.location.pathname + '-actions';
            },
            options() {
                return this.actions.map((action) => ({
                    value: action.key,
                    formatted_value: action.name,
                }));
            },
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
