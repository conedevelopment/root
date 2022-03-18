<template>
    <form @submit.prevent="submit" @reset.prevent="form.reset">
        <div class="form-group-stack">
            <FormHandler
                v-for="field in model.fields"
                v-bind="field"
                v-model="form[field.name]"
                :form="form"
                :key="field.name"
                :name="field.name"
            ></FormHandler>

            <div class="form-group--submit">
                <button type="submit" class="btn btn--primary" :disabled="form.processing">
                    {{ __('Save') }}
                </button>
                <button type="button" class="btn btn--delete" :disabled="form.processing">
                    {{ __('Delete') }}
                </button>
            </div>
        </div>
    </form>
</template>

<script>
    import Form from './../../Components/Layout/Form';

    export default {
        props: {
            urls: {
                type: Object,
                required: true,
            },
            model: {
                type: Object,
                required: true,
            },
        },

        layout: function (h, page) {
            return h(this.resolveDefaultLayout(), () => h(Form, () => page));
        },

        data() {
            return {
                form: this.$inertia.form(window.location.pathname, Object.assign({}, this.model.data)),
            };
        },

        methods: {
            submit() {
                this.form.clearErrors();
                this.form.patch(`${this.urls.index}/${this.model.id}`);
            },
        },
    }
</script>
