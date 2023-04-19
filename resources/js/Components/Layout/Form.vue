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
                                v-if="model.trashed && model.abilities.restore"
                                type="button"
                                class="btn btn--warning"
                                :disabled="form.processing"
                                @click="restore"
                            >
                                {{ __('Restore') }}
                            </button>
                            <button
                                v-if="model.exists && (model.abilities.delete || model.abilities.forceDelete)"
                                type="button"
                                class="btn btn--delete"
                                :disabled="form.processing"
                                @click="destroy"
                            >
                                {{ model.trashed ? __('Delete permanently') : __('Delete') }}
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
        },

        mounted() {
            window.addEventListener('beforeunload', this.onBeforeunload);

            this.notifier = this.$inertia.on('before', (event) => {
                if (this.form.isDirty && event.detail.visit.method === 'get') {
                    return window.confirm(this.__('You may have unsaved form data. Are you sure you want to navigate away?'));
                }
            });
        },

        beforeUnmount() {
            this.notifier.call();
            this.notifier = null;
            window.removeEventListener('beforeunload', this.onBeforeunload);
        },

        data() {
            return {
                form: this.$inertia.form(Object.assign({}, this.model.data)),
                notifier: null,
            };
        },

        computed: {
            method() {
                return this.model.exists ? 'patch' : 'post';
            },
        },

        methods: {
            onBeforeunload(event) {
                if (this.form.isDirty) {
                    event.preventDefault();
                }
            },
            submit() {
                this.form.submit(this.method, this.model.url, {
                    onStart: () => {
                        this.form.clearErrors();
                    },
                    onFinish: () => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },
                });
            },
            destroy() {
                this.$inertia.delete(this.model.url, {
                    onBefore: () => window.confirm(this.__('Are you sure, you want to delete the resource?')),
                });
            },
            restore() {
                this.$inertia.post(`${this.model.url}/restore`, {
                    onBefore: () => window.confirm(this.__('Are you sure, you want to restore the resource?')),
                });
            },
        },
    }
</script>
