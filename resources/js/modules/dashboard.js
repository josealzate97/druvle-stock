document.addEventListener('DOMContentLoaded', () => {
    const data = window.dashboardData || {};
    let trendChart = null;
    let donutChart = null;
    let ChartLib = null;

    const loadChartLib = async () => {
        if (!ChartLib) {
            const mod = await import('chart.js/auto');
            ChartLib = mod.default;
        }
        return ChartLib;
    };

    const renderCharts = async () => {
        const Chart = await loadChartLib();
        const isDark = document.body.classList.contains('theme-dark');
        const axisColor = isDark ? '#94a3b8' : '#94a3b8';
        const gridColor = isDark ? '#2b313c' : '#eef2f7';
        const lineColor = isDark ? '#9cc8ff' : '#1b77d3';
        const lineFill = isDark ? 'rgba(156, 200, 255, 0.18)' : 'rgba(27, 119, 211, 0.12)';
        const donutBorder = isDark ? '#14171d' : '#ffffff';

        const trendCtx = document.getElementById('salesTrendChart');
        if (trendCtx) {
            if (trendChart) {
                trendChart.destroy();
            }

            trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: data.salesTrendLabels || [],
                    datasets: [
                        {
                            label: 'Ventas',
                            data: data.salesTrend || [],
                            borderColor: lineColor,
                            backgroundColor: lineFill,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: lineColor,
                            pointBorderWidth: 0,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { intersect: false, mode: 'index' },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: axisColor, font: { weight: '600' } },
                        },
                        y: {
                            grid: { color: gridColor },
                            ticks: {
                                color: axisColor,
                                font: { weight: '600' },
                                callback: (value) => `€ ${value}`,
                            },
                        },
                    },
                },
            });
        }

        const donutCtx = document.getElementById('topCategoriesChart');
        if (donutCtx) {
            if (donutChart) {
                donutChart.destroy();
            }

            const categories = data.topCategories || [];
            const labels = categories.map((c) => c.name);
            const values = categories.map((c) => Number(c.total_qty || 0));
            const colors = ['#1d4ed8', '#10b981', '#f59e0b', '#6366f1', '#ef4444'];

            donutChart = new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [
                        {
                            data: values,
                            backgroundColor: colors,
                            borderWidth: 2,
                            borderColor: donutBorder,
                            hoverOffset: 6,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                    },
                },
            });

            const legend = document.getElementById('topCategoriesLegend');
            if (legend) {
                legend.innerHTML = labels
                    .map((label, index) => {
                        const total = values.reduce((sum, value) => sum + value, 0) || 1;
                        const percent = Math.round((values[index] / total) * 100);
                        return `
                            <div class="legend-item">
                                <span class="legend-dot" style="background:${colors[index % colors.length]}"></span>
                                <span>${label}</span>
                                <strong>${percent}%</strong>
                            </div>
                        `;
                    })
                    .join('');
            }
        }
    };

    renderCharts();

    const themeSwitch = document.getElementById('theme-switch');
    if (themeSwitch) {
        themeSwitch.addEventListener('change', () => {
            renderCharts();
        });
    }
});
