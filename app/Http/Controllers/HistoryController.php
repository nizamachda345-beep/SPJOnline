<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use App\Exports\HistoryExport; // Pastikan sudah dibuat
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Concerns\FromQuery; // <--- PASTIKAN INI ADA
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;


class HistoryController extends Controller
{
    // Fungsi pembantu untuk menyamakan filter di semua aksi
    private function applyFilters(Request $request)
    {
        $query = Submission::whereIn('status', ['disetujui', 'verified']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_spp_spm', 'LIKE', "%{$search}%")
                  ->orWhere('judul_kegiatan', 'LIKE', "%{$search}%")
                  ->orWhereHas('department', function($d) use ($search) {
                      $d->where('nama_dinas', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('updated_at', $request->date);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->applyFilters($request);
        $histories = $query->with(['department', 'user'])->latest('updated_at')->paginate(10);
        return view('history.index', compact('histories'));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new HistoryExport($request), 'Arsip_Dokumen_' . now()->format('d_M_Y') . '.xlsx');
    }

    public function printPDF(Request $request)
    {
        $histories = $this->applyFilters($request)->with('department')->get();
        
        $pdf = Pdf::loadView('exports.history_pdf', compact('histories'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Arsip_Digital.pdf');
    }

    public function show($id)
    {
        $submission = Submission::with(['department', 'user', 'documents'])->findOrFail($id);
        return view('history.show', compact('submission'));
    }

    
}