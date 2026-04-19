{{-- Chart Component - Monthly Disbursement Visualization --}}
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-1 fw-bold text-secondary">
                    <i class="fas fa-chart-line me-2 text-primary"></i>Visualisasi Pencairan Bulanan
                </h6>
                <p class="text-muted small mb-0">Ringkasan total pencairan berkas per bulan dalam tahun ini.</p>
            </div>
            <select id="chartYearFilter" class="form-select form-select-sm" style="width: 120px;">
                @php $currentYear = date('Y'); @endphp
                @for($year = $currentYear; $year >= $currentYear - 5; $year--)
                    <option value="{{ $year }}" @selected($year == $currentYear)>{{ $year }}</option>
                @endfor
            </select>
        </div>
    </div>
    <div class="card-body">
        <div class="chart-container">
            <canvas id="monthlyDisbursementChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let monthlyChart = null;
    
    function formatCurrency(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(value / 1000000)) + 'M';
    }
    
    function initChart(data) {
        const ctx = document.getElementById('monthlyDisbursementChart');
        
        if (monthlyChart) {
            monthlyChart.destroy();
        }
        
        monthlyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [
                    {
                        label: 'Total Pencairan',
                        data: data,
                        borderColor: '#1d4ed8',
                        backgroundColor: 'rgba(29, 78, 216, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#1d4ed8',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#1e40af'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#1f2937',
                            font: { size: 12, weight: 600 },
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#1d4ed8',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                return formatCurrency(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(33, 37, 41, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6c757d',
                            font: { size: 11 },
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6c757d',
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }
    
    function loadChartData(year) {
        // Fetch data via AJAX
        fetch(`{{ route('dashboard.chart-data') }}?year=${year}`)
            .then(response => response.json())
            .then(data => {
                initChart(data.monthlyData);
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
                // Fallback data
                initChart([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]);
            });
    }
    
    // Initial load
    loadChartData(new Date().getFullYear());
    
    // Year filter change
    const yearFilter = document.getElementById('chartYearFilter');
    if (yearFilter) {
        yearFilter.addEventListener('change', function() {
            loadChartData(this.value);
        });
    }
});
</script>
