<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $appends = ['jenis_dokumen'];
    protected $fillable = [
        'submission_id',
        'jenis_dokumen',
        'file_path',
        'status_dokumen',
        'catatan',
    ];

    // Relasi balik ke data Submission utama
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

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

}