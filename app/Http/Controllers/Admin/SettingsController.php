<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\SystemSettingLog;
use App\Services\SystemSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class SettingsController extends Controller
{
    public function index(SystemSettingsService $settings): View
    {
        $recentLogs = collect();

        try {
            $recentLogs = SystemSettingLog::with('user')->latest('changed_at')->limit(10)->get();
        } catch (Throwable $exception) {
            $context = ['exception_class' => $exception::class];
            if ($exception instanceof QueryException) {
                $context['sql_state'] = $exception->errorInfo[0] ?? $exception->getCode();
                $context['driver_code'] = $exception->errorInfo[1] ?? null;
            }

            Log::error('Gagal membaca system_setting_logs; halaman settings ditampilkan tanpa ringkasan log.', $context);
        }

        return view('admin.settings.index', [
            'settings' => $settings->all(),
            'timezoneOptions' => $settings->timezoneOptions(),
            'recentLogs' => $recentLogs,
        ]);
    }

    public function logs(): View
    {
        $logReadFailed = false;
        $logs = new LengthAwarePaginator([], 0, 30);

        try {
            $logs = SystemSettingLog::with('user')->latest('changed_at')->paginate(30);
        } catch (Throwable $exception) {
            $logReadFailed = true;
            $context = ['exception_class' => $exception::class];
            if ($exception instanceof QueryException) {
                $context['sql_state'] = $exception->errorInfo[0] ?? $exception->getCode();
                $context['driver_code'] = $exception->errorInfo[1] ?? null;
            }

            Log::error('Gagal membaca system_setting_logs; halaman riwayat ditampilkan tanpa data.', $context);
        }

        return view('admin.settings.logs', [
            'logs' => $logs,
            'logReadFailed' => $logReadFailed,
        ]);
    }

    public function update(Request $request, SystemSettingsService $settings): RedirectResponse
    {
        $validatedLimits = $request->validate([
            'security.logo_max_size_mb' => ['required', 'integer', 'between:1,5'],
            'submission.document_max_size_mb' => ['required', 'integer', 'between:1,20'],
            'submission.sp2d_max_size_mb' => ['required', 'integer', 'between:1,20'],
            'system.timezone' => ['required', 'in:Asia/Jakarta,Asia/Makassar,Asia/Jayapura'],
            'system.maintenance_mode' => ['required', 'boolean'],
        ]);
        $logoMaxSizeMb = (int) $validatedLimits['security']['logo_max_size_mb'];

        $validated = $request->validate([
            'branding.product_name' => ['required', 'string', 'max:80'],
            'branding.government_name' => ['required', 'string', 'max:160'],
            'branding.government_short_name' => ['required', 'string', 'max:80'],
            'branding.agency_name' => ['required', 'string', 'max:160'],
            'branding.office_address' => ['nullable', 'string', 'max:500'],
            'branding.contact' => ['nullable', 'string', 'max:160'],
            'branding.application_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:' . ($logoMaxSizeMb * 1024)],
            'branding.document_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:' . ($logoMaxSizeMb * 1024)],
            'submission.schedule_mode' => ['required', 'in:scheduled,open,closed'],
            'submission.lock_start_time' => ['required', 'date_format:H:i'],
            'submission.lock_end_time' => ['required', 'date_format:H:i'],
            'submission.schedule_message' => ['required', 'string', 'max:500'],
            'submission.closed_message' => ['required', 'string', 'max:500'],
            'ba_rekon.deadline_day' => ['required', 'integer', 'between:1,28'],
            'ba_rekon.upload_start_day' => ['required', 'integer', 'between:1,31'],
            'ba_rekon.upload_end_day' => ['required', 'integer', 'between:1,31'],
            'ba_rekon.upload_enabled' => ['required', 'boolean'],
            'ba_rekon.access_message' => ['required', 'string', 'max:500'],
            'ba_rekon.closed_message' => ['required', 'string', 'max:500'],
        ]);

        if ($validated['submission']['lock_start_time'] >= $validated['submission']['lock_end_time']) {
            throw ValidationException::withMessages([
                'submission.lock_end_time' => 'Waktu mulai penguncian harus lebih awal daripada waktu selesai.',
            ]);
        }

        if ($validated['ba_rekon']['upload_start_day'] > $validated['ba_rekon']['upload_end_day']) {
            throw ValidationException::withMessages([
                'ba_rekon.upload_end_day' => 'Tanggal akhir periode unggah harus sama dengan atau setelah tanggal mulainya.',
            ]);
        }

        $currentValues = $settings->all();
        if ($settings->hasReadFailure()) {
            throw ValidationException::withMessages([
                'settings' => 'Pengaturan tidak disimpan karena konfigurasi saat ini gagal dibaca. Silakan coba lagi atau hubungi administrator.',
            ]);
        }

        $newFiles = [];
        $oldFiles = [];

        foreach (['branding.application_logo', 'branding.document_logo'] as $fileKey) {
            $inputName = $fileKey;
            if ($request->hasFile($inputName)) {
                $newPath = $request->file($inputName)->storePublicly('branding', 'public');
                if (!$newPath) {
                    foreach ($newFiles as $stagedFile) {
                        Storage::disk('public')->delete($stagedFile);
                    }
                    throw ValidationException::withMessages([
                        $inputName => 'File logo gagal disimpan. Silakan coba lagi.',
                    ]);
                }
                $newFiles[$fileKey] = $newPath;
                $oldFiles[] = $currentValues[$fileKey] ?? null;
            }
        }

        $values = [
            'branding.product_name' => $validated['branding']['product_name'],
            'branding.government_name' => $validated['branding']['government_name'],
            'branding.government_short_name' => $validated['branding']['government_short_name'],
            'branding.agency_name' => $validated['branding']['agency_name'],
            'branding.office_address' => $validated['branding']['office_address'] ?? '',
            'branding.contact' => $validated['branding']['contact'] ?? '',
            'security.logo_max_size_mb' => (string) $validatedLimits['security']['logo_max_size_mb'],
            'submission.document_max_size_mb' => (string) $validatedLimits['submission']['document_max_size_mb'],
            'submission.sp2d_max_size_mb' => (string) $validatedLimits['submission']['sp2d_max_size_mb'],
            'system.timezone' => $validatedLimits['system']['timezone'],
            'system.maintenance_mode' => $validatedLimits['system']['maintenance_mode'] ? '1' : '0',
            'submission.schedule_mode' => $validated['submission']['schedule_mode'],
            'submission.lock_start_time' => $validated['submission']['lock_start_time'],
            'submission.lock_end_time' => $validated['submission']['lock_end_time'],
            'submission.schedule_message' => $validated['submission']['schedule_message'],
            'submission.closed_message' => $validated['submission']['closed_message'],
            'ba_rekon.deadline_day' => (string) $validated['ba_rekon']['deadline_day'],
            'ba_rekon.upload_start_day' => (string) $validated['ba_rekon']['upload_start_day'],
            'ba_rekon.upload_end_day' => (string) $validated['ba_rekon']['upload_end_day'],
            'ba_rekon.upload_enabled' => $validated['ba_rekon']['upload_enabled'] ? '1' : '0',
            'ba_rekon.access_message' => $validated['ba_rekon']['access_message'],
            'ba_rekon.closed_message' => $validated['ba_rekon']['closed_message'],
        ];
        $values = array_merge($values, $newFiles);

        try {
            DB::transaction(function () use ($values, $currentValues, $request): void {
                foreach ($values as $key => $value) {
                    $value = (string) $value;
                    $oldValue = (string) ($currentValues[$key] ?? '');
                    if ($value === $oldValue) {
                        continue;
                    }

                    [$group] = explode('.', $key, 2);
                    $type = match ($key) {
                        'submission.lock_start_time', 'submission.lock_end_time' => 'time',
                        'security.logo_max_size_mb' => 'integer',
                        'submission.document_max_size_mb', 'submission.sp2d_max_size_mb' => 'integer',
                        'system.timezone' => 'text',
                        'ba_rekon.deadline_day', 'ba_rekon.upload_start_day', 'ba_rekon.upload_end_day' => 'integer',
                        'ba_rekon.upload_enabled' => 'boolean',
                        'system.maintenance_mode' => 'boolean',
                        default => 'text',
                    };

                    SystemSetting::updateOrCreate(
                        ['setting_key' => $key],
                        [
                            'group_name' => $group,
                            'value' => $value,
                            'value_type' => $type,
                            'updated_by' => $request->user()->id,
                        ]
                    );

                    SystemSettingLog::create([
                        'user_id' => $request->user()->id,
                        'setting_key' => $key,
                        'old_value' => $this->auditValue($key, $oldValue),
                        'new_value' => $this->auditValue($key, $value),
                        'changed_at' => now(),
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            foreach ($newFiles as $newFile) {
                Storage::disk('public')->delete($newFile);
            }
            throw $exception;
        }

        foreach ($oldFiles as $oldFile) {
            $stillReferenced = $oldFile && SystemSetting::where('value', $oldFile)->exists();
            if ($oldFile && !$stillReferenced && !in_array($oldFile, $newFiles, true)) {
                Storage::disk('public')->delete($oldFile);
            }
        }

        $settings->forgetCache();

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }

    private function auditValue(string $key, ?string $value): ?string
    {
        if (preg_match('/password|passwd|secret|token|credential|api[_-]?key|private[_-]?key/i', $key)) {
            return $value === null ? null : '[REDACTED]';
        }

        return $value;
    }
}
