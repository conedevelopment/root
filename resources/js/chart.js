import ApexCharts from 'apexcharts';

Alpine.data('chart', (config) => {
    return {
        init() {
            const chart = new ApexCharts(this.$el, config);

            chart.render();
        },
    };
});
