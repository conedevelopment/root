<template>
    <form @submit.prevent="submit" @reset.prevent="form.reset">
        <FormHandler
            v-for="field in model.fields"
            v-bind="field"
            v-model="form[field.name]"
            :key="field.name"
            :error="form.errors[field.name]"
            :disabled="form.processing"
            @update:modelValue="form.clearErrors(field.name)"
        ></FormHandler>

        <button type="submit" :disabled="form.processing">
            Save
        </button>
    </form>
</template>

<script>
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

        data() {
            return {
                form: this.$inertia.form(
                    window.location.pathname,
                    this.model.fields.reduce((stack, field) => Object.assign(stack, { [field.name]: field.value }), {})
                ),
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
