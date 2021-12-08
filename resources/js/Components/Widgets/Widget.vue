<template>
    <div v-html="content"></div>
</template>

<script>
    export default {
        props: {
            template: {
                type: String,
                default: null,
            },
            async: {
                type: Boolean,
                default: false,
            },
            url: {
                type: String,
                default: null,
            }
        },

        inheritAttrs: false,

        mounted() {
            if (this.async) {
                this.fetch();
            }
        },

        data() {
            return {
                content: this.template,
            };
        },

        methods: {
            fetch() {
                this.$http.get(this.url)
                    .then((response) => {
                        this.content = response.data;
                    });
            },
        },
    }
</script>
