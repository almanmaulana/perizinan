document.addEventListener('DOMContentLoaded', () => {

    // ==== Role user dari blade ====
    const userRole = window.userRole || ''; // pastikan dikirim dari blade: window.userRole = '{{ $user->role }}';

    // ==== Deteksi dark mode ====  
    const isDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

    // ==== Warna dasar untuk mode ====  
    const textColor = isDark ? '#f9fafb' : '#111827';
    const gridColor = isDark ? 'rgba(255,255,255,0.25)' : 'rgba(0,0,0,0.15)';
    const tooltipBg = isDark ? '#1F2937' : '#ffffff';
    const tooltipTitleColor = isDark ? '#f9fafb' : '#111827';
    const tooltipBodyColor = isDark ? '#f9fafb' : '#111827';

    // ==== Warna untuk jenis izin (normalisasi lowercase) ====  
    const colorMap = {
        'sakit': '#3B82F6',
        'kegiatan': '#FACC15',
        'lainnya': '#6B7280'
    };

    // ==== Chart Izin Bulan Ini ====  
    const izinCtx = document.getElementById('izinBarChart')?.getContext('2d');
    if (izinCtx) {
        const dataColors = (window.izinLabels || []).map(label => {
            const key = label.toLowerCase();
            return colorMap[key] || '#6B7280';
        });

        new Chart(izinCtx, {
            type: 'bar',
            data: {
                labels: window.izinLabels || [],
                datasets: [{
                    label: 'Jumlah Izin',
                    data: window.izinCounts || [],
                    backgroundColor: dataColors,
                    borderRadius: 12,
                    barPercentage: 0.5,
                    maxBarThickness: 50
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: textColor,
                            font: { weight: 'bold' },
                            generateLabels: chart => {
                                return window.izinLabels.map(label => ({
                                    text: label,
                                    fillStyle: colorMap[label.toLowerCase()] || '#6B7280',
                                    font: { weight: 'bold' }
                                }));
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        titleColor: tooltipTitleColor,
                        bodyColor: tooltipBodyColor,
                        bodyFont: { weight: 'bold' },
                        callbacks: {
                            label: ctx => `${ctx.label}: ${ctx.raw} izin`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: textColor,
                            stepSize: 1,
                            font: { weight: '500' }
                        },
                        grid: {
                            color: gridColor,
                            lineWidth: 1.8,
                            drawBorder: true,
                            drawTicks: true
                        }
                    },
                    x: {
                        ticks: { color: textColor, font: { weight: '500' } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ==== Chart Denda hanya untuk role 'keamanan' ====  
    if (userRole === 'keamanan') {
        const dendaCtx = document.getElementById('dendaPieChart')?.getContext('2d');
        if (dendaCtx) {
            new Chart(dendaCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Dibayar','Belum Dibayar'],
                    datasets: [{
                        data: [window.totalDendaDibayar || 0, window.totalDendaBelum || 0],
                        backgroundColor: ['#16A34A','#DC2626'],
                        borderWidth: 2,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { 
                            position: 'bottom', 
                            labels: { color: textColor, font: { size: 14, weight: '500' } } 
                        },
                        tooltip: {
                            backgroundColor: tooltipBg,
                            titleColor: tooltipTitleColor,
                            bodyColor: tooltipBodyColor,
                            callbacks: {
                                label: ctx => `${ctx.label}: Rp ${ctx.raw.toLocaleString()}`
                            }
                        }
                    }
                }
            });
        }
    }

});
