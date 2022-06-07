<template>
    <div class="app-operation">
        <Link :href="model.url" class="btn btn--primary btn--icon">
            <Icon name="arrow-back" class="btn__icon btn__icon--sm"></Icon>
            {{ model_name }}
        </Link>
    </div>
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
                                v-if="model.exists"
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
    import { Link } from '@inertiajs/inertia-vue3';

    export default {
        components: {
            Link,
        },

        props: {
            model: {
                type: Object,
                required: true,
            },
            model_name: {
                type: String,
                required: true,
            },
        },

        data() {
            return {
                form: this.$inertia.form(Object.assign({}, this.model.data)),
            };
        },

        computed: {
            method() {
                return this.model.exists ? 'patch' : 'post';
            },
        },

        methods: {
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
                    onBefore: () => window.confirm(this.__('Are you sure?')),
                });
            },
        },
    }
</script>
