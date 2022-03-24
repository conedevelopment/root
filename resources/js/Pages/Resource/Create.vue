<template>
    <div>
        <FormHandler
            v-for="field in model.fields"
            v-bind="field"
            v-model="$parent.form[field.name]"
            :form="$parent.form"
            :key="field.name"
            :name="field.name"
        ></FormHandler>
    </div>
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
            return h(this.resolveDefaultLayout(), () => h(Form, {
                model: page.props.model,
                url: page.props.urls.index,
            }, () => page));
        },
    }
</script>
