<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Services\SystemSettingsService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{

    // Method: index
    public function index(Request $request)
    {
        $request->validate([
            'bulan' => 'nullable|integer|between:1,12',
            'tahun' => 'nullable|integer|min:2000|max:2100',
        ]);
        $search = $request->input('search');
        $waktuSekarang = CarbonImmutable::now(app(SystemSettingsService::class)->timezone());
        $bulanDipilih = (int) $request->input('bulan', $waktuSekarang->month);
        $tahunDipilih = (int) $request->input('tahun', $waktuSekarang->year);
        $tahunOptions = \App\Models\BaRekon::query()
            ->select('tahun')
            ->distinct()
            ->pluck('tahun')
            ->map(fn ($year) => (int) $year)
            ->prepend($waktuSekarang->year)
            ->unique()
            ->sortDesc()
            ->values();

        $departments = Department::when($search, function ($query, $search) {
                return $query->where('nama_dinas', 'LIKE', "%{$search}%")
                             ->orWhere('kode_dinas', 'LIKE', "%{$search}%");
            })
            ->withCount('users')
            ->with([
                'baRekons' => function ($query) use ($tahunDipilih, $waktuSekarang) {
                    $query->whereIn('tahun', array_unique([$tahunDipilih, $waktuSekarang->year]));
                }
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.department.index', compact('departments', 'bulanDipilih', 'tahunDipilih', 'tahunOptions', 'waktuSekarang'));
    }

    public function updateSubmissionAccess(Request $request, Department $department)
    {
        $validated = $request->validate([
            'akses' => 'required|in:open,closed',
        ]);

        $department->update([
            'akses_pengajuan_manual' => $validated['akses'] === 'open',
            'akses_pengajuan_dibuka_oleh' => $validated['akses'] === 'open'
                ? $request->user()->name
                : null,
        ]);

        return redirect()->back()->with(
            'success',
            $validated['akses'] === 'open'
                ? 'Akses pengajuan transaksi berhasil dibuka.'
                : 'Akses pengajuan transaksi berhasil ditutup.'
        );
    }

    // Method: store
    public function store(Request $request)
    {
        $request->validate(['nama_dinas' => 'required|unique:departments,nama_dinas']);
        Department::create($request->all());
        return redirect()->back()->with('success', 'Dinas berhasil ditambahkan.');
    }

    // Method: update
    public function update(Request $request, Department $department)
    {
        $request->validate(['nama_dinas' => 'required|unique:departments,nama_dinas,' . $department->id]);
        $department->update($request->all());
        return redirect()->back()->with('success', 'Dinas berhasil diperbarui.');
    }

    // Method: destroy
    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('departments.index')->with('success', 'Dinas berhasil dihapus.');
    }
}
