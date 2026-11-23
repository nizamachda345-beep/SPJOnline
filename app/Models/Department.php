<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\SystemSettingsService;

class Department extends Model
{
    protected $fillable = [
        'nama_dinas',
        'kode_dinas',
        'akses_pengajuan_manual',
        'akses_pengajuan_dibuka_oleh',
    ];

    protected $casts = [
        'akses_pengajuan_manual' => 'boolean',
    ];

    // Method: users
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Method: baRekons
    public function baRekons(): HasMany
    {
        return $this->hasMany(BaRekon::class);
    }

    // Method: canUpload
    public static function canUpload($departmentId)
    {
        $department = static::find($departmentId);
        if (!$department) {
            return false;
        }

        return app(SystemSettingsService::class)->submissionAvailability($department)['allowed'];
    }
}
