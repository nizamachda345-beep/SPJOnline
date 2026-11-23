# SPJ Online

Aplikasi internal untuk mengelola pengajuan dokumen pencairan, verifikasi, validasi, unggah SP2D, dan arsip BA Rekon. Sistem ini dibangun dengan Laravel dan menyediakan akses berdasarkan peran pengguna.

> **Catatan:** Dokumen SOP di repositori berstatus draf dan perlu ditinjau serta disahkan oleh pemilik proses sebelum diberlakukan.

## Fitur utama

- Pengajuan dan revisi dokumen SPP/SPM oleh Operator.
- Verifikasi oleh Kasie BUD dan validasi oleh Kabid Perbendaharaan.
- Unggah serta verifikasi dokumen SP2D.
- Riwayat, pencarian, ekspor Excel, dan cetak PDF.
- Pengelolaan BA Rekon dan arsip dokumen.
- Administrasi pengguna, unit kerja, pengaturan sistem, pemantauan aktivitas, dan laporan kendala untuk Super Admin.
- Akses pemeriksaan khusus untuk role BPK.

## Teknologi

- PHP 8.3 atau lebih baru
- Laravel 13
- MySQL (konfigurasi bawaan contoh)
- Node.js dan npm
- Vite, Tailwind CSS
- Pest untuk pengujian

## Persyaratan

Pastikan PHP 8.3+, Composer, Node.js/npm, dan server basis data telah tersedia. Ekstensi PHP yang dibutuhkan mengikuti persyaratan Laravel dan paket Composer yang digunakan.

## Menyiapkan aplikasi

1. Pasang dependensi PHP:

   ```bash
   composer install
   ```

2. Salin `.env.example` menjadi `.env` (di Windows PowerShell gunakan `Copy-Item .env.example .env`). Atur `APP_URL` serta koneksi basis data (`DB_*`) sesuai lingkungan Anda. Contoh bawaan memakai MySQL dengan nama basis data `idj`.

3. Buat basis data yang telah dikonfigurasi, lalu siapkan kunci aplikasi dan skema:

   ```bash
   php artisan key:generate
   php artisan migrate
   ```

4. Pasang dependensi frontend dan buat aset:

   ```bash
   npm install
   npm run build
   ```

> Akun awal dan data referensi harus disiapkan melalui prosedur administrasi proyek. Jangan gunakan data produksi untuk pengembangan lokal.

## Menjalankan dalam mode pengembangan

```bash
composer run dev
```

Perintah tersebut menjalankan server Laravel, worker antrean, dan Vite. Alternatifnya, jalankan masing-masing proses di terminal terpisah:

```bash
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

Buka alamat yang ditampilkan oleh server Laravel, biasanya `http://127.0.0.1:8000`.

## Peran pengguna

| Peran | Kegunaan utama |
|---|---|
| Operator | Membuat pengajuan, mengunggah atau memperbaiki dokumen, dan mengelola BA Rekon. |
| Kasie BUD | Memeriksa dokumen dan meneruskan pengajuan ke tahap validasi. |
| Kabid Perbendaharaan | Memvalidasi pengajuan serta mengakses riwayat. |
| Super Admin | Mengelola akun dan pengaturan, serta memantau aktivitas dan kendala. |
| BPK | Mengakses informasi dan dokumen untuk keperluan pemeriksaan sesuai hak akses. |

Hak akses aktual ditentukan oleh role dan middleware aplikasi.

## Dokumentasi

- [SOP Sistem](SOP_Sistem.md)
- [Indeks paket SOP](docs/sop/INDEKS-SOP.md)
- [Ringkasan pekerjaan dokumentasi SOP](docs/sop/RINGKASAN-PEKERJAAN.md)
- Dokumen audit dan hasil pemeriksaan tersedia di berkas Markdown pada direktori utama.

## Perintah berguna

```bash
php artisan migrate       # Jalankan migrasi basis data
php artisan route:list    # Tampilkan daftar rute
php artisan test          # Jalankan pengujian
npm run build             # Buat aset frontend untuk produksi
```

## Catatan operasional

- Atur `APP_DEBUG=false` untuk lingkungan produksi dan gunakan `APP_KEY` yang rahasia.
- Pastikan penyimpanan dokumen, backup basis data, antrean, dan izin direktori Laravel dikonfigurasi sesuai lingkungan penerapan.
- Jangan commit `.env`, kredensial, atau dokumen transaksi yang memuat data sensitif.
- Sebelum penerapan, tinjau hasil audit, jalankan migrasi pada basis data yang benar, dan pastikan kebijakan akses serta SOP telah disahkan.
