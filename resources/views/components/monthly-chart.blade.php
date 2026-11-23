{{-- View: components/monthly-chart --}}
<div class="card idj-glass border-0 shadow-sm rounded-4 overflow-hidden h-100 d-flex flex-column mb-0">

    <div class="glass-reflection"></div>

    <div class="card-header bg-dark border-0 py-3 px-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">

            <div class="d-flex align-items-center gap-3 text-nowrap">

                <div class="bg-primary bg-opacity-50 p-2 rounded-3 flex-shrink-0">
                    <i class="fas fa-chart-line text-white"></i>
                </div>

                <div>

                    <h6
                        class="mb-0 text-nowrap"
                        style="letter-spacing: -0.02em;"
                    >
                        Trend Pencairan Dana
                    </h6>

                    <p class="text-white extra-small mb-0 fw-medium text-nowrap">
                        Statistik real-time tahun berjalan
                    </p>

                </div>

            </div>

            <div class="chart-filters">

                @if(auth()->user()->hasAnyRole([
                    'super_admin',
                    'kasie_bud',
                    'kabid_perbendaharaan',
                    'bpk'
                ]))

                    <div class="chart-filter chart-filter-skpd">
                        <select id="chartDeptFilter" class="chart-filter-select">
                            <option value="">Semua SKPD</option>

                            @foreach(
                                \App\Models\Department::orderBy('nama_dinas', 'asc')->get()
                                as $dept
                            )
                                <option value="{{ $dept->id }}">
                                    {{ $dept->nama_dinas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                @endif

                <div class="chart-filter chart-filter-month">
                    <select id="chartMonthFilter" class="chart-filter-select">
                        <option value="">Semua Bulan</option>
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>

                <div class="chart-filter chart-filter-year">
                    <select id="chartYearFilter" class="chart-filter-select">
                        @php $currentYear = date('Y'); @endphp
                        @for($year = $currentYear; $year >= $currentYear - 5; $year--)
                            <option value="{{ $year }}" @selected($year == $currentYear)>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>

                <button
                    onclick="toggleExpand()"
                    class="btn btn-dark border-1 border-light-subtle btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                    id="expandBtn"
                    style="width: 32px; height: 32px;"
                >
                    <i class="fas fa-compress-alt" style="font-size: 0.7rem;"></i>
                </button>

            </div>

        </div>

    </div>

    <div class="card-body px-4 py-3">

        <div
            id="chartWrapper"
            class="chart-container"
            style="position: relative; height:370px; transition: all 0.3s ease;"
        >

            <div
                id="chartLoader"
                class="position-absolute top-50 start-50 translate-middle d-none"
                style="z-index: 10;"
            >

                <div
                    class="spinner-border spinner-border-sm text-primary opacity-25"
                    role="status"
                ></div>

            </div>

            <canvas id="monthlyDisbursementChart"></canvas>

        </div>

        <div
            class="mt-3 pt-2 border-top border-light text-muted text-center"
            style="font-size: 0.8rem;"
        >

            Total
            (
            <span id="selectedDeptLabel">

                @if(auth()->user()->hasAnyRole([
                    'super_admin',
                    'kasie_bud',
                    'kabid_perbendaharaan',
                    'bpk'
                ]))

                    Semua SKPD

                @else

                    SKPD Anda

                @endif

            </span>
            )

            :

            <span
                id="totalDisbursementValue"
                class="fw-bold text-dark"
            >
                Rp 0
            </span>

        </div>

    </div>

</div>

<script>

window.monthlyChart = null;

// Function: Memformat total pencairan ke rupiah
function formatRupiah(amount)
{
    return 'Rp ' +
        new Intl.NumberFormat('id-ID').format(
            amount || 0
        );
}

// Function: Menginisialisasi grafik pencairan
window.initDisbursementChart = function(
    data,
    totalSum,
    labels,
    chartMode = 'monthly'
)
{
    const canvas = document.getElementById('monthlyDisbursementChart');

    if (!canvas) {
        return;
    }

    const ctx = canvas.getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 370);
    gradient.addColorStop(0, 'rgba(78, 115, 223, 0.15)');
    gradient.addColorStop(1, 'rgba(78, 115, 223, 0)');

    if (window.monthlyChart) {
        window.monthlyChart.destroy();
    }

    window.monthlyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels || [],
            datasets: [{
                label: chartMode === 'daily' ? 'Pencairan Harian' : 'Pencairan Bulanan',
                data: data || [],
                borderColor: '#4e73df',
                backgroundColor: gradient,
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: chartMode === 'daily' ? 3 : 2,
                pointHoverRadius: 5,
                pointHitRadius: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: function(tooltipItems) {
                            if (chartMode === 'daily') {
                                return 'Tanggal ' + tooltipItems[0].label;
                            }
                            return tooltipItems[0].label;
                        },
                        label: function(context) {
                            return ' Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y || 0);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: true,
                        drawBorder: false,
                        color: 'rgba(0, 0, 0, 0.06)',
                        lineWidth: 1
                    },
                    ticks: {
                        autoSkip: chartMode === 'daily',
                        maxTicksLimit: chartMode === 'daily' ? 16 : 12,
                        maxRotation: 0,
                        minRotation: 0
                    },
                    title: {
                        display: chartMode === 'daily',
                        text: chartMode === 'daily' ? 'Tanggal' : '',
                        font: { size: 11, weight: '500' }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        drawBorder: false,
                        color: 'rgba(0, 0, 0, 0.06)',
                        lineWidth: 1
                    },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000000) {
                                return 'Rp ' + (value / 1000000000).toFixed(1) + ' M';
                            }
                            return 'Rp ' + (value / 1000000).toFixed(0) + ' Jt';
                        }
                    }
                }
            }
        }
    });

    const totalElement =
        document.getElementById(
            'totalDisbursementValue'
        );

    if (totalElement) {

        totalElement.textContent =
            formatRupiah(totalSum);

    }

};

