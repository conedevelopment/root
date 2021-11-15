<template>
    <form @submit.prevent @reset.prevent>
        <select @change="submit">
            <option value="" disabled selected>Extract</option>
            <option v-for="extract in extracts" :key="extract.key" :value="extract.key">
                {{ extract.name }}
            </option>
        </select>
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

        methods: {
            submit(event) {
                this.$inertia.visit(`${this.$parent.query.path}/extracts/${event.target.value}`);
            },
        },
    }
</script>
