<template>
    <div class="form-group form-group--vertical-check">
        <label class="form-check">
            <input class="form-check__control" type="checkbox">
            <span class="form-label form-check__label">{{ $attrs.label }}</span>
        </label>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: [Object, String, Number, Boolean],
                default: null,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        computed: {
            isSwitch() {
                return ! Array.isArray(this.modelValue);
            },
            checked() {
                const json = JSON.stringify(this.$attrs.value);

                return this.isSwitch
                    ? this.modelValue
                    : this.modelValue.some((value) => JSON.stringify(value) === json);
            },
        },

        methods: {
            update() {
                let value;

                if (this.isSwitch) {
                    value = ! this.modelValue;
                } else if (! this.checked) {
                    value = Array.from(this.modelValue);

                    value.push(this.$attrs.value);
                } else {
                    const json = JSON.stringify(this.$attrs.value);

                    value = Array.from(this.modelValue);

                    value.splice(this.modelValue.findIndex((item) => JSON.stringify(item) === json), 1);
                }

                this.$emit('update:modelValue', value);
            },
        },
    }
</script>
