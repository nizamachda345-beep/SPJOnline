<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use BackedEnum;
use Illuminate\Support\Arr;
use Spatie\Permission\Contracts\Role as RoleContract;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles {
        hasRole as private hasStoredRole;
        hasAnyRole as private hasAnyStoredRole;
    }

    /** The account's real role; never affected by the selected access mode. */
    public function isActualSuperAdmin(): bool
    {
        return $this->hasStoredRole('super_admin');
    }

    /** Role currently being viewed by a Super Admin in this session. */
    public function accessMode(): ?string
    {
        $request = request();

        if (!$this->isActualSuperAdmin() || !$request->hasSession()) {
            return null;
        }

        $mode = $request->session()->get('master_access_mode');

        return is_string($mode) ? $mode : null;
    }

    public function hasRole($roles, ?string $guard = null): bool
    {
        if (!$this->isActualSuperAdmin() || !$this->accessMode()) {
            return $this->hasStoredRole($roles, $guard);
        }

        if ($roles instanceof RoleContract) {
            $roles = $roles->name;
        } elseif ($roles instanceof BackedEnum) {
            $roles = $roles->value;
        }

        $expected = collect(Arr::wrap($roles))
            ->flatten()
            ->flatMap(function ($role) {
                if ($role instanceof RoleContract) {
                    return [$role->name];
                }

                if ($role instanceof BackedEnum) {
                    $role = (string) $role->value;
                }

                return is_string($role) ? explode('|', $role) : [(string) $role];
            });

        return $expected->contains($this->accessMode());
    }

    public function hasAnyRole(...$roles): bool
    {
        if (!$this->isActualSuperAdmin() || !$this->accessMode()) {
            return $this->hasAnyStoredRole(...$roles);
        }

        return $this->hasRole($roles);
    }

    protected $fillable = [
        'name',
        'nip',
        'email',
        'password',
        'department_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Method: casts
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen_at' => 'datetime',
        ];
    }

    // Method: department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

// Method: getRoleLabelAttribute
public function getRoleLabelAttribute()
{
    $role = $this->isActualSuperAdmin() && $this->accessMode()
        ? $this->accessMode()
        : $this->roles->first()?->name;

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
