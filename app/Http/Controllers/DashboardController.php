<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
{
    $user = auth()->user();
    
    // REDIRECT BERDASARKAN ROLE (Agar URL di Browser Rapi)
    if ($user->hasRole('super_admin')) {
        return redirect()->route('admin.dashboard');
    } 
    
    if ($user->hasRole('bpk')) {
        return redirect()->route('bpk.dashboard');
    }

    // LOGIC UNTUK OPERATOR, KASIE, & KABID (TETAP DI DASHBOARD UMUM)
    $query = Submission::query();

    if ($user->hasRole('operator')) {
        $query->where('department_id', $user->department_id);
    } 

    $stats = [
        'total_berkas'    => (clone $query)->count(),
        'pending'         => (clone $query)->where('status', 'pending')->count(),
        'revisi'          => (clone $query)->where('status', 'revisi')->count(),
        'proses_validasi' => (clone $query)->where('status', 'proses validasi')->count(),
        'disetujui'       => (clone $query)->where('status', 'disetujui')->count(),
    ];

    $recentSubmissions = $query->with('department')->latest()->take(10)->get();

    return view('dashboard', compact('stats', 'recentSubmissions'));
}
}