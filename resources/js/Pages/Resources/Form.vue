<template>
    <div class="form-layout">
        <div class="app-card card card--edit">
            <div class="card__inner">
                <form @submit.prevent="submit" @reset.prevent="_form.reset">
                    <div class="form-group-stack">
                        <FormHandler
                            v-for="field in form.fields"
                            v-bind="field"
                            v-model="_form[field.name]"
                            :form="_form"
                            :key="field.name"
                            :name="field.name"
                        />
                        <div class="form-group--submit">
                            <button type="submit" class="btn btn--primary" :disabled="_form.processing">
                                {{ __('Save') }}
                            </button>
                            <button
                                v-if="form.trashed && form.abilities.restore"
                                type="button"
                                class="btn btn--warning"
                                :disabled="_form.processing"
                                @click="restore"
                            >
                                {{ __('Restore') }}
                            </button>
                            <button
                                v-if="form.exists && (form.abilities.delete || form.abilities.forceDelete)"
                                type="button"
                                class="btn btn--delete"
                                :disabled="_form.processing"
                                @click="destroy"
                            >
                                {{ form.trashed ? __('Delete permanently') : __('Delete') }}
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
            form: {
                type: Object,
                required: true,
            },
        },

        mounted() {
            window.addEventListener('beforeunload', this.onBeforeunload);

            this.notifier = this.$inertia.on('before', (event) => {
                if (this._form.isDirty && event.detail.visit.method === 'get') {
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
                _form: this.$inertia.form(Object.assign({}, this.form.data)),
                notifier: null,
            };
        },

        computed: {
            method() {
                return this.form.exists ? 'patch' : 'post';
            },
        },

        methods: {
            onBeforeunload(event) {
                if (this._form.isDirty) {
                    event.preventDefault();
                }
            },
            submit() {
                this._form.submit(this.method, this.form.url, {
                    onStart: () => {
                        this.form.clearErrors();
                    },
                    onFinish: () => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },
                });
            },
            destroy() {
                this.$inertia.delete(this.form.url, {
                    onBefore: () => window.confirm(this.__('Are you sure, you want to delete the resource?')),
                });
            },
            restore() {
                this.$inertia.post(`${this.form.url}/restore`, {
                    onBefore: () => window.confirm(this.__('Are you sure, you want to restore the resource?')),
                });
            },
        },
    }
</script>
