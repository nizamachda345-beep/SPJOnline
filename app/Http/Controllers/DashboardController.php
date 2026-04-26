<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Department;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // 1. REDIRECT KHUSUS SUPER ADMIN
        if ($user->hasRole('super_admin')) {
            return redirect()->route('admin.dashboard');
        } 
        
        // BPK DILEPAS DARI REDIRECT AGAR BISA MELIHAT DASHBOARD UTAMA
        // (Jika sebelumnya ada redirect ke bpk.dashboard, sekarang dihapus)

        // 2. MULAI QUERY
        $query = Submission::query();

        /**
         * 3. LOGIKA FILTER AKSES DATA (SANGAT PENTING)
         * Hanya Role 'operator' yang datanya dikunci ke department_id miliknya.
         * Role 'kabid', 'kasie_bud', dan 'bpk' melihat SEMUA DATA SKPD.
         */
        if ($user->hasRole('operator')) {
            $query->where('department_id', $user->department_id);
        }

        // 4. SINKRONISASI STATISTIK DENGAN DATABASE
        $stats = [
    'total_berkas'    => (clone $query)->count(),
    'pending'         => (clone $query)->where('status', 'pending')->count(),
    'reset'           => (clone $query)->where('status', 'reset')->count(), // Tambahkan ini
    'revisi'          => (clone $query)->where('status', 'revisi')->count(),
    'rejected'       => (clone $query)->where('status', 'ditolak')->count(), // Tambahkan ini   
    'validasi' => (clone $query)->where('status', 'proses validasi')->count(),
    'verifikasi'  => (clone $query)->where('status', 'proses verifikasi')->count(),
    'disetujui'       => (clone $query)->whereIn('status', ['disetujui', 'verified'])->count(),
];

        // Ambil data terbaru untuk tabel di bawah dashboard
        $recentSubmissions = $query->with('department')->latest()->take(10)->get();

        return view('dashboard', compact('stats', 'recentSubmissions'));
    }

    public function chartData(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $deptId = $request->get('department_id'); 
        $user = auth()->user(); 
        
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $query = Submission::whereIn('status', ['disetujui', 'verified'])
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month);

            // Logika Filter Chart juga disesuaikan
            if ($user->hasRole('operator')) {
                $query->where('department_id', $user->department_id);
            } else {
                // Untuk Kabid/Kasie/BPK/Admin bisa melihat per SKPD atau Global
                if (!empty($deptId)) {
                    $query->where('department_id', $deptId);
                }
            }

            $total = $query->sum('total_pencairan');
            $monthlyData[] = (float) $total;
        }
        
        return response()->json([
            'monthlyData' => $monthlyData,
            'year' => $year
        ]);
    }
}