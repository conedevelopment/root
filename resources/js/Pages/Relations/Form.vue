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

    export default {
        props: {
            model: {
                type: Object,
                required: true,
            },
            resource: {
                type: Object,
                required: true,
            },
            parent: {
                type: Object,
                required: true,
            },
            field: {
                type: Object,
                required: true,
            },
        },

        layout: function (h, page) {
            return h(this.resolveDefaultLayout(), () => h(Form, {
                model: page.props.model,
                model_name: page.props.model.exists ? page.props.field.related_name : page.props.field.name,
            }, () => page));
        },
    }
</script>
