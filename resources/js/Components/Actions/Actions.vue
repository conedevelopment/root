<template>
    <div>
        <form @submit.prevent="submit" class="form--inline">
            <div class="form-group form-group--inline">
                <label class="form-label" for="actions-select">{{ __('Action') }}</label>
                <select id="actions-select" class="form-control" v-model="_action">
                    <option :value="null">{{ __('Select Action') }}</option>
                    <option v-for="(action, index) in actions" :value="index" :key="index">
                        {{ action.name }}
                    </option>
                </select>
            </div>
            <button
                type="submit"
                class="btn btn--primary"
                :disabled="_action === null || selection.length === 0"
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
                :selection="selection"
                @error="$emit('error', action)"
                @success="$emit('success', action)"
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
            selection: {
                type: Array,
                default: () => [],
            },
        },

        emits: ['success', 'error'],

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
