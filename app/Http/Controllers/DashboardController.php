<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Department;
use App\Models\BaRekon;
use App\Services\SystemSettingsService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    // Method: canViewAllDepartments
    private function canViewAllDepartments($user)
    {
        return $user->hasAnyRole([
            'super_admin',
            'kasie_bud',
            'kabid_perbendaharaan',
            'bpk',
        ]);
    }

    // Method: index
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return redirect()->route('admin.dashboard');
        }

        $query = Submission::query();

        if (!$this->canViewAllDepartments($user)) {
            $query->where('department_id', $user->department_id);
        }

        $sp2dBelumUploadQuery = (clone $query)
            ->whereIn('status', ['disetujui', 'verified'])
            ->whereRaw("UPPER(TRIM(COALESCE(jenis_pencairan, ''))) <> ?", ['GU NIHIL'])
            ->whereDoesntHave('documents', function ($document) {
                $document->where('jenis_dokumen', 'LIKE', '%SP2D%');
            });

        $stats = [
            'total_berkas' => (clone $query)->count(),
            'pending'      => (clone $query)->where('status', 'pending')->count(),
            'reset'        => (clone $query)->where('status', 'reset')->count(),
            'revisi'       => (clone $query)->where('status', 'revisi')->count(),
            'direvisi'     => (clone $query)->where('status', 'direvisi')->count(),
            'expired'      => (clone $query)->where('status', 'expired')->count(),
            'rejected'     => (clone $query)->where('status', 'ditolak')->count(),
            'validasi'     => (clone $query)->where('status', 'proses validasi')->count(),
            'verifikasi'   => (clone $query)->where('status', 'proses verifikasi')->count(),
            'disetujui'    => (clone $query)->whereIn('status', ['disetujui', 'verified'])->count(),
            'total_anggaran_disetujui' => (clone $query)
                ->whereIn('status', ['disetujui', 'verified'])
                ->sum('total_pencairan'),
            'sp2d_belum_upload' => (clone $sp2dBelumUploadQuery)->count(),
        ];

        $sp2dBelumUpload = (clone $sp2dBelumUploadQuery)
            ->with('department')
            ->latest('updated_at')
            ->paginate(10, ['*'], 'sp2d_page')
            ->withQueryString();

        $recentActivities = (clone $query)
            ->with(['department', 'user'])
            ->orderBy('updated_at', 'desc')
            ->limit(6)
            ->get();

        $recentSubmissions = (clone $query)
            ->with('department')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'stats',
            'sp2dBelumUpload',
            'recentSubmissions',
            'recentActivities'
        ));
    }

    // Method: chartData
    public function chartData(Request $request)
    {
        $year   = (int) $request->get('year', date('Y'));
        $month  = $request->get('month');
        $deptId = $request->get('department_id');
        $user   = auth()->user();

        if ($year < 2000 || $year > 2100) {
            $year = (int) date('Y');
        }

        $canViewAllDepartments = $this->canViewAllDepartments($user);

        $baseQuery = Submission::query()
            ->whereIn('status', ['disetujui', 'verified']);

        if ($canViewAllDepartments) {
            if (!empty($deptId)) {
                $baseQuery->where('department_id', $deptId);
            }
        } else {
            $baseQuery->where('department_id', $user->department_id);
        }

        $departmentName = 'Semua SKPD';

        if (!empty($deptId) && $canViewAllDepartments) {
            $department = Department::find($deptId);

            if ($department) {
                $departmentName = $department->nama_dinas;
            }
        } elseif (!$canViewAllDepartments) {
            $department = Department::find($user->department_id);

            if ($department) {
                $departmentName = $department->nama_dinas;
            }
        }

        if (empty($month)) {
            $monthlyData = [];
            $chartLabels = [];

            $monthNames = [
                1  => 'Jan',
                2  => 'Feb',
                3  => 'Mar',
                4  => 'Apr',
                5  => 'Mei',
                6  => 'Jun',
                7  => 'Jul',
                8  => 'Ags',
                9  => 'Sep',
                10 => 'Okt',
                11 => 'Nov',
                12 => 'Des',
            ];

            for ($m = 1; $m <= 12; $m++) {
                $query = clone $baseQuery;

                $total = $query
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $m)
                    ->sum('total_pencairan');

                $monthlyData[] = (float) $total;
                $chartLabels[] = $monthNames[$m];
            }

            return response()->json([
                'monthlyData'    => $monthlyData,
                'chartLabels'    => $chartLabels,
                'totalSum'      => array_sum($monthlyData),
                'year'          => $year,
                'month'         => null,
                'monthName'     => 'Semua Bulan',
                'departmentId'  => $deptId,
                'departmentName'=> $departmentName,
                'chartMode'     => 'monthly',
            ]);
        }

        $month = (int) $month;

        if ($month < 1 || $month > 12) {
            return response()->json([
                'message' => 'Bulan tidak valid.'
            ], 422);
        }

        $daysInMonth = \Carbon\Carbon::create(
            $year,
            $month,
            1
        )->daysInMonth;

        $dailyData   = [];
        $chartLabels = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $query = clone $baseQuery;

            $total = $query
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->whereDay('created_at', $day)
                ->sum('total_pencairan');

            $dailyData[] = (float) $total;
            $chartLabels[] = (string) $day;
        }

        $monthNames = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return response()->json([
            'monthlyData'    => $dailyData,
            'chartLabels'    => $chartLabels,
            'totalSum'       => array_sum($dailyData),
            'year'           => $year,
            'month'          => $month,
            'monthName'      => $monthNames[$month],
            'departmentId'   => $deptId,
            'departmentName' => $departmentName,
            'chartMode'      => 'daily',
            'daysInMonth'    => $daysInMonth,
        ]);
    }

    // Method: fetchLatest
    public function fetchLatest()
    {
        $user = auth()->user();
        $unreadDbCount = $user->unreadNotifications->count();
        $settings = app(SystemSettingsService::class);
        $waktuSekarang = CarbonImmutable::now($settings->timezone());
        $department = $user->department;
        $baRekonStatus = $user->hasRole('operator') && $user->department_id
            ? BaRekon::query()
                ->where('department_id', $user->department_id)
                ->where('bulan', $waktuSekarang->month)
                ->where('tahun', $waktuSekarang->year)
                ->value('status')
            : null;
        $baRekonValid = $baRekonStatus === 'verified';
        $baRekonPending = $baRekonStatus === 'pending';
        $baRekonDitolak = $baRekonStatus === 'rejected';
        $submissionAvailability = $user->hasRole('operator')
            ? ($department
                ? $settings->submissionAvailability($department, $waktuSekarang, $baRekonStatus, true)
                : ['allowed' => false, 'reason' => 'department_missing', 'message' => 'Akun Anda belum terhubung ke SKPD manapun. Hubungi administrator.'])
            : ['allowed' => true, 'reason' => null, 'message' => null];

        $htmlList = view(
            'layouts.partials.notification-list',
            compact('user', 'waktuSekarang', 'baRekonValid', 'baRekonPending', 'baRekonDitolak', 'submissionAvailability')
        )->render();

        return response()->json([
            'unreadCount' => $unreadDbCount,
            'htmlList'    => $htmlList,
        ]);
    }
}
