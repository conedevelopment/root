<template>
    <FormHandler
        v-for="field in model.fields"
        v-bind="field"
        v-model="$parent.form[field.name]"
        :form="$parent.form"
        :key="field.name"
        :name="field.name"
    />
</template>

<script>
    import Form from './../../Components/Layout/Form.vue';

    let key = new Date().getTime();

    export default {
        props: {
            model: {
                type: Object,
                required: true,
            },
        },

        layout: function (h, page) {
            if (Object.keys(page.props.errors).length === 0) {
                key = new Date().getTime();
            }

            return h(this.resolveDefaultLayout(), () => h(Form, {
                key: key,
                model: page.props.model,
            }, () => page));
        },
    }
</script>
