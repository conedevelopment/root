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
                const extract = this.extracts.find((extract) => {
                    return extract.key = event.target.value;
                });

                this.$inertia.visit(extract.url);
            },
        },
    }
</script>
