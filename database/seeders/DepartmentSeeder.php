<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $dinas = [
            ['nama_dinas' => 'DINAS KETAHANAN PANGAN', 'kode_dinas' => 'DKP'],
            ['nama_dinas' => 'DINAS KESEHATAN', 'kode_dinas' => 'DINKES'],
            ['nama_dinas' => 'DINAS PERDAGANGAN', 'kode_dinas' => 'DISDAG'],
            ['nama_dinas' => 'RSUD', 'kode_dinas' => 'RSUD'],
            ['nama_dinas' => 'DINAS PERTANIAN', 'kode_dinas' => 'DISTANI'],
            ['nama_dinas' => 'DINAS PENDIDIKAN', 'kode_dinas' => 'DISDIK'],
            ['nama_dinas' => 'DINAS PERIKANAN', 'kode_dinas' => 'DISKAN'],
            ['nama_dinas' => 'INSPEKTORAT DAERAH', 'kode_dinas' => 'INSP'],
            ['nama_dinas' => 'DINAS KOMUNIKASI DAN INFORMATIKA', 'kode_dinas' => 'DISKOMINFO'],
            ['nama_dinas' => 'SEKRETARIAT DAERAH', 'kode_dinas' => 'SETDA'],
            ['nama_dinas' => 'BADAN PERENCANAAN PEMBANGUNAN DAERAH', 'kode_dinas' => 'BAPPEDA'],
            ['nama_dinas' => 'DINAS TRANSMIGRASI DAN KETENAGAKERJAAN', 'kode_dinas' => 'DISTRAKER'],
            ['nama_dinas' => 'BADAN KEPEGAWAIAN PENDIDIKAN & PELATIHAN', 'kode_dinas' => 'BKPP'],
            ['nama_dinas' => 'BADAN PENANGGULANGAN BENCANA DAERAH', 'kode_dinas' => 'BPBD'],
            ['nama_dinas' => 'BADAN KESBANG DAN POLITIK', 'kode_dinas' => 'KESBANGPOL'],
            ['nama_dinas' => 'DINAS PARIWISATA DAN KEBUDAYAAN', 'kode_dinas' => 'DISPARBUD'],
            ['nama_dinas' => 'SEKRETARIAT DPRD', 'kode_dinas' => 'SETWAN'],
            ['nama_dinas' => 'DINAS PU', 'kode_dinas' => 'DPU'],
            ['nama_dinas' => 'DINAS PEMUDA DAN OLAHRAGA', 'kode_dinas' => 'DISPORA'],
            ['nama_dinas' => 'BADAN KEUANGAN DAERAH', 'kode_dinas' => 'BKD'],
            ['nama_dinas' => 'DINAS PENANAMAN MODAL', 'kode_dinas' => 'DPM'],
            ['nama_dinas' => 'DINAS LINGKUNGAN HIDUP', 'kode_dinas' => 'DLH'],
            ['nama_dinas' => 'DINAS KOPERASI USAHA KECIL', 'kode_dinas' => 'DISKOPUK'],
            ['nama_dinas' => 'DINAS PERUMAHAN', 'kode_dinas' => 'DISPERUM'],
            ['nama_dinas' => 'DINAS PERPUSTAKAAN', 'kode_dinas' => 'DISPUS'],
            ['nama_dinas' => 'DINAS PERKEBUNAN', 'kode_dinas' => 'DISBUN'],
            ['nama_dinas' => 'DINAS PENGENDALIAN PENDUDUK', 'kode_dinas' => 'DALDUK'],
            ['nama_dinas' => 'DINAS KEPENDUDUKAN & PENCATATAN SIPIL', 'kode_dinas' => 'DUKCAPIL'],
            ['nama_dinas' => 'KANTOR CAMAT DUMOGA', 'kode_dinas' => 'CAM-DUM'],
            ['nama_dinas' => 'DINAS PERHUBUNGAN', 'kode_dinas' => 'DISHUB'],
            ['nama_dinas' => 'DINAS PEMBERDAYAAN PEREMPUAN', 'kode_dinas' => 'DPP'],
            ['nama_dinas' => 'KANTOR CAMAT PASSI BARAT', 'kode_dinas' => 'CAM-PASBAR'],
            ['nama_dinas' => 'KANTOR CAMAT BOLAANG', 'kode_dinas' => 'CAM-BOL'],
            ['nama_dinas' => 'KANTOR CAMAT BILALANG', 'kode_dinas' => 'CAM-BIL'],
            ['nama_dinas' => 'KANTOR CAMAT BOLAANG TIMUR', 'kode_dinas' => 'CAM-BOLTIM'],
            ['nama_dinas' => 'KANTOR CAMAT LOLAK', 'kode_dinas' => 'CAM-LOLAK'],
            ['nama_dinas' => 'DINAS SATUAN POLISI PAMONG PRAJA', 'kode_dinas' => 'SATPOLPP'],
            ['nama_dinas' => 'KANTOR CAMAT SANG TOMBOLANG', 'kode_dinas' => 'CAM-SANGTOM'],
            ['nama_dinas' => 'KANTOR CAMAT DUMOGA TIMUR', 'kode_dinas' => 'CAM-DUMTIM'],
            ['nama_dinas' => 'KANTOR CAMAT POIGAR', 'kode_dinas' => 'CAM-POI'],
            ['nama_dinas' => 'KANTOR CAMAT PASSI TIMUR', 'kode_dinas' => 'CAM-PASTIM'],
            ['nama_dinas' => 'KANTOR CAMAT LOLAYAN', 'kode_dinas' => 'CAM-LOLAYAN'],
            ['nama_dinas' => 'KANTOR CAMAT DUMOGA TENGGARA', 'kode_dinas' => 'CAM-DUMRA'],
            ['nama_dinas' => 'KANTOR CAMAT DUMOGA TENGAH', 'kode_dinas' => 'CAM-DUMTENG'],
            ['nama_dinas' => 'DINAS SOSIAL', 'kode_dinas' => 'DINSOS'],
            ['nama_dinas' => 'KANTOR CAMAT DUMOGA UTARA', 'kode_dinas' => 'CAM-DUMUT'],
            ['nama_dinas' => 'KANTOR CAMAT DUMOGA BARAT', 'kode_dinas' => 'CAM-DUMBAR'],
            ['nama_dinas' => 'DINAS PEMBERDAYAAN MASYARAKAT', 'kode_dinas' => 'DPMD'],
        ];

        foreach ($dinas as $item) {
            Department::updateOrCreate(
                ['kode_dinas' => $item['kode_dinas']],
                ['nama_dinas' => $item['nama_dinas']]
            );
        }
    }
}