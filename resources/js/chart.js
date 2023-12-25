import ApexCharts from 'apexcharts';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('chart', (config) => {
        return {
            init() {
                const chart = new ApexCharts(this.$el, config);

                chart.render();
            },
        };
    });
});
