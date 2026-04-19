<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
{
    // withCount('users') digunakan untuk menampilkan statistik operator per dinas
    $departments = Department::withCount('users')->latest()->get();
    
    return view('admin.department.index', compact('departments'));
}

    public function store(Request $request)
    {
        $request->validate(['nama_dinas' => 'required|unique:departments,nama_dinas']);
        Department::create($request->all());
        return redirect()->back()->with('success', 'Dinas berhasil ditambahkan.');
    }

    public function update(Request $request, Department $department)
    {
        $request->validate(['nama_dinas' => 'required|unique:departments,nama_dinas,' . $department->id]);
        $department->update($request->all());
        return redirect()->back()->with('success', 'Dinas berhasil diperbarui.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->back()->with('success', 'Dinas berhasil dihapus.');
    }
}