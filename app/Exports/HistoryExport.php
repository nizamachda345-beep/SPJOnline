<?php

namespace App\Exports;

use App\Models\Submission;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Http\Request;

class HistoryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
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
                $q->where('nomor_spp', 'like', "%{$search}%")
                    ->orWhere('nomor_spm', 'like', "%{$search}%")
                    ->orWhere('judul_kegiatan', 'like', "%{$search}%")
                    ->orWhere('judul_sub_kegiatan', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%")
                    ->orWhereHas('department', function($d) use ($search) {
                        $d->where('nama_dinas', 'like', "%{$search}%");
                    });
            });
        }

        if ($this->request->filled('start_date') && $this->request->filled('end_date')) {
            $query->whereBetween('updated_at', [$this->request->start_date . ' 00:00:00', $this->request->end_date . ' 23:59:59']);
        }
        elseif ($this->request->filled('date')) {
            $query->whereDate('updated_at', $this->request->date);
        }

        if ($this->request->filled('jenis_pencairan')) {
            $query->where('jenis_pencairan', $this->request->jenis_pencairan);
        }

        if ($this->request->filled('kegiatan')) {
            $kegiatan = $this->request->kegiatan;
            $query->where(function($q) use ($kegiatan) {
                $q->where('judul_kegiatan', 'like', "%{$kegiatan}%")
                  ->orWhere('judul_sub_kegiatan', 'like', "%{$kegiatan}%");
            });
        }

        return $query->with('department')->orderBy('updated_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'SKPD / Dinas',
            'Nomor SPP',
            'Tanggal SPP',
            'Nomor SPM',
            'Tanggal SPM',
            'Judul Kegiatan',
            'Sub Kegiatan',
            'Perihal',
            'Jenis Permintaan',
            'Sumber Dana',
            'Nominal Permintaan',
            'Tanggal Pengajuan', // <-- Tambahan Kolom Tanggal Pengajuan
            'Tanggal Pengesahan',
        ];
    }

    public function map($submission): array
    {
        static $rowNumber = 0;
        return [
            ++$rowNumber,
            $submission->department->nama_dinas ?? '-',
            $submission->nomor_spp ?? '-',
            $submission->tanggal_spp ? \Carbon\Carbon::parse($submission->tanggal_spp)->format('d/m/Y') : '-',
            $submission->nomor_spm ?? '-',
            $submission->tanggal_spm ? \Carbon\Carbon::parse($submission->tanggal_spm)->format('d/m/Y') : '-',
            $submission->judul_kegiatan,
            $submission->judul_sub_kegiatan ?? '-',
            $submission->perihal ?? '-',
            strtoupper($submission->jenis_pencairan ?? '-'),
            strtoupper($submission->sumber_dana ?? '-'),
            (float) $submission->total_pencairan,
            $submission->created_at ? $submission->created_at->format('d/m/Y H:i') . ' WITA' : '-',
            $submission->updated_at->format('d/m/Y H:i') . ' WITA',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Kolom L untuk Nominal Pencairan (bergeser karena penambahan kolom baru)
        ];
    }
}
