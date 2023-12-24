import ApexCharts from 'apexcharts';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('widget', (config) => {
        return {
            init() {
                const chart = new ApexCharts(this.$el, config);

                chart.render();
            },
        };
    });
});
