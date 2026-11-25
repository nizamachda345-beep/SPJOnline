<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected $appends = ['status_style'];

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
                self::STATUS_PENDING  => ['color' => 'primary',   'icon' => 'fa-clock',    'label' => 'Pending'],
                self::STATUS_RESET    => ['color' => 'dark',      'icon' => 'fa-undo',     'label' => 'Reset'],
                self::STATUS_REVISION => ['color' => 'warning',   'icon' => 'fa-exclamation-triangle', 'label' => 'Revisi'],
                self::STATUS_REJECTED => ['color' => 'danger',    'icon' => 'fa-times-circle', 'label' => 'Rejected'],
                self::STATUS_VERIFIED => ['color' => 'success',   'icon' => 'fa-check-circle', 'label' => 'Verified'],
                default               => ['color' => 'secondary', 'icon' => 'fa-question', 'label' => $status],
            };
        }
}
