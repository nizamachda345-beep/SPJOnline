<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BaRekon;
use App\Models\Department;
use App\Models\User;
use App\Services\SystemSettingsService;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SystemActivityNotification;
use Carbon\CarbonImmutable;

class BaRekonController extends Controller
{

    // Method: archive
    public function archive(Request $request)
    {
        $request->validate([
            'bulan' => 'nullable|integer|between:1,12',
            'tahun' => 'nullable|integer|min:2000|max:2100',
            'status' => 'nullable|in:pending,verified,rejected',
            'dinas' => 'nullable|integer|exists:departments,id',
        ]);

        $user = auth()->user();
        $query = BaRekon::with(['department', 'user']);

        if ($user->hasRole('operator')) {
            $query->where('department_id', $user->department_id);
        } elseif ($request->filled('dinas')) {
            $query->where('department_id', $request->dinas);
        }

        $query->when($request->filled('bulan'), fn ($builder) => $builder->where('bulan', $request->bulan))
            ->when($request->filled('tahun'), fn ($builder) => $builder->where('tahun', $request->tahun))
            ->when($request->filled('status'), fn ($builder) => $builder->where('status', $request->status));

        $baRekons = $query
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->orderByDesc('uploaded_at')
            ->paginate(15)
            ->withQueryString();

        $yearsQuery = BaRekon::query();
        if ($user->hasRole('operator')) {
            $yearsQuery->where('department_id', $user->department_id);
        }
        $years = $yearsQuery->select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');
        $departments = $user->hasRole('operator')
            ? collect()
            : Department::whereHas('baRekons')->orderBy('nama_dinas')->get(['id', 'nama_dinas']);

        return view('ba-rekon.archive', compact('baRekons', 'years', 'departments'));
    }

    // Method: archiveFile
    public function archiveFile($id)
    {
        $user = auth()->user();
        $query = BaRekon::query();

        if ($user->hasRole('operator')) {
            $query->where('department_id', $user->department_id);
        }

        $baRekon = $query->findOrFail($id);

        if (!Storage::disk('public')->exists($baRekon->file_path)) {
            abort(404, 'File BA Rekon tidak ditemukan.');
        }

        return response()->file(Storage::disk('public')->path($baRekon->file_path));
    }

   // Method: store
   public function store(Request $request)
    {
        $user = auth()->user()->load('department');
        $departmentId = $user->department_id;
        $settingsService = app(SystemSettingsService::class);
        $waktuSekarang = now($settingsService->timezone());
        $bulan = $waktuSekarang->month;
        $tahun = $waktuSekarang->year;

        $existingBa = BaRekon::where('department_id', $departmentId)
                             ->where('bulan', $bulan)
                             ->where('tahun', $tahun)
                             ->first();

        if ($existingBa?->status === 'verified') {
            return redirect()->back()->with('error', 'Berita Acara Rekonsiliasi bulan ini sudah diverifikasi dan dikunci. Anda tidak dapat mengubahnya lagi.');
        }

        $uploadAvailability = $settingsService->baRekonUploadAvailability(
            CarbonImmutable::instance($waktuSekarang),
            $existingBa !== null,
        );
        if (!$uploadAvailability['allowed']) {
            return redirect()->back()->with('error', $uploadAvailability['message']);
        }

        $request->validate([
            'file_ba' => 'required|mimes:pdf|max:2048',
        ]);

        $additionalData = [
            'department_id'  => $departmentId,
            'nama_operator'  => $user->name,
            'nama_dinas'     => $user->department->nama_dinas ?? '-',
            'bulan'          => $bulan,
            'tahun'          => $tahun,
        ];

        $targetRoles = ['kabid_perbendaharaan', 'kasie_bud', 'super_admin'];
        $recipients = User::whereHas('roles', function($q) use ($targetRoles) {
            $q->whereIn('name', $targetRoles);
        })->get();

        if ($existingBa) {
            Department::whereKey($departmentId)->update(['akses_pengajuan_manual' => null]);

            if ($existingBa->file_path && Storage::disk('public')->exists($existingBa->file_path)) {
                Storage::disk('public')->delete($existingBa->file_path);
            }

            $filePath = $request->file('file_ba')->store('ba-rekons', 'public');

            $existingBa->update([
                'user_id'     => $user->id,
                'file_path'   => $filePath,
                'status'      => 'pending',
                'catatan'     => null,
                'uploaded_at' => now(),
            ]);

            $user->notify(new SystemActivityNotification(
                'Perbaikan BA Rekonsiliasi Terkirim',
                'Dokumen perbaikan berhasil dikirim dan sedang menunggu proses verifikasi.',
                'fa-file-upload',
                'warning',
                $additionalData
            ));

            foreach ($recipients as $recipient) {
                $recipient->notify(new SystemActivityNotification(
                    'Perbaikan BA Rekonsiliasi',
                    ($user->department->nama_dinas ?? 'SKPD') . ' mengirim dokumen perbaikan dan siap untuk diverifikasi.',
                    'fa-file-upload',
                    'warning',
                    $additionalData
                ));
            }

            return redirect()->back()->with('success', 'Berita Acara Rekonsiliasi berhasil diperbarui dan dikirim ulang untuk diverifikasi.');
        }

        Department::whereKey($departmentId)->update(['akses_pengajuan_manual' => null]);

        $filePath = $request->file('file_ba')->store('ba-rekons', 'public');

        BaRekon::create([
            'department_id' => $departmentId,
            'user_id'       => $user->id,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'file_path'     => $filePath,
            'status'        => 'pending',
            'uploaded_at'   => now(),
        ]);

        $user->notify(new SystemActivityNotification(
            'BA Rekonsiliasi Berhasil Dikirim',
            'Dokumen berhasil dikirim dan sedang menunggu proses verifikasi.',
            'fa-file-upload',
            'primary',
            $additionalData
        ));

        foreach ($recipients as $recipient) {
            $recipient->notify(new SystemActivityNotification(
                'BA Rekonsiliasi Baru',
                ($user->department->nama_dinas ?? 'SKPD') . ' mengirim dokumen BA Rekonsiliasi dan menunggu verifikasi.',
                'fa-file-upload',
                'primary',
                $additionalData
            ));
        }

        return redirect()->back()->with('success', 'Berita Acara Rekonsiliasi berhasil diunggah.');
    }

