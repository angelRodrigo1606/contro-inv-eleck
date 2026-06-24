import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;

Alpine.data('themeToggle', () => ({
    theme: localStorage.getItem('theme')
        || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),

    init() {
        this.apply();
    },

    toggle() {
        this.theme = this.theme === 'dark' ? 'light' : 'dark';
        this.apply();
    },

    apply() {
        if (this.theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('theme', this.theme);
    },
}));

Alpine.start();

const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

function formatMonthLabel(month) {
    const [year, monthIndex] = month.split('-');

    return `${monthNames[parseInt(monthIndex, 10) - 1]} ${year}`;
}

function isDarkTheme() {
    return document.documentElement.classList.contains('dark');
}

function getChartColors() {
    return isDarkTheme()
        ? { grid: 'rgba(248, 250, 252, 0.1)', text: 'rgba(248, 250, 252, 0.7)' }
        : { grid: 'rgba(28, 46, 92, 0.1)', text: 'rgba(28, 46, 92, 0.7)' };
}

function initStockMovementChart() {
    const canvas = document.getElementById('stockMovementChart');

    if (! canvas) {
        return;
    }

    const labels = JSON.parse(canvas.dataset.labels || '[]').map(formatMonthLabel);
    const entries = JSON.parse(canvas.dataset.entries || '[]');
    const exits = JSON.parse(canvas.dataset.exits || '[]');
    const colors = getChartColors();

    const chart = new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Entradas',
                    data: entries,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Salidas',
                    data: exits,
                    borderColor: 'rgb(245, 158, 11)',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: colors.text,
                        usePointStyle: true,
                        padding: 20,
                    },
                },
                tooltip: {
                    backgroundColor: isDarkTheme() ? 'rgba(30, 41, 59, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                    titleColor: isDarkTheme() ? 'rgb(248, 250, 252)' : 'rgb(28, 46, 92)',
                    bodyColor: isDarkTheme() ? 'rgb(248, 250, 252)' : 'rgb(28, 46, 92)',
                    borderColor: colors.grid,
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: (context) => `${context.dataset.label}: ${context.parsed.y} unidades`,
                    },
                },
            },
            scales: {
                x: {
                    grid: {
                        color: colors.grid,
                    },
                    ticks: {
                        color: colors.text,
                    },
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: colors.grid,
                    },
                    ticks: {
                        color: colors.text,
                    },
                    title: {
                        display: true,
                        text: 'Unidades',
                        color: colors.text,
                    },
                },
            },
        },
    });

    const observer = new MutationObserver(() => {
        const newColors = getChartColors();
        chart.options.plugins.legend.labels.color = newColors.text;
        chart.options.plugins.tooltip.backgroundColor = isDarkTheme() ? 'rgba(30, 41, 59, 0.95)' : 'rgba(255, 255, 255, 0.95)';
        chart.options.plugins.tooltip.titleColor = isDarkTheme() ? 'rgb(248, 250, 252)' : 'rgb(28, 46, 92)';
        chart.options.plugins.tooltip.bodyColor = isDarkTheme() ? 'rgb(248, 250, 252)' : 'rgb(28, 46, 92)';
        chart.options.plugins.tooltip.borderColor = newColors.grid;
        chart.options.scales.x.grid.color = newColors.grid;
        chart.options.scales.x.ticks.color = newColors.text;
        chart.options.scales.y.grid.color = newColors.grid;
        chart.options.scales.y.ticks.color = newColors.text;
        chart.options.scales.y.title.color = newColors.text;
        chart.update();
    });

    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStockMovementChart);
} else {
    initStockMovementChart();
}
