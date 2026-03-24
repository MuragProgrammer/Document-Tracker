document.addEventListener('DOMContentLoaded', function() {

    const {
        labels = [],
        counts = [],
        sectionNames = [],
        sectionChartData = [],
        trendLabels = [],
        trendCounts = []
    } = window.REPORTS || {};

    // -----------------------------
    // FIX: Convert counts object → array
    // -----------------------------
    const countsArray = Object.values(counts || {});

    // -----------------------------
    // Colors (MATCH labels exactly)
    // -----------------------------
    const baseColors = [
        '#fbac46', // Pending
        '#2b358e', // Under Review
        '#bd3737', // End of Cycle
        '#318BF2'  // Reopened
    ];

    const sectionColors = {
        "Pending": "#fbac46",
        "Under Review": "#2b358e",
        "End": "#bd3737",
        "Reopened": "#318BF2"
    };

    // -----------------------------
    // Chart Creator (SAFE)
    // -----------------------------
    const createChart = (id, type, data, options = {}) => {
        const canvas = document.getElementById(id);
        if (!canvas) return;

        return new Chart(canvas.getContext('2d'), {
            type,
            data,
            options
        });
    };

    // -----------------------------
    // DONUT CHART
    // -----------------------------
    if (countsArray.length && countsArray.some(v => v > 0)) {
        createChart('donutChart', 'doughnut', {
            labels: labels,
            datasets: [{
                data: countsArray,
                backgroundColor: baseColors
            }]
        }, {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        });
    }

    // -----------------------------
    // LINE CHART (WHOLE NUMBERS)
    // -----------------------------
    if (trendCounts.length) {
        createChart('lineChart', 'line', {
            labels: trendLabels,
            datasets: [{
                label: 'Documents Created',
                data: trendCounts,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.2)',
                fill: true,
                tension: 0.4
            }]
        }, {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        stepSize: 1,
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    }
                }
            }
        });
    }

    // -----------------------------
    // STACKED BAR (SECTION)
    // -----------------------------
    if (sectionNames.length) {

        const datasets = sectionChartData.map(item => ({
            label: item.label,
            data: item.data,
            backgroundColor: sectionColors[item.label] || '#94a3b8',
            borderRadius: 6
        }));

        createChart('sectionStackedChart', 'bar', {
            labels: sectionNames,
            datasets: datasets
        }, {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Document Status per Section'
                },
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        stepSize: 1
                    }
                }
            }
        });
    }

    // -----------------------------
    // CARD CLICK FILTERING
    // -----------------------------
    const statusMap = {
        "Draft": "CREATED",
        "Pending": "PENDING",
        "Under Review": "UNDER REVIEW",
        "End of Cycle": "END OF CYCLE",
        "Reopened": "REOPENED"
    };

    document.querySelectorAll('.cards-row .card').forEach(card => {
        card.addEventListener('click', () => {
            const statusInput = document.querySelector('select[name="status"]');

            if (statusInput) {
                const backendStatus = statusMap[card.dataset.status] || "";
                statusInput.value = backendStatus;

                document.getElementById('filterForm').submit();
            }
        });
    });

    // -----------------------------
    // DEBUG (optional)
    // -----------------------------
    // console.log(window.REPORTS);

});
