<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\User;
use App\Models\Department;
use App\Notifications\SystemActivityNotification;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{

    // Method: dashboard
    public function dashboard()
    {
        $statusCounts = Submission::selectRaw("status, count(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $total_berkas = Submission::count();
        $total_user = User::count();
        $total_dinas = Department::count();

        return view('admin.dashboard', [
            'stats' => [
                'pending'          => $statusCounts['pending'] ?? 0,
                'proses validasi' => $statusCounts['proses validasi'] ?? 0,
                'verified'        => $statusCounts['verified'] ?? 0,
                'reset'           => $statusCounts['reset'] ?? 0,
                'revisi'          => $statusCounts['revisi'] ?? 0,
                'rejected'        => $statusCounts['rejected'] ?? 0,
                'disetujui'       => $statusCounts['disetujui'] ?? 0,
                'total_pengajuan_disetujui' => Submission::whereIn('status', ['disetujui', 'verified'])->count(),
                'total_pengajuan_lengkap' => Submission::query()
                    ->where(function ($query) {
                        $query->whereRaw("UPPER(TRIM(COALESCE(jenis_pencairan, ''))) = ?", ['GU NIHIL'])
                            ->orWhereHas('documents', function ($document) {
                                $document->where('jenis_dokumen', 'LIKE', '%SP2D%')
                                    ->whereIn('status_dokumen', ['verified', 'approved']);
                            });
                    })
                    ->count(),
                'direvisi'        => $statusCounts['direvisi'] ?? 0,
                'expired'         => $statusCounts['expired'] ?? 0,
                'total_belum_upload_sp2d' => Submission::query()
                    ->whereIn('status', ['disetujui', 'verified'])
                    ->whereRaw("UPPER(TRIM(COALESCE(jenis_pencairan, ''))) <> ?", ['GU NIHIL'])
                    ->whereDoesntHave('documents', function ($document) {
                        $document->where('jenis_dokumen', 'LIKE', '%SP2D%');
                    })
                    ->count(),
                'total_berkas'    => $total_berkas,

                'user_total'      => $total_user,
                'dinas'           => $total_dinas,
            ]
        ]);
    }

    // Method: index
    public function index(Request $request)
    {
        $departments = Department::all();

        $query = Submission::with(['department', 'user', 'documents']);

        if ($request->filled('dinas')) {
            $query->where('department_id', $request->dinas);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('nomor')) {
            $nomor = $request->nomor;
            $query->where(function($q) use ($nomor) {
                $q->where('nomor_spm', 'like', '%' . $nomor . '%')
                  ->orWhere('nomor_spp', 'like', '%' . $nomor . '%');
            });
        }

        if ($request->filled('kegiatan')) {
            $query->where('judul_kegiatan', 'like', '%' . $request->kegiatan . '%');
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('min_nominal')) {
            $nominal = preg_replace('/[^0-9]/', '', $request->min_nominal);
            if (!empty($nominal)) {
                $query->where('total_pencairan', '>=', $nominal);
            }
        }

        $submissions = $query->latest()->paginate(10)->withQueryString();

        return view('admin.monitoring', compact('submissions', 'departments'));
    }

    // Method: show
    public function show($id)
    {
        $submission = Submission::with(['user', 'department', 'documents'])->findOrFail($id);
        return view('admin.show', compact('submission'));
    }

    // Method: resetStatus
    public function resetStatus($id)
    {

        $admin = auth()->guard()->user();

        return DB::transaction(function () use ($id, $admin) {
            $submission = Submission::with(['department', 'user'])->findOrFail($id);

            $submission->update([
                'status'                => 'reset',
                'verified_by_name'      => null,
                'verified_at'           => null,
                'validated_by_name'     => null,
                'validated_at'          => null,
                'revised_at'            => null,
                'revisi_at'             => null,
                'sent_to_kabid_at'      => null,
                'sent_to_kabid_by_name' => null,
                'rejected_at'           => null,
                'catatan'               => 'Status telah direset oleh Admin pada ' . now()->format('d/m/Y H:i'),
                'reset_at'              => now(),
            ]);

            $submission->documents()->update([
                'status_dokumen' => 'pending',
                'catatan'        => 'Direset oleh Admin'
            ]);

            $additionalData = [
                'submission_id'   => $submission->id,
                'admin_nama'      => $admin->name ?? 'Administrator',
                'nama_dinas'      => $submission->department->nama_dinas ?? '-',
                'nomor_spm'       => $submission->nomor_spm,
                'nomor_spp'       => $submission->nomor_spp,
                'total_pencairan' => $submission->total_pencairan,
                'aksi'            => 'reset_berkas',
            ];

            if ($submission->user) {
                $submission->user->notify(new SystemActivityNotification(
                    'Pengajuan Dikembalikan',
                    'Pengajuan telah dikembalikan ke tahap awal. Silakan lakukan penyesuaian sebelum mengirim kembali.',
                    'fa-undo',
                    'warning',
                    $additionalData
                ));

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Berkas berhasil direset dan dikembalikan ke Operator.'
                ]);
            }
            }
            return redirect()->back()->with('success', 'Berkas berhasil direset dan dikembalikan ke Operator.');
        });
    }
}
