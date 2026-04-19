<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
 public function index(Request $request)
{
    $query = Submission::where('status', 'disetujui'); // atau scopeHistory Anda

    // Filter Search (Nomor SPP/SPM atau Nama Kegiatan)
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

    // Filter Tanggal
    if ($request->filled('date')) {
        $query->whereDate('updated_at', $request->date);
    }

    $histories = $query->with(['department', 'user'])->latest('updated_at')->paginate(10);

    return view('history.index', compact('histories'));
}

    public function show($id)
    {
        $submission = Submission::with(['department', 'user', 'documents'])->findOrFail($id);
        return view('history.show', compact('submission'));
    }
}