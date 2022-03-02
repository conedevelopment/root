<template>
    <form @submit.prevent="submit">
        <Modal ref="modal" :title="action.name">
            <template #footer>
                <button type="submit" class="btn btn--primary">
                    {{ __('Run') }}
                </button>
            </template>
        </Modal>
    </form>
</template>

<script>
    export default {
        props: {
            action: {
                type: Object,
                required: true,
            },
        },

        data() {
            return {
                form: this.$inertia.form(this.action.key, Object.assign({}, this.action.data)),
            };
        },

        methods: {
            open() {
                this.$refs.modal.open();
            },
            submit() {
                this.form.transform((data) => ({
                    ...data,
                    models: this.models,
                    all: false,
                })).post(this.action.url, {
                    onSuccess: () => {
                        this.$emit('update:models', []);
                    },
                });
            },
        },
    }
</script>
