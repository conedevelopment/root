<template>
    <div class="form-layout">
        <div class="app-card card card--edit">
            <div class="card__inner">
                <form @submit.prevent="submit" @reset.prevent="form.reset">
                    <div class="form-group-stack">
                        <slot></slot>
                        <div class="form-group--submit">
                            <button type="submit" class="btn btn--primary" :disabled="form.processing">
                                {{ __('Save') }}
                            </button>
                            <button
                                v-if="exists"
                                type="button"
                                class="btn btn--delete"
                                :disabled="form.processing"
                                @click="destroy"
                            >
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            model: {
                type: Object,
                required: true,
            },
            url: {
                type: String,
                required: true,
            },
        },

        data() {
            return {
                form: this.$inertia.form(window.location.pathname, Object.assign({}, this.model.data)),
            };
        },

        computed: {
            exists() {
                return this.model.id !== null;
            },
            method() {
                return this.exists ? 'patch' : 'post';
            },
        },

        methods: {
            submit() {
                this.form.submit(this.method, this.url, {
                    onStart: () => {
                        this.form.clearErrors();
                    },
                    onFinish: () => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },
                });
            },
            destroy() {
                this.$inertia.delete(this.model.urls.destroy, {
                    onBefore: () => confirm(this.__('Are you sure?')),
                });
            },
        },
    }
</script>
