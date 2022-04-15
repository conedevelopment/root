<template>
    <form @submit.prevent="submit">
        <Modal ref="modal" :title="action.name">
            <FormHandler
                v-for="field in action.fields"
                v-bind="field"
                v-model="form[field.name]"
                :form="form"
                :key="field.name"
                :name="field.name"
            ></FormHandler>

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
            selection: {
                type: Array,
                required: true,
            },
            allMatching: {
                type: Boolean,
                required: true,
            },
        },

        emits: ['success', 'error'],

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
                    all: this.allMatching,
                    models: this.selection,
                })).post(this.action.url, {
                    onSuccess: () => {
                        this.$emit('success');
                        this.$refs.modal.close();
                    },
                    onError: (errors) => {
                        this.$emit('error');
                    },
                }, { errorBag: this.action.key });
            },
        },
    }
</script>
