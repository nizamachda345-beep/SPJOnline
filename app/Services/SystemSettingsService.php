<?php

namespace App\Services;

use App\Models\BaRekon;
use App\Models\Department;
use App\Models\SystemSetting;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use JsonException;
use Throwable;

class SystemSettingsService
{
    public const TIMEZONE_OPTIONS = [
        'Asia/Jakarta' => 'WIB (UTC+7)',
        'Asia/Makassar' => 'WITA (UTC+8)',
        'Asia/Jayapura' => 'WIT (UTC+9)',
    ];

    public const DEFAULTS = [
        'branding.product_name' => 'SPJOnline',
        'branding.government_name' => 'PEMERINTAH KABUPATEN BOLAANG MONGONDOW',
        'branding.government_short_name' => 'BOLMONG',
        'branding.agency_name' => 'BADAN KEUANGAN DAERAH',
        'branding.office_address' => 'Jl. Trans Sulawesi Desa Lalow Kecamatan Lolak, Kab. Bolaang Mongondow Provinsi Sulawesi Utara',
        'branding.contact' => 'bkd@bolmongkab.go.id',
        'branding.application_logo' => '',
        'branding.document_logo' => '',
        'system.timezone' => 'Asia/Makassar',
        'system.maintenance_mode' => '0',
        'security.logo_max_size_mb' => '2',
        'submission.schedule_mode' => 'scheduled',
        'submission.document_max_size_mb' => '10',
        'submission.sp2d_max_size_mb' => '2',
        'submission.lock_start_time' => '14:00',
        'submission.lock_end_time' => '18:00',
        'submission.schedule_message' => 'Pengajuan Transaksi sementara ditutup pada pukul {start}-{end} {timezone}. Silakan mengakses kembali setelah pukul {end} {timezone}.',
        'submission.closed_message' => 'Pengajuan transaksi sedang ditutup oleh administrator.',
        'ba_rekon.deadline_day' => '10',
        'ba_rekon.upload_start_day' => '1',
        'ba_rekon.upload_end_day' => '31',
        'ba_rekon.upload_enabled' => '1',
        'ba_rekon.access_message' => 'BA Rekon bulan berjalan belum terverifikasi. Akses pengajuan akan terbuka setelah BA Rekon diverifikasi.',
        'ba_rekon.closed_message' => 'Pengunggahan BA Rekon sedang ditutup oleh administrator.',
    ];

    private const DEFAULT_TYPES = [
        'submission.lock_start_time' => 'time',
        'submission.lock_end_time' => 'time',
        'submission.document_max_size_mb' => 'integer',
        'submission.sp2d_max_size_mb' => 'integer',
        'security.logo_max_size_mb' => 'integer',
        'ba_rekon.deadline_day' => 'integer',
        'ba_rekon.upload_start_day' => 'integer',
        'ba_rekon.upload_end_day' => 'integer',
        'ba_rekon.upload_enabled' => 'boolean',
        'system.maintenance_mode' => 'boolean',
    ];

    private ?array $settings = null;
    private ?array $types = null;
    private bool $settingsReadFailed = false;

    public function all(): array
    {
        if ($this->settings === null) {
            $this->settings = self::DEFAULTS;
            $this->types = self::DEFAULT_TYPES;
            $this->settingsReadFailed = false;

            try {
                $hasStoredTypes = true;
                try {
                    $stored = SystemSetting::query()
                        ->whereIn('setting_key', array_keys(self::DEFAULTS))
                        ->get(['setting_key', 'value', 'value_type']);
                } catch (QueryException $exception) {
                    if (!$this->isMissingValueTypeColumn($exception)) {
                        throw $exception;
                    }

                    // Older schemas can still provide settings; infer known
                    // types from keys while logging the missing metadata.
                    $stored = SystemSetting::query()
                        ->whereIn('setting_key', array_keys(self::DEFAULTS))
                        ->get(['setting_key', 'value']);
                    $hasStoredTypes = false;

                    Log::warning(
                        'Kolom system_settings.value_type belum tersedia; tipe setting diinferensikan dari key.',
                        $this->databaseFailureContext($exception)
                    );
                }

                foreach ($stored as $setting) {
                    $key = (string) $setting->setting_key;
                    if (!array_key_exists($key, self::DEFAULTS)) {
                        continue;
                    }

                    // Keep the existing all()/get() API string-compatible.
                    $this->settings[$key] = $setting->value === null
                        ? self::DEFAULTS[$key]
                        : (string) $setting->value;
                    $this->types[$key] = $hasStoredTypes
                        ? $this->resolveType($key, (string) $setting->value_type)
                        : (self::DEFAULT_TYPES[$key] ?? 'string');
                }
            } catch (Throwable $exception) {
                // Defaults keep unrelated pages usable; the structured log
                // retains the database error code without logging values.
                $this->settings = self::DEFAULTS;
                $this->types = self::DEFAULT_TYPES;
                $this->settingsReadFailed = true;
                Log::error(
                    'Gagal membaca system_settings; nilai default digunakan untuk request ini.',
                    $this->databaseFailureContext($exception)
                );
            }
        }

        return $this->settings;
    }

