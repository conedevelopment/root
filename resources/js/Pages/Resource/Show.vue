<template>
    <div>
        <Actions
            v-if="actions.length > 0"
            :selection="[model.id]"
            :actions="actions"
            @success="clearSelection"
        ></Actions>
        <div class="card">
            <ul>
                <li v-for="field in model.fields" :key="field.name">
                    <strong>{{ field.label }}</strong>: {{ field.formatted_value }}
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import Actions from './../../Components/Actions/Actions';

    export default {
        components: {
            Actions,
        },

        props: {
            actions: {
                type: Array,
                default: () => [],
            },
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
