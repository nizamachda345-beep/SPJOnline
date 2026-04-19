<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT ROLE (Pastikan Spatie Permission sudah terinstall)
        $roles = ['super_admin', 'operator', 'kabid_perbendaharaan', 'bpk', 'kasie_bud'];
        foreach ($roles as $roleName) {
            Role::updateOrCreate(['name' => $roleName]);
        }

        // 2. AMBIL DATA DINAS (Pastikan DepartmentSeeder sudah dijalankan lebih dulu)
        $bkd = Department::where('nama_dinas', 'BADAN KEUANGAN DAERAH')->first();
        $dinkes = Department::where('nama_dinas', 'DINAS KESEHATAN')->first();

        // Proteksi: Jika dinas tidak ditemukan, hentikan seeder agar tidak terjadi error "null"
        if (!$bkd || !$dinkes) {
            $this->command->error('Gagal Seeding: Data Dinas BKD atau Dinkes tidak ditemukan di database!');
            return;
        }

        // 3. DAFTAR USER YANG AKAN DIBUAT
        $users = [
            [
                'name' => 'Admin iDJ Bolmong',
                'email' => 'admin@bolmong.go.id',
                'password' => Hash::make('password'),
                'department_id' => $bkd->id,
                'role' => 'super_admin',
            ],
            [
                'name' => 'Kepala Seksi BUD',
                'email' => 'kasie.bud@bolmong.go.id',
                'password' => Hash::make('password'),
                'department_id' => $bkd->id,
                'role' => 'kasie_bud',
            ],
            [
                'name' => 'Operator BKD',
                'email' => 'operator.bkd@bolmong.go.id',
                'password' => Hash::make('password'),
                'department_id' => $bkd->id,
                'role' => 'operator',
            ],
            [
                'name' => 'Operator Dinkes',
                'email' => 'operator.dinkes@bolmong.go.id',
                'password' => Hash::make('password'),
                'department_id' => $dinkes->id,
                'role' => 'operator',
            ],
            [
                'name' => 'Kabid Perbendaharaan',
                'email' => 'kabid@bolmong.go.id',
                'password' => Hash::make('password'),
                'department_id' => $bkd->id,
                'role' => 'kabid_perbendaharaan',
            ],
            [
                'name' => 'Tim Pemeriksa BPK',
                'email' => 'bpk@bolmong.go.id',
                'password' => Hash::make('password'),
                'department_id' => $bkd->id,
                'role' => 'bpk',
            ],
        ];

        // 4. EKSEKUSI PEMBUATAN USER
        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']); // Hapus key role agar tidak bentrok dengan fillable User

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->assignRole($role);
        }

        $this->command->info('RoleUserSeeder berhasil dijalankan. Semua user sudah memiliki department_id.');
    }
}