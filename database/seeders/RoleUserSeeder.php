<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['super_admin', 'operator', 'kabid_perbendaharaan', 'bpk', 'kasie_bud'];
        foreach ($roles as $roleName) {
            Role::updateOrCreate(['name' => $roleName]);
        }

        $allDepartments = Department::all();

        if ($allDepartments->isEmpty()) {
            $this->command->error('Gagal Seeding: Tidak ada data Dinas ditemukan. Jalankan DepartmentSeeder dulu!');
            return;
        }

        $bkd = Department::where('nama_dinas', 'LIKE', '%BADAN KEUANGAN DAERAH%')->first();

        $specialUsers = [
            [
                'name' => 'Nizam Achda',
                'email' => 'SU@bolmong.go.id',
                'password' => Hash::make('password'),
                'department_id' => $bkd->id ?? $allDepartments->first()->id,
                'role' => 'super_admin',
            ],
            [
                'name' => 'Sofiah Y. Matantu, SH',
                'email' => 'kasie.bud@bolmong.go.id',
                'password' => Hash::make('password'),
                'department_id' => $bkd->id ?? $allDepartments->first()->id,
                'role' => 'kasie_bud',
            ],
            [
                'name' => 'Wawan S. Gaib, SE',
                'email' => 'kabid@bolmong.go.id',
                'password' => Hash::make('password'),
                'department_id' => $bkd->id ?? $allDepartments->first()->id,
                'role' => 'kabid_perbendaharaan',
            ],
            [
                'name' => 'Tim Pemeriksa BPK',
                'email' => 'bpk@bolmong.go.id',
                'password' => Hash::make('password'),
                'department_id' => $bkd->id ?? $allDepartments->first()->id,
                'role' => 'bpk',
            ],
        ];

        foreach ($specialUsers as $sUser) {
            $role = $sUser['role'];
            unset($sUser['role']);
            $user = User::updateOrCreate(['email' => $sUser['email']], $sUser);
            $user->assignRole($role);
        }

        foreach ($allDepartments as $dept) {
            $slug = Str::slug($dept->kode_dinas);
            $email = "operator." . $slug . "@bolmong.go.id";

            $operator = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => "Operator " . $dept->kode_dinas,
                    'email' => $email,
                    'password' => Hash::make('password'), // Password default
                    'department_id' => $dept->id,
                ]
            );

            $operator->assignRole('operator');
        }

        $this->command->info('RoleUserSeeder sukses: User pimpinan dibuat & 1 Operator per SKPD telah aktif.');
    }
}
