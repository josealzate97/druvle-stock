document.addEventListener("DOMContentLoaded", () => {

    const { planDistribution, tenantGrowth } = window.supportDashboardData || {};

    // Colores base
    const colors = {
        blue:   '#1b77d3',
        green:  '#10b981',
        gold:   '#f59e0b',
        purple: '#7c3aed',
        red:    '#ef4444',
    };

    const isDark = () => document.body.classList.contains('theme-dark');
    const textColor = () => isDark() ? '#94a3b8' : '#475569';
    const gridColor = () => isDark() ? '#1f2a3a' : '#eef1f5';

    // ── Gráfico crecimiento de tenants ──
    const growthCtx = document.getElementById('tenantGrowthChart');
    const growthEmpty = document.getElementById('tenantGrowthEmpty');
    const hasGrowthData = tenantGrowth && tenantGrowth.values && tenantGrowth.values.some(v => v > 0);

    if (growthCtx && hasGrowthData) {
        new Chart(growthCtx, {
            type: 'bar',
            data: {
                labels: tenantGrowth.labels,
                datasets: [{
                    label: 'Negocios registrados',
                    data: tenantGrowth.values,
                    backgroundColor: 'rgba(27,119,211,0.18)',
                    borderColor: colors.blue,
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.raw} negocios` } }
                },
                scales: {
                    x: { grid: { color: gridColor() }, ticks: { color: textColor() } },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor() },
                        ticks: { color: textColor(), precision: 0 }
                    }
                }
            }
        });
    } else if (growthCtx && growthEmpty) {
        growthCtx.style.display = 'none';
        growthEmpty.style.display = 'flex';
    }

    // ── Gráfico distribución de planes ──
    const planCtx = document.getElementById('planDistributionChart');
    const planEmpty = document.getElementById('planDistributionEmpty');
    const hasPlanData = planDistribution && planDistribution.values && planDistribution.values.some(v => v > 0);

    if (planCtx && hasPlanData) {
        const planColors = [colors.purple, colors.blue, colors.gold];
        const chart = new Chart(planCtx, {
            type: 'doughnut',
            data: {
                labels: planDistribution.labels,
                datasets: [{
                    data: planDistribution.values,
                    backgroundColor: planColors.map(c => c + '33'),
                    borderColor: planColors,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.raw} negocios` } }
                }
            }
        });

        // Leyenda
        const legend = document.getElementById('planDistributionLegend');
        if (legend) {
            legend.innerHTML = planDistribution.labels.map((label, i) => `
                <div class="chart-legend-item">
                    <span class="chart-legend-dot" style="background:${planColors[i]}"></span>
                    <span>${label}</span>
                    <span class="chart-legend-val">${planDistribution.values[i]}</span>
                </div>
            `).join('');
        }
    } else if (planCtx && planEmpty) {
        planCtx.style.display = 'none';
        planEmpty.style.display = 'flex';
    }

});
