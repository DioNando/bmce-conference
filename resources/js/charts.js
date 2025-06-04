import { Chart, registerables } from 'chart.js/auto';
import { getColors } from './utils'; // Helper to get DaisyUI colors

Chart.register(...registerables);

document.addEventListener('DOMContentLoaded', function () {
    const colors = getColors(); // Fetch DaisyUI colors

    // Chart pour les meetings par statut
    const meetingStatusCtx = document.getElementById('meetingStatusChart');
    if (meetingStatusCtx) {
        const meetingStatusData = JSON.parse(meetingStatusCtx.dataset.stats);
        new Chart(meetingStatusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(meetingStatusData),
                datasets: [{
                    data: Object.values(meetingStatusData),
                    backgroundColor: [
                        colors.success, // Confirmé
                        colors.warning, // En attente
                        colors.error,   // Annulé
                        colors.info,    // Autre statut
                    ],
                    borderColor: colors.baseContent, // Use base-content for border
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: colors.baseContent // Text color for legend
                        }
                    },
                    title: {
                        display: true,
                        text: 'Répartition des réunions par statut',
                        color: colors.baseContent // Text color for title
                    }
                }
            }
        });
    }

    // Chart pour l'évolution des inscriptions
    const registrationChartCtx = document.getElementById('registrationChart');
    if (registrationChartCtx) {
        const registrationData = JSON.parse(registrationChartCtx.dataset.stats);
        new Chart(registrationChartCtx, {
            type: 'line',
            data: {
                labels: registrationData.labels,
                datasets: [
                    {
                        label: 'Émetteurs',
                        data: registrationData.issuers,
                        borderColor: colors.warning,
                        backgroundColor: colors.warning + '33', // Lighter version for area
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Investisseurs',
                        data: registrationData.investors,
                        borderColor: colors.primary,
                        backgroundColor: colors.primary + '33', // Lighter version for area
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Évolution des inscriptions',
                        color: colors.baseContent
                    },
                    legend: {
                        labels: {
                            color: colors.baseContent
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nombre d\'utilisateurs',
                            color: colors.baseContent
                        },
                        ticks: {
                            color: colors.baseContent
                        },
                        grid: {
                            color: colors.base300 // Grid line color
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date',
                            color: colors.baseContent
                        },
                        ticks: {
                            color: colors.baseContent
                        },
                        grid: {
                            color: colors.base300 // Grid line color
                        }
                    }
                }
            }
        });
    }

    // Chart pour les organisations par type
    const orgTypeChartCtx = document.getElementById('organizationTypeChart');
    if (orgTypeChartCtx) {
        const orgTypeData = JSON.parse(orgTypeChartCtx.dataset.stats);
        new Chart(orgTypeChartCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(orgTypeData),
                datasets: [{
                    label: 'Organisations par type',
                    data: Object.values(orgTypeData),
                    backgroundColor: [
                        colors.primary,
                        colors.secondary,
                        colors.accent,
                        colors.info,
                        colors.success,
                    ],
                    borderColor: colors.baseContent,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: true,
                        text: 'Organisations par type',
                        color: colors.baseContent
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: colors.baseContent
                        },
                        grid: {
                            color: colors.base300
                        }
                    },
                    x: {
                        ticks: {
                            color: colors.baseContent
                        },
                        grid: {
                            color: colors.base300
                        }
                    }
                }
            }
        });
    }
});
