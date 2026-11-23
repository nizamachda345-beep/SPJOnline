<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErrorReport;
use App\Services\SystemSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ErrorReportController extends Controller
{
    public function index(): View
    {
        $reports = ErrorReport::query()
            ->with('user:id,name')
            ->latest('occurred_at')
            ->paginate(20);
        $displayTimezone = app(SystemSettingsService::class)->timezone();

        return view('admin.error-reports.index', compact('reports', 'displayTimezone'));
    }

    public function show(ErrorReport $errorReport): View
    {
        $errorReport->load(['user:id,name', 'resolver:id,name']);
        $displayTimezone = app(SystemSettingsService::class)->timezone();

        return view('admin.error-reports.show', compact('errorReport', 'displayTimezone'));
    }

    public function resolve(ErrorReport $errorReport): RedirectResponse
    {
        if ($errorReport->status !== 'resolved') {
            $errorReport->forceFill([
                'status' => 'resolved',
                'resolved_by' => auth()->id(),
                'resolved_at' => now(),
            ])->save();
        }

        return back()->with('success', 'Laporan kendala ditandai selesai.');
    }
}
