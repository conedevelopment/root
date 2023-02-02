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
            />
            <template #footer>
                <button
                    type="submit"
                    class="btn"
                    :class="{ 'btn--delete': action.destructive, 'btn--primary': ! action.destructive }"
                >
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
                form: this.$inertia.form(Object.assign({}, this.action.data)),
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
                })).post(this.action.url + window.location.search, {
                    onBefore: () => {
                        if (this.action.confirmable) {
                            return window.confirm(this.__('Are you sure?'));
                        }
                    },
                    onSuccess: () => {
                        this.$emit('success');
                        this.$refs.modal.close();
                        this.form.reset();
                    },
                    onError: (errors) => {
                        this.$emit('error');
                    },
                }, { errorBag: this.action.key });
            },
        },
    }
</script>
