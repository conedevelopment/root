<template>
    <form @submit.prevent @reset.prevent>
        <FormHandler
            nullable
            component="Select"
            v-model="_extract"
            :name="name"
            :form="form"
            :label="__('Extract')"
            :options="options"
            @update:modelValue="submit"
        ></FormHandler>
    </form>
</template>

<script>
    export default {
        props: {
            extracts: {
                type: Array,
                required: true,
            },
        },

        data() {
            return {
                _extract: null,
                form: this.$inertia.form(this.name, {}),
            };
        },

        computed: {
            name() {
                return window.location.pathname + '-extracts';
            },
            options() {
                return this.extracts.map((extract) => ({
                    value: extract.key,
                    formatted_value: extract.name,
                }));
            },
        },

        methods: {
            submit() {
                const extract = this.extracts.find((extract) => {
                    return extract.key = this._extract;
                });

                this.form.get(extract.url);
            },
        },
    }
</script>
