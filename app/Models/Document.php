<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    const STATUS_PENDING  = 'pending';
    const STATUS_RESET    = 'reset';
    const STATUS_REVISION = 'revisi';
    const STATUS_REJECTED = 'rejected';
    const STATUS_VERIFIED = 'verified';

    protected $fillable = [
        'submission_id',
        'jenis_dokumen',
        'file_path',
        'status_dokumen',
        'catatan',
    ];

    protected $appends = ['status_style', 'preview_url'];

    // Method: submission
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    // Method: getJenisDokumenAttribute
    public function getJenisDokumenAttribute($value)
    {
        $labels = [
            'spp_spm'   => 'SPP/SPM',
            'kwitansi'  => 'Kwitansi',
            'rincian'   => 'Rincian Kegiatan',
            'laporan'   => 'Laporan Kegiatan',
            'foto'      => 'Foto Kegiatan',
            'pendukung' => 'Dokumen Pendukung',
        ];

        return $labels[$value] ?? ucwords(str_replace('_', ' ', $value));
    }

    // Method: getStatusStyleAttribute
    public function getStatusStyleAttribute()
    {
        $status = $this->status_dokumen;

        return match($status) {
            self::STATUS_PENDING  => ['color' => 'primary',   'icon' => 'fa-clock',                'label' => 'Pending'],
            self::STATUS_RESET    => ['color' => 'dark',      'icon' => 'fa-undo',                 'label' => 'Reset'],
            self::STATUS_REVISION => ['color' => 'warning',   'icon' => 'fa-exclamation-triangle', 'label' => 'Revisi'],
            self::STATUS_REJECTED => ['color' => 'danger',    'icon' => 'fa-times-circle',         'label' => 'Rejected'],
            self::STATUS_VERIFIED => ['color' => 'success',   'icon' => 'fa-check-circle',         'label' => 'Verified'],
            default               => ['color' => 'secondary', 'icon' => 'fa-question',             'label' => $status],
        };
    }

    // Method: getPreviewUrlAttribute (Untuk Pratinjau PDF di Modal/Iframe)
    public function getPreviewUrlAttribute(): string
    {
        if (empty($this->file_path)) {
            return '#';
        }

        $disk = config('filesystems.default', 's3');

        if ($disk === 'public') {
            return asset('storage/' . $this->file_path);
        }

        try {
            return Storage::disk($disk)->temporaryUrl(
                $this->file_path,
                now()->addMinutes(30),
                [
                    'ResponseContentType'        => 'application/pdf',
                    'ResponseContentDisposition' => 'inline; filename="' . basename($this->file_path) . '"',
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Gagal membuat preview temporaryUrl Backblaze B2', [
                'document_id' => $this->id,
                'file_path'   => $this->file_path,
                'error'       => $e->getMessage(),
            ]);

            return asset('storage/' . $this->file_path);
        }
    }

    // Method: getDownloadUrlAttribute (Opsional: Jika ada tombol Download langsung ke perangkat)
    public function getDownloadUrlAttribute(): string
    {
        if (empty($this->file_path)) {
            return '#';
        }

        $disk = config('filesystems.default', 's3');

        if ($disk === 'public') {
            return asset('storage/' . $this->file_path);
        }

        try {
            return Storage::disk($disk)->temporaryUrl(
                $this->file_path,
                now()->addMinutes(30),
                [
                    'ResponseContentType'        => 'application/pdf',
                    'ResponseContentDisposition' => 'attachment; filename="' . basename($this->file_path) . '"',
                ]
            );
        } catch (\Throwable $e) {
            return asset('storage/' . $this->file_path);
        }
    }
}