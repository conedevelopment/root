<template>
    <div class="card">
        <ul>
            <li v-for="field in model.fields" :key="field.name">
                <strong>{{ field.label }}</strong>: {{ field.formatted_value }}
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: {
            model: {
                type: Object,
                required: true,
            },
        },

        layout: function (h, page) {
            return h(this.resolveDefaultLayout(), () => page);
        },
    }
</script>
