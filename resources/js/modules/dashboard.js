document.addEventListener('DOMContentLoaded', () => {
    const data = window.dashboardData || {};

    const trendCtx = document.getElementById('salesTrendChart');
    if (trendCtx && window.Chart) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: data.salesTrendLabels || [],
                datasets: [
                    {
                        label: 'Ventas',
                        data: data.salesTrend || [],
                        borderColor: '#1b77d3',
                        backgroundColor: 'rgba(27, 119, 211, 0.12)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointBackgroundColor: '#1b77d3',
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
                        ticks: { color: '#94a3b8', font: { weight: '600' } },
                    },
                    y: {
                        grid: { color: '#eef2f7' },
                        ticks: {
                            color: '#94a3b8',
                            font: { weight: '600' },
                            callback: (value) => `€ ${value}`,
                        },
                    },
                },
            },
        });
    }

    const donutCtx = document.getElementById('topCategoriesChart');
    if (donutCtx && window.Chart) {
        const categories = data.topCategories || [];
        const labels = categories.map((c) => c.name);
        const values = categories.map((c) => Number(c.total_qty || 0));
        const colors = ['#1d4ed8', '#10b981', '#f59e0b', '#6366f1', '#ef4444'];

        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [
                    {
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 0,
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
});