    public function get(string $key): string
    {
        return (string) ($this->all()[$key] ?? '');
    }

    public function timezone(): string
    {
        $timezone = $this->get('system.timezone');
        if (!array_key_exists($timezone, self::TIMEZONE_OPTIONS)) {
            Log::warning('Zona waktu system setting tidak dikenali; Asia/Makassar digunakan.', [
                'setting_key' => 'system.timezone',
            ]);

            return 'Asia/Makassar';
        }

        return $timezone;
    }

    public function timezoneLabel(?string $timezone = null): string
    {
        $timezone ??= $this->timezone();

        return self::TIMEZONE_OPTIONS[$timezone] ?? self::TIMEZONE_OPTIONS['Asia/Makassar'];
    }

    public function timezoneOptions(): array
    {
        return self::TIMEZONE_OPTIONS;
    }

    /** Whether the last attempt to load settings failed and defaults were used. */
    public function hasReadFailure(): bool
    {
        $this->all();

        return $this->settingsReadFailed;
    }

    /** Return a setting converted according to its stored or inferred type. */
    public function getTyped(string $key): mixed
    {
        if (!array_key_exists($key, self::DEFAULTS)) {
            return null;
        }

        $settings = $this->all();
        $type = $this->types[$key] ?? self::DEFAULT_TYPES[$key] ?? 'string';
        [$valid, $value] = $this->convertValue($settings[$key], $type);

        if ($valid) {
            return $value;
        }

        Log::warning('Nilai system setting tidak sesuai tipe; nilai default digunakan.', [
            'setting_key' => $key,
            'value_type' => $type,
        ]);

        if ($key === 'ba_rekon.upload_enabled') {
            return false;
        }

        [$defaultValid, $defaultValue] = $this->convertValue(
            self::DEFAULTS[$key],
            self::DEFAULT_TYPES[$key] ?? 'string'
        );

        return $defaultValid ? $defaultValue : null;
    }

    public function forgetCache(): void
    {
        $this->settings = null;
        $this->types = null;
        $this->settingsReadFailed = false;
    }

    public function submissionAvailability(
        Department $department,
        ?CarbonImmutable $now = null,
        ?string $resolvedBaStatus = null,
        bool $baStatusResolved = false
    ): array
    {
        $this->all();
        if ($this->settingsReadFailed) {
            return [
                'allowed' => false,
                'reason' => 'settings_unavailable',
                'message' => 'Pengaturan akses sistem gagal dibaca. Pengajuan baru sementara dibatasi; silakan hubungi administrator.',
            ];
        }

        $timezone = $this->timezone();
        $now = ($now ?? CarbonImmutable::now($timezone))
            ->setTimezone($timezone);
        $manualAccess = $department->akses_pengajuan_manual;
        $mode = $this->get('submission.schedule_mode');

        if ($manualAccess === false) {
            return ['allowed' => false, 'reason' => 'department_closed', 'message' => 'Akses pengajuan sedang ditutup oleh administrator.'];
        }

        if ($mode === 'closed') {
            return ['allowed' => false, 'reason' => 'schedule_closed', 'message' => $this->get('submission.closed_message')];
        }

        if ($mode === 'open') {
            return ['allowed' => true, 'reason' => null, 'message' => null];
        }

        if ($this->isWithinLockWindow($now)) {
            $start = $this->get('submission.lock_start_time');
            $end = $this->get('submission.lock_end_time');
            $timeZoneLabel = $this->timezoneLabel($timezone);

            $message = str_replace(
                ['{start}', '{end}', '{timezone}'],
                [$start, $end, $timeZoneLabel],
                $this->get('submission.schedule_message')
            );

            return [
                'allowed' => false,
                'reason' => 'schedule_time',
                'message' => $message,
            ];
        }

        if ($manualAccess !== true && $now->day > (int) $this->getTyped('ba_rekon.deadline_day')) {
            $baStatus = $baStatusResolved
                ? $resolvedBaStatus
                : BaRekon::query()
                    ->where('department_id', $department->id)
                    ->where('bulan', $now->month)
                    ->where('tahun', $now->year)
                    ->value('status');

            if ($baStatus !== 'verified') {
                $message = match ($baStatus) {
                    'pending' => 'BA Rekon sudah diunggah dan sedang menunggu verifikasi. Akses pengajuan akan terbuka setelah BA Rekon diverifikasi.',
                    'rejected' => 'BA Rekon ditolak. Silakan unggah perbaikan dan tunggu verifikasi.',
                    default => $this->get('ba_rekon.access_message'),
                };

                return [
                    'allowed' => false,
                    'reason' => 'ba_rekon',
                    'message' => $message,
                ];
            }
        }

        return ['allowed' => true, 'reason' => null, 'message' => null];
    }