    // Method: verifikasi
    public function verifikasi(Request $request, $departmentId)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2000|max:2100',
        ]);
        $bulan = (int) $request->input('bulan');
        $tahun = (int) $request->input('tahun');

        $baRekon = BaRekon::with(['department', 'user'])
                    ->where('department_id', $departmentId)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->firstOrFail();

        $baRekon->update([
            'status' => 'verified',
            'catatan' => null,
        ]);
        $baRekon->department?->update(['akses_pengajuan_manual' => null]);

        $additionalData = [
            'department_id'  => $departmentId,
            'nama_operator'  => $baRekon->user->name ?? '-',
            'nama_dinas'     => $baRekon->department->nama_dinas ?? '-',
            'bulan'          => $bulan,
            'tahun'          => $tahun,
            'status'         => 'verified',
        ];

        $operators = User::where('department_id', $departmentId)->get();
        foreach ($operators as $operator) {
            $operator->notify(new SystemActivityNotification(
                'BA Rekonsiliasi Disetujui',
                'Dokumen telah disetujui. Pengajuan Permintaan berikutnya kini dapat dilakukan.',
                'fa-check-circle',
                'success',
                $additionalData
            ));
        }

        return redirect()->back()->with('success', 'Berita Acara Rekonsiliasi berhasil disetujui (Verified).');
    }

    // Method: tolakBaRekon
    public function tolakBaRekon(Request $request, $departmentId)
    {
        $request->validate([
            'catatan' => 'required|string|max:500',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2000|max:2100',
        ]);

        $bulan = (int) $request->input('bulan');
        $tahun = (int) $request->input('tahun');

        $baRekon = BaRekon::with(['department', 'user'])
                    ->where('department_id', $departmentId)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->firstOrFail();

        $baRekon->update([
            'status' => 'rejected',
            'catatan' => $request->catatan,
        ]);
        $baRekon->department?->update(['akses_pengajuan_manual' => null]);

        $additionalData = [
            'department_id'  => $departmentId,
            'nama_operator'  => $baRekon->user->name ?? '-',
            'nama_dinas'     => $baRekon->department->nama_dinas ?? '-',
            'bulan'          => $bulan,
            'tahun'          => $tahun,
            'status'         => 'rejected',
            'catatan'        => $request->catatan,
        ];

        $operators = User::where('department_id', $departmentId)->get();
        foreach ($operators as $operator) {
            $operator->notify(new SystemActivityNotification(
                'Perbaikan Diperlukan',
                'Perbaiki Dokumen BA Rekonsiliasi. Catatan: "' . $request->catatan . '". Silakan lakukan perbaikan dan kirim kembali.',
                'fa-times-circle',
                'danger',
                $additionalData
            ));
        }

        return redirect()->back()->with('success', 'Berita Acara Rekonsiliasi ditolak dan catatan telah dikirim ke Operator.');
    }

    // Method: showKabid
    public function showKabid(Request $request, $departmentId)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2000|max:2100',
        ]);
        $bulan = (int) $request->input('bulan');
        $tahun = (int) $request->input('tahun');

        $baRekon = BaRekon::where('department_id', $departmentId)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->first();

        if (!$baRekon || !Storage::disk('public')->exists($baRekon->file_path)) {
            return redirect()->back()->with('error', 'File Berita Acara Rekonsiliasi tidak ditemukan.');
        }

        return response()->file(storage_path('app/public/' . $baRekon->file_path));
    }
}