// Function: Memuat data grafik sesuai filter
window.loadChartData = function()
{

    const loader =
        document.getElementById(
            'chartLoader'
        );

    const yearSelect =
        document.getElementById(
            'chartYearFilter'
        );

    const monthSelect =
        document.getElementById(
            'chartMonthFilter'
        );

    const deptSelect =
        document.getElementById(
            'chartDeptFilter'
        );

    const deptLabel =
        document.getElementById(
            'selectedDeptLabel'
        );

    if (!yearSelect) {

        return;

    }

    if (loader) {

        loader.classList.remove(
            'd-none'
        );

    }

    const year =
        yearSelect.value;

    const month =
        monthSelect
            ? monthSelect.value
            : '';

    const deptId =
        deptSelect
            ? deptSelect.value
            : '';

    if (
        deptSelect &&
        deptLabel
    ) {

        const selectedOption =
            deptSelect.options[
                deptSelect.selectedIndex
            ];

        deptLabel.textContent =
            deptSelect.value
                ? selectedOption.text
                : 'Semua SKPD';

    }

    const url =
        `{{ route('dashboard.chart-data') }}` +
        `?year=${encodeURIComponent(year)}` +
        `&month=${encodeURIComponent(month)}` +
        `&department_id=${encodeURIComponent(deptId)}`;

    fetch(url)

        .then(response => {

            if (!response.ok) {

                throw new Error(
                    'Gagal mengambil data chart.'
                );

            }

            return response.json();

        })

        .then(data => {

            if (loader) {

                loader.classList.add(
                    'd-none'
                );

            }

            if (deptLabel) {

                deptLabel.textContent =
                    data.departmentName ||
                    'Semua SKPD';

            }

            window.initDisbursementChart(

                data.monthlyData || [],

                data.totalSum || 0,

                data.chartLabels || [],

                data.chartMode || 'monthly'

            );

        })

        .catch(error => {

            console.error(
                'Chart Error:',
                error
            );

            if (loader) {

                loader.classList.add(
                    'd-none'
                );

            }

            window.initDisbursementChart(

                [0],

                0,

                ['Tidak ada data'],

                'monthly'

            );

        });

};

window.loadChartData();

document.addEventListener(
    'change',
    function(e)
    {

        if (

            e.target.id ===
                'chartYearFilter'

            ||

            e.target.id ===
                'chartMonthFilter'

            ||

            e.target.id ===
                'chartDeptFilter'

        ) {

            window.loadChartData();

        }

    }
);

</script>


<style>

    .fw-black {
        font-weight: 800;
    }

    .text-dark-blue {
        color: #1e293b;
    }

    .extra-small {
        font-size: 0.65rem;
    }

    .text-nowrap {
        white-space: nowrap !important;
    }

    .transition-all {
        transition:
            all 0.4s cubic-bezier(
                0.4,
                0,
                0.2,
                1
            );
    }

    /* ==========================================================
       CHART FILTER - COMPACT PROFESSIONAL
       ========================================================== */

    .chart-filters {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 7px;
        margin-left: auto;
        flex-shrink: 0;
    }

    .chart-filter {
        position: relative;
        flex-shrink: 0;
    }

    .chart-filter-skpd { width: 145px; }
    .chart-filter-month { width: 112px; }
    .chart-filter-year { width: 78px; }

    .chart-filter-select {
        width: 100%;
        height: 32px;
        padding: 0 27px 0 10px;
        border: 1px solid rgba(255, 255, 255, 0.20);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        font-size: 0.70rem;
        font-weight: 500;
        outline: none;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        transition: background-color .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .chart-filter-select:hover {
        background: rgba(255, 255, 255, 0.13);
        border-color: rgba(255, 255, 255, 0.32);
    }

    .chart-filter-select:focus {
        background: rgba(255, 255, 255, 0.14);
        border-color: rgba(255, 255, 255, 0.40);
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.06);
    }

    .chart-filter::after {
        content: "";
        position: absolute;
        top: 50%;
        right: 10px;
        width: 6px;
        height: 6px;
        border-right: 1.5px solid rgba(255,255,255,.75);
        border-bottom: 1.5px solid rgba(255,255,255,.75);
        transform: translateY(-65%) rotate(45deg);
        pointer-events: none;
        transition: transform .2s ease, border-color .2s ease;
    }

    .chart-filter:focus-within::after {
        transform: translateY(-35%) rotate(225deg);
        border-color: #ffffff;
    }

    .chart-filter-select option {
        background: #1f2937;
        color: #ffffff;
        font-size: .75rem;
    }

    .chart-filter-year .chart-filter-select {
        font-weight: 600;
    }

    @media (max-width: 900px) {
        .chart-filters { width: 100%; justify-content: flex-end; }
        .chart-filter-skpd { width: 140px; }
        .chart-filter-month { width: 108px; }
        .chart-filter-year { width: 75px; }
    }

    @media (max-width: 576px) {
        .chart-filters { width: 100%; gap: 5px; }
        .chart-filter-skpd { width: 125px; }
        .chart-filter-month { width: 100px; }
        .chart-filter-year { width: 68px; }
        .chart-filter-select { height: 30px; padding-left: 8px; padding-right: 23px; font-size: .65rem; }
    }
</style>

