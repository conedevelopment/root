<template>
    <div class="dropdown">
        <button
            type="button"
            class="dropdown__btn"
            :aria-expanded="isOpen ? 'true' : 'false'"
            :aria-controls="id"
            @click="toggle"
        >
            <Icon name="more-vert"></Icon>
        </button>
        <ul class="dropdown__menu" :id="id">
            <slot></slot>
        </ul>
    </div>
</template>

<script>
    import Closable from './../Mixins/Closable';

    export default {
        mixins: [Closable],

        props: {
            id: {
                type: String,
                required: true,
            },
        },

        mounted() {
            window.addEventListener('keyup', (event) => {
                if (this.isOpen && event.code === 'Escape') {
                    this.close();
                }
            });

            window.addEventListener('click', (event) => {
                if (this.isOpen && ! this.$el.contains(event.target)) {
                    this.close();
                }
            });
        },
    }
</script>
