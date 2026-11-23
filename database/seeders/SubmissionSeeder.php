<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Submission;
use App\Models\User;
use Carbon\Carbon;

class SubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();

        $operator = User::role('operator')->first() ?? User::first();
        $kasie    = User::role('kasie_bud')->first() ?? User::first();
        $kabid    = User::role('kabid_perbendaharaan')->first() ?? User::first();

        $jenisPencairan = ['LS', 'GU', 'TU', 'LS Nihil', 'GU Nihil', 'TU Nihil','UP'];
        $statusOptions = ['pending', 'proses validasi', 'verified', 'disetujui', 'reset', 'revisi', 'rejected'];

        foreach ($departments as $dept) {
            for ($i = 1; $i <= 15; $i++) {
                $status = $statusOptions[array_rand($statusOptions)];
                $tanggalBuat = Carbon::now()->subDays(rand(10, 60));

                $isVerified = in_array($status, ['verified', 'disetujui']);

                $isApproved = ($status == 'disetujui');

                Submission::create([
                    'user_id'            => $operator->id,
                    'department_id'      => $dept->id,
                    'nomor_spp'      => str_pad(rand(1, 99999), 6, '0', STR_PAD_LEFT),
                    'nomor_spm'      => str_pad(rand(1, 99999), 6, '0', STR_PAD_LEFT),
                    'jenis_pencairan'    => $jenisPencairan[array_rand($jenisPencairan)],
                    'tanggal'            => $tanggalBuat->format('Y-m-d'),
                    'judul_kegiatan'     => 'Keg. ' . $i . ' ' . ($dept->nama_dinas ?? 'SKPD'),
                    'judul_sub_kegiatan' => 'Sub Keg. ' . $i,
                    'perihal'            => 'Permohonan pencairan dana triwulan untuk ' . ($dept->nama_dinas ?? 'Instansi'),
                    'total_pencairan'    => rand(10000000, 500000000),
                    'status'             => $status,

                    'catatan_kabid'      => $status == 'reset' ? 'Perbaiki lampiran dokumen utama.' : 'Sesuai aturan.',
                    'catatan'            => 'Data testing untuk simulasi alur Kasie ke Kabid.',

                    'verified_by_name'   => $isVerified ? $kasie->name : null,
                    'verified_at'        => $isVerified ? $tanggalBuat->copy()->addDays(2) : null,
                    'verified_by'        => $isVerified ? $kasie->id : null,

                    'validated_by_name'   => $isApproved ? $kabid->name : null,
                    'validated_at'       => $isApproved ? $tanggalBuat->copy()->addDays(4) : null,
                    'validated_by'       => $isApproved ? $kabid->id : null,

                    'created_at'         => $tanggalBuat,
                    'updated_at'         => Carbon::now(),
                ]);
            }
        }
    }
}
