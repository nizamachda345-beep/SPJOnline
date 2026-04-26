<?php

namespace App\Exports;

use App\Models\Submission;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Http\Request;

class HistoryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Submission::query()->whereIn('status', ['disetujui', 'verified']);

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_spp_spm', 'like', "%{$search}%")
                  ->orWhere('judul_kegiatan', 'like', "%{$search}%")
                  ->orWhere('judul_sub_kegiatan', 'like', "%{$search}%") // Tambah filter sub kegiatan
                  ->orWhere('perihal', 'like', "%{$search}%")      // Tambah filter perihal
                  ->orWhereHas('department', function($d) use ($search) {
                      $d->where('nama_dinas', 'like', "%{$search}%");
                  });
            });
        }

        if ($this->request->filled('date')) {
            $query->whereDate('updated_at', $this->request->date);
        }

        // Pastikan relasi department dipanggil untuk efisiensi
        return $query->with('department');
    }

    /**
     * Judul Kolom di Excel (Baris Pertama)
     */
    public function headings(): array
    {
        return [
            'No',
            'SKPD / Dinas',
            'Nomor SPP/SPM',
            'Jenis Pencairan',  // Kolom Baru
            'Judul Kegiatan',
            'Sub Kegiatan',     // Kolom Baru
            'Perihal',          // Kolom Baru
            'Nominal Pencairan',
            'Tanggal Pengesahan',
        ];
    }

    /**
     * Memetakan data dari database ke kolom Excel
     */
    public function map($submission): array
    {
        static $rowNumber = 0;
        return [
            ++$rowNumber,
            $submission->department->nama_dinas ?? '-',
            $submission->nomor_spp_spm,
            $submission->jenis_pencairan ?? '-',             // Data Baru (Sesuaikan nama kolom DB)
            $submission->judul_kegiatan,
            $submission->judul_sub_kegiatan ?? '-',                // Data Baru
            $submission->perihal ?? '-',                     // Data Baru
            number_format($submission->total_pencairan, 0, ',', '.'),
            $submission->updated_at->format('d/m/Y H:i') . ' WITA',
        ];
    }
}