<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;
    const STATUS_RESET = 'reset';

    protected $fillable = [
    'user_id',
    'department_id',
    'nomor_spp_spm',
    'tanggal',
    'judul_kegiatan',
    'judul_sub_kegiatan',
    'perihal',
    'total_pencairan',
    'status',
    // Kolom Baru
    'verified_by_name',
    'verified_at',
    'validated_by_name',
    'validated_at',
    'catatan',
    'jenis_pencairan',
    ];

// Supaya tanggal otomatis jadi objek Carbon (mudah diformat)
protected $casts = [
    'verified_at' => 'datetime',
    'validated_at' => 'datetime',
    'tanggal' => 'date',
];

    // Relasi ke User (Siapa yang upload)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Dinas
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Relasi ke banyak dokumen/file PDF
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // app/Models/Submission.php

public function scopeFilter($query, array $filters)
{
    // Filter berdasarkan Nomor SPP/SPM (Pencarian Parsial)
    $query->when($filters['nomor'] ?? false, function ($query, $nomor) {
        return $query->where('nomor_spp_spm', 'like', '%' . $nomor . '%');
    });

    // Filter berdasarkan Dinas
    $query->when($filters['dinas'] ?? false, function ($query, $dinas) {
        return $query->where('department_id', $dinas);
    });

    // Filter berdasarkan Tanggal Spesifik
    $query->when($filters['tanggal'] ?? false, function ($query, $tanggal) {
        return $query->whereDate('created_at', $tanggal);
    });

    // Filter berdasarkan Minimal Nominal
    $query->when($filters['min_nominal'] ?? false, function ($query, $min) {
        return $query->where('total_pencairan', '>=', $min);
    });
}

// Tambahkan di App\Models\Submission.php

public function getStatusLabelAttribute()
    {
        // 1. Logika untuk status RESET (Oleh Admin)
        if ($this->status === self::STATUS_RESET) {
            return [
                'label' => 'Direset Admin',
                'sub'   => 'Data kembali ke Operator',
                'color' => 'dark', // Kita pakai warna gelap
                'icon'  => 'fa-undo'
            ];
        }

        // 2. Logika untuk status REVISI/PENDING ULANG
        if ($this->status === 'pending' && $this->verified_by_name !== null) {
            return [
                'label' => 'Menunggu Verifikasi',
                'sub'   => 'Telah direvisi oleh Operator',
                'color' => 'info',
                'icon'  => 'fa-sync-alt'
            ];
        }

        // 3. Logika Standar
        return match($this->status) {
            'pending' => ['label' => 'Menunggu Verifikasi', 'sub' => null, 'color' => 'warning', 'icon' => 'fa-clock'],
            'revisi'  => ['label' => 'Perlu Revisi',         'sub' => null, 'color' => 'danger',  'icon' => 'fa-exclamation-circle'],
            'verified'=> ['label' => 'Disetujui',           'sub' => null, 'color' => 'success', 'icon' => 'fa-check-circle'],
            'proses validasi' => ['label' => 'Proses Validasi', 'sub' => 'Menunggu Kabid', 'color' => 'primary', 'icon' => 'fa-file-signature'],
            default   => ['label' => ucfirst($this->status), 'sub' => null, 'color' => 'secondary', 'icon' => 'fa-info-circle'],
        };
    }
    

}