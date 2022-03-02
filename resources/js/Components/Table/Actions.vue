<template>
    <div>
        <form @submit.prevent="submit">
            <div class="form-group">
                <label class="form-label" for="actions-select">{{ __('Action') }}</label>
                <select id="actions-select" class="form-control" v-model="_action">
                    <option :value="null">{{ __('Action') }}</option>
                    <option v-for="(action, index) in actions" :value="index" :key="index">
                        {{ action.name }}
                    </option>
                </select>
            </div>
            <button
                type="submit"
                class="btn btn--primary"
                :disabled="_action === null || models.length === 0"
            >
                {{ __('Run') }}
            </button>
        </form>
        <div>
            <Action
                v-for="action in actions"
                ref="action"
                :action="action"
                :key="action.key"
                :models="models"
                @success="$emit('update:models', [])"
            ></Action>
        </div>
    </div>
</template>

<script>
    import Action from './Action';

    export default {
        components: {
            Action,
        },

        props: {
            actions: {
                type: Array,
                default: () => [],
            },
            models: {
                type: Array,
                default: () => [],
            },
        },

        emits: ['update:models'],

        data() {
            return {
                _action: null,
            };
        },

        methods: {
            submit() {
                this.$refs.action[this._action].open();
            },
        },
    }
</script>
