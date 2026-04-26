{{-- Chart Component - Monthly Disbursement Visualization --}}
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white border-bottom py-3">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h6 class="mb-1 fw-bold text-secondary">
                <i class="fas fa-chart-line me-2 text-primary"></i>Visualisasi Pencairan Bulanan
            </h6>
            <p class="text-muted small mb-0">Ringkasan total pencairan berkas per bulan dalam tahun ini.</p>
        </div>
        
        {{-- Container filter --}}
        <div class="d-flex gap-2 w-100 w-md-auto flex-grow-1">
            {{-- Filter SKPD --}}
            @if(auth()->user()->hasAnyRole(['super_admin', 'kasie_bud', 'kabid_perbendaharaan', 'bpk']))
                <select id="chartDeptFilter" class="form-select form-select-sm" style="min-width: 200px;">
                    <option value="">Semua SKPD (Global)</option>
                    @foreach(\App\Models\Department::orderBy('nama_dinas', 'asc')->get() as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->nama_dinas }}</option>
                    @endforeach
                </select>
            @endif

            {{-- Filter Tahun ditambahkan 'ms-auto' agar terdorong ke kanan --}}
            <select id="chartYearFilter" class="form-select form-select-sm ms-auto" style="width: 120px;">
                @php $currentYear = date('Y'); @endphp
                @for($year = $currentYear; $year >= $currentYear - 5; $year--)
                    <option value="{{ $year }}" @selected($year == $currentYear)>{{ $year }}</option>
                @endfor
            </select>
        </div>
    </div>
</div>
    <div class="card-body">
        <div class="chart-container" style="position: relative; height:300px;">
            <canvas id="monthlyDisbursementChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let monthlyChart = null;
    
    // Format mata uang ke Juta (Jt) agar lebih mudah dibaca di chart
    function formatCurrency(value) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(value / 1000000)) + ' Jt';
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
                datasets: [{
                    label: 'Total Pencairan',
                    data: data,
                    borderColor: '#1d4ed8',
                    backgroundColor: 'rgba(29, 78, 216, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.45,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHitRadius: 30,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#1d4ed8',
                    pointBorderWidth: 2,
                }]
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
                        grid: { color: 'rgba(33, 37, 41, 0.05)', drawBorder: false },
                        ticks: {
                            color: '#6c757d',
                            font: { size: 11 },
                            callback: function(value) { return formatCurrency(value); }
                        }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: '#6c757d', font: { size: 11 } }
                    }
                }
            }
        });
    }
    
    function loadChartData() {
        const year = document.getElementById('chartYearFilter').value;
        const deptFilter = document.getElementById('chartDeptFilter');
        const deptId = deptFilter ? deptFilter.value : '';

        // Tampilkan loading state (opsional)
        fetch(`{{ route('dashboard.chart-data') }}?year=${year}&department_id=${deptId}`)
            .then(response => response.json())
            .then(data => {
                initChart(data.monthlyData);
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
                initChart([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]);
            });
    }
    
    // Initial load
    loadChartData();
    
    // Event listener untuk Tahun
    document.getElementById('chartYearFilter').addEventListener('change', loadChartData);
    
    // Event listener untuk SKPD (jika ada/Admin)
    const deptFilterElement = document.getElementById('chartDeptFilter');
    if (deptFilterElement) {
        deptFilterElement.addEventListener('change', loadChartData);
    }
});
</script>