    public function baRekonUploadAvailability(?CarbonImmutable $now = null, bool $isCorrection = false): array
    {
        $this->all();
        if ($this->settingsReadFailed) {
            return [
                'allowed' => false,
                'message' => 'Pengaturan BA Rekon gagal dibaca. Unggahan sementara ditutup; silakan hubungi administrator.',
                'reason' => 'settings_unavailable',
            ];
        }

        if ($this->getTyped('ba_rekon.upload_enabled') === true) {
            $timezone = $this->timezone();
            $now = ($now ?? CarbonImmutable::now($timezone))
                ->setTimezone($timezone);
            $startDay = (int) $this->getTyped('ba_rekon.upload_start_day');
            $endDay = (int) $this->getTyped('ba_rekon.upload_end_day');

            if ($now->day >= $startDay && $now->day <= $endDay) {
                return ['allowed' => true, 'message' => null, 'reason' => null];
            }

            if ($isCorrection) {
                return [
                    'allowed' => true,
                    'message' => 'Periode unggah baru BA Rekon ditutup; perbaikan dokumen yang sudah ada tetap diperbolehkan.',
                    'reason' => 'correction_outside_upload_period',
                ];
            }

            return [
                'allowed' => false,
                'message' => $this->get('ba_rekon.closed_message'),
                'reason' => 'outside_upload_period',
            ];
        }

        return [
            'allowed' => false,
            'message' => $this->get('ba_rekon.closed_message'),
            'reason' => 'uploads_disabled',
        ];
    }

    private function isWithinLockWindow(CarbonImmutable $now): bool
    {
        $start = $this->minutes($this->get('submission.lock_start_time'));
        $end = $this->minutes($this->get('submission.lock_end_time'));
        $current = ((int) $now->format('H') * 60) + (int) $now->format('i');

        return $current >= $start && $current < $end;
    }

    private function minutes(string $time): int
    {
        [$hour, $minute] = array_map('intval', explode(':', $time));

        return ($hour * 60) + $minute;
    }

    private function resolveType(string $key, string $storedType): string
    {
        $type = strtolower(trim($storedType));
        $type = match ($type) {
            'bool' => 'boolean',
            'int' => 'integer',
            'decimal', 'double' => 'float',
            default => $type,
        };

        // Legacy rows commonly use the migration's generic "text" type.
        // Infer known non-string keys in that case to preserve their meaning.
        if ($type === '' || in_array($type, ['text', 'string'], true)) {
            return self::DEFAULT_TYPES[$key] ?? 'string';
        }

        if (in_array($type, ['boolean', 'integer', 'number', 'float', 'json', 'time'], true)) {
            return $type;
        }

        Log::warning('Tipe system setting tidak dikenali; tipe fallback berdasarkan key digunakan.', [
            'setting_key' => $key,
            'value_type' => $type,
        ]);

        return self::DEFAULT_TYPES[$key] ?? 'string';
    }

    /** @return array{0: bool, 1: mixed} */
    private function convertValue(mixed $value, string $type): array
    {
        $raw = is_string($value) ? trim($value) : $value;

        try {
            return match ($type) {
                'boolean' => $this->convertBoolean($raw),
                'integer' => $this->convertInteger($raw),
                'number' => $this->convertNumber($raw),
                'float' => $this->convertFloat($raw),
                'json' => [true, json_decode((string) $raw, true, 512, JSON_THROW_ON_ERROR)],
                'time', 'string', 'text' => [true, (string) $value],
                default => [false, null],
            };
        } catch (JsonException) {
            return [false, null];
        }
    }

    /** @return array{0: bool, 1: mixed} */
    private function convertBoolean(mixed $value): array
    {
        $converted = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $converted === null ? [false, null] : [true, $converted];
    }

    /** @return array{0: bool, 1: mixed} */
    private function convertInteger(mixed $value): array
    {
        $converted = filter_var($value, FILTER_VALIDATE_INT);

        return $converted === false ? [false, null] : [true, $converted];
    }

    /** @return array{0: bool, 1: mixed} */
    private function convertNumber(mixed $value): array
    {
        $integer = filter_var($value, FILTER_VALIDATE_INT);
        if ($integer !== false) {
            return [true, $integer];
        }

        return $this->convertFloat($value);
    }

    /** @return array{0: bool, 1: mixed} */
    private function convertFloat(mixed $value): array
    {
        $converted = filter_var($value, FILTER_VALIDATE_FLOAT);

        return $converted === false || !is_finite((float) $converted)
            ? [false, null]
            : [true, (float) $converted];
    }

    private function databaseFailureContext(Throwable $exception): array
    {
        $context = ['exception_class' => $exception::class];

        if ($exception instanceof QueryException) {
            $context['sql_state'] = $exception->errorInfo[0] ?? $exception->getCode();
            $context['driver_code'] = $exception->errorInfo[1] ?? null;
        }

        return $context;
    }

    private function isMissingValueTypeColumn(QueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());

        return str_contains($message, "unknown column 'value_type'")
            || str_contains($message, 'no such column: value_type')
            || str_contains($message, 'no such column: system_settings.value_type');
    }
}
