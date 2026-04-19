<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Properti fillable standar Laravel (Lebih aman untuk versi ini)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id', // Ini yang akan menghilangkan error NULL
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // app/Models/User.php

public function getRoleLabelAttribute()
{
    $role = $this->roles->first()?->name;

    return match ($role) {
        'super_admin' => 'SU',
        'operator' => 'Operator',
        'kabid_perbendaharaan' => 'Kepala Bidang Perbendaharaan',
        'kasie_bud' => 'Kepala Seksi BUD',
        'bpk' => 'Badan Pemeriksa Keuangan',
        default => ucfirst($role),
    };
}
}