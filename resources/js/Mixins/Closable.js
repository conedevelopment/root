export default {
    data() {
        return {
            isOpen: false,
        };
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

    methods: {
        open() {
            if (! this.isOpen) {
                this.isOpen = true;
                this.$dispatcher.emit('open');
            }
        },
        close() {
            if (this.isOpen) {
                this.isOpen = false;
                this.$dispatcher.emit('close');
            }
        },
        toggle() {
            this.isOpen ? this.close() : this.open();
        },
    },
}
