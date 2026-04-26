<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    // KONSTANTA STATUS (Sesuai Permintaan)
    const STATUS_PENDING  = 'pending';   // Baru diinput operator
    const STATUS_RESET    = 'reset';     // Direset Admin
    const STATUS_REVISION = 'revisi';    // Ditolak Kasie BUD
    const STATUS_REJECTED = 'rejected';  // Ditolak Kabid
    const STATUS_VERIFIED = 'verified';  // Selesai

    protected $fillable = [
        'submission_id',
        'jenis_dokumen',
        'file_path',
        'status_dokumen', // Kolom database Anda
        'catatan',
    ];

    // Menambahkan atribut virtual agar bisa dipanggil langsung di Blade
    protected $appends = ['status_style'];

    // Relasi balik ke data Submission utama
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    // Accessor untuk Label Jenis Dokumen
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

    /**
     * Accessor untuk Gaya Visual Status
     * Menggunakan kolom 'status_dokumen' sesuai $fillable Anda
     */
        public function getStatusStyleAttribute()
        {
            $status = $this->status_dokumen;

            return match($status) {
                self::STATUS_PENDING  => ['color' => 'primary',   'icon' => 'fa-clock',    'label' => 'Pending'],
                self::STATUS_RESET    => ['color' => 'dark',      'icon' => 'fa-undo',     'label' => 'Reset'], // Warna Gelap
                self::STATUS_REVISION => ['color' => 'warning',   'icon' => 'fa-exclamation-triangle', 'label' => 'Revisi'],
                self::STATUS_REJECTED => ['color' => 'danger',    'icon' => 'fa-times-circle', 'label' => 'Rejected'],
                self::STATUS_VERIFIED => ['color' => 'success',   'icon' => 'fa-check-circle', 'label' => 'Verified'],
                default               => ['color' => 'secondary', 'icon' => 'fa-question', 'label' => $status],
            };
        }
}