<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Submission extends Model
{
    use HasFactory;

    const STATUS_RESET = 'reset';

    protected $fillable = [
        'user_id', 'department_id', 'nomor_spp', 'nomor_spm', 'jenis_pencairan',
        'tanggal', 'judul_kegiatan', 'judul_sub_kegiatan', 'perihal',
        'total_pencairan', 'status', 'catatan_kabid', 'catatan',
        'verified_by_name', 'verified_at', 'verified_by',
        'validated_at', 'validated_by_name', 'validated_by',
        'sent_to_kabid_by_name', 'sent_to_kabid_at', 'rejected_at',
        'revised_at', 'revisi_at', 'tolak_by', 'reset_at', 'tolak_by_kabid', 'status_audit', 'catatan_audit', 'sumber_dana','tanggal_spp','tanggal_spm', 'expires_at',
    ];

    protected $casts = [
        'verified_at'      => 'datetime',
        'validated_at'     => 'datetime',
        'tanggal'          => 'date',
        'sent_to_kabid_at' => 'datetime',
        'rejected_at'      => 'datetime',
        'revised_at'       => 'datetime',
        'revisi_at'        => 'datetime',
        'reset_at'         => 'datetime',
        'tolak_by'         => 'integer',
        'tolak_by_kabid'   => 'integer',
    ];

    // Method: user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Method: department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Method: documents
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // Method: penolak
    public function penolak()
    {
        return $this->belongsTo(User::class, 'tolak_by');
    }

    // Method: penolakKabid
    public function penolakKabid()
    {
        return $this->belongsTo(User::class, 'tolak_by_kabid');
    }

    // Method: scopeFilter
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['nomor_spp'] ?? false, function ($query, $spp) {
            return $query->where('nomor_spp', 'like', '%' . $spp . '%');
        });

        $query->when($filters['nomor_spm'] ?? false, function ($query, $spm) {
            return $query->where('nomor_spm', 'like', '%' . $spm . '%');
        });

        $query->when($filters['dinas'] ?? false, function ($query, $dinas) {
            return $query->where('department_id', $dinas);
        });

        $query->when($filters['tanggal'] ?? false, function ($query, $tanggal) {
            return $query->whereDate('created_at', $tanggal);
        });

        $query->when($filters['min_nominal'] ?? false, function ($query, $min) {
            return $query->where('total_pencairan', '>=', $min);
        });
    }

    // Method: getStatusLabelAttribute
    public function getStatusLabelAttribute()
    {
        if ($this->status === self::STATUS_RESET) {
            return [
                'label' => 'Direset Admin',
                'sub'   => 'Data kembali ke Operator',
                'color' => 'dark',
                'icon'  => 'fa-undo'
            ];
        }

        if ($this->status === 'pending' && $this->verified_by_name !== null) {
            return [
                'label' => 'Menunggu Verifikasi',
                'sub'   => 'Telah direvisi oleh Operator',
                'color' => 'info',
                'icon'  => 'fa-sync-alt'
            ];
        }

        return match($this->status) {
            'pending'         => ['label' => 'Menunggu Verifikasi', 'sub' => null, 'color' => 'warning', 'icon' => 'fa-clock'],
            'revisi'          => ['label' => 'Perlu Revisi',        'sub' => null, 'color' => 'danger',  'icon' => 'fa-exclamation-circle'],
            'rejected'        => ['label' => 'Ditolak Kabid',       'sub' => null, 'color' => 'danger',  'icon' => 'fa-times-circle'],
            'verified'        => ['label' => 'Disetujui',           'sub' => null, 'color' => 'success', 'icon' => 'fa-check-circle'],
            'proses validasi' => ['label' => 'Proses Validasi',     'sub' => 'Menunggu Kabid', 'color' => 'primary', 'icon' => 'fa-file-signature'],
            default           => ['label' => ucfirst($this->status), 'sub' => null, 'color' => 'secondary', 'icon' => 'fa-info-circle'],
        };
    }

    // Method: getRiwayatProsesAttribute
    public function getRiwayatProsesAttribute()
    {
        if ($this->validated_at) {
            return "Disahkan oleh " . $this->validated_by_name;
        }
        if ($this->verified_at) {
            return "Diverifikasi oleh " . $this->verified_by_name;
        }
        return "Tahap Operator";
    }

// Method: getStatusAttribute
public function getStatusAttribute($value)
{
    if ($value === 'pending' && $this->attributes['expires_at'] && Carbon::now()->greaterThan(Carbon::parse($this->attributes['expires_at']))) {
        $this->attributes['status'] = 'expired';
        self::where('id', $this->id)->update(['status' => 'expired']);

        return 'expired';
    }

    return $value;
}

}
