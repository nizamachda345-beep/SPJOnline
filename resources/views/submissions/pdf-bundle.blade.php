{{-- View: submissions/pdf-bundle --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ringkasan Permintaan Transaksi SP2D - {{ $submission->nomor_spm }}</title>

    @php
        $documentLogoSetting = $systemSettings['branding.document_logo'] ?? '';
        $documentLogoPath = $documentLogoSetting && \Illuminate\Support\Facades\Storage::disk('public')->exists($documentLogoSetting)
            ? \Illuminate\Support\Facades\Storage::disk('public')->path($documentLogoSetting)
            : public_path('/logo_bolmong.png');

        $hasSp2d = $submission->documents->contains(function($doc) {
            return strtolower($doc->jenis_dokumen) === 'sp2d'
                && (
                    !empty($doc->file_path)
                    || strtolower($doc->status_dokumen) === 'approved'
                    || strtolower($doc->status_dokumen) === 'valid'
                );
        });

        $jumlahDokumen = $submission->documents->count();

        if ($jumlahDokumen <= 3) {

            $layoutClass = 'layout-normal';

        } elseif ($jumlahDokumen <= 4) {

            $layoutClass = 'layout-compact';

        } elseif ($jumlahDokumen <= 6) {

            $layoutClass = 'layout-dense';

        } elseif ($jumlahDokumen <= 8) {

            $layoutClass = 'layout-very-dense';

        } else {

            $layoutClass = 'layout-extra-dense';
        }
    @endphp



<style>

        /* =========================================================
           BODY
           ========================================================= */

        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            color: #000;
            margin: 0;
            padding: 20px;

            /*
             * Hanya tinggi yang akan disesuaikan.
             * Ukuran horizontal tetap seperti desain asli.
             */
        }

        /* =========================================================
           KOP SURAT
           ========================================================= */

        .header-table {
            width: 100%;
            border-bottom: 3px double #000;

            /*
             * Tinggi header akan menyesuaikan sedikit
             */
            padding-bottom: var(--header-padding, 10px);
            margin-bottom: var(--header-margin, 10px);
        }

        .logo-cell {
            width: 60px;
            vertical-align: top;
        }

        .text-cell {
            text-align: center;
            vertical-align: middle;
        }

        .instansi {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .dinas {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .alamat {
            font-size: 10pt;
            font-style: italic;
        }

        /* =========================================================
           SECTION HEADING
           ========================================================= */

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;

            margin-top: var(--section-margin-top, 15px);
            margin-bottom: var(--section-margin-bottom, 5px);

            background: #f0f0f0;

            padding:
                var(--section-padding-y, 5px)
                5px;

            border: 1px solid #000;
        }

        /* =========================================================
           TABEL
           ========================================================= */

        .table-data,
        .table-doc {
            width: 100%;
            border-collapse: collapse;

            margin-bottom:
                var(--table-margin-bottom, 10px);
        }

        .table-data th,
        .table-data td,
        .table-doc th,
        .table-doc td {

            border: 1px solid #000;

            /*
             * Kiri-kanan tetap 8px seperti kode asli.
             * Hanya atas-bawah yang dinamis.
             */

            padding-top:
                var(--table-padding-y, 5px);

            padding-bottom:
                var(--table-padding-y, 5px);

            padding-left: 8px;
            padding-right: 8px;

            vertical-align: top;
        }

        .table-data th {
            background-color: #f9f9f9;
            width: 30%;
            font-weight: bold;
        }

        .table-doc th {
            background-color: #f9f9f9;
            text-align: center;
        }

        /* =========================================================
           JANGAN POTONG BARIS TABEL
           ========================================================= */

        .table-data tr,
        .table-doc tr {
            page-break-inside: avoid;
        }

        /* =========================================================
           BAGIAN C DAN D
           ========================================================= */

        .section-c,
        .section-d {
            page-break-inside: avoid;
        }

        .section-c table,
        .section-d table {
            page-break-inside: avoid;
        }

        /* =========================================================
           NORMAL
           0 - 3 DOKUMEN
           ========================================================= */

        body.layout-normal {

            --header-padding: 10px;
            --header-margin: 10px;

            --section-margin-top: 15px;
            --section-margin-bottom: 5px;
            --section-padding-y: 5px;

            --table-padding-y: 5px;
            --table-margin-bottom: 10px;
        }

        /* =========================================================
           COMPACT
           4 DOKUMEN
           ========================================================= */

        body.layout-compact {

            --header-padding: 9px;
            --header-margin: 9px;

            --section-margin-top: 11px;
            --section-margin-bottom: 4px;
            --section-padding-y: 4px;

            --table-padding-y: 4px;
            --table-margin-bottom: 7px;
        }

        /* =========================================================
           DENSE
           5 - 6 DOKUMEN
           ========================================================= */

        body.layout-dense {

            --header-padding: 8px;
            --header-margin: 8px;

            --section-margin-top: 9px;
            --section-margin-bottom: 3px;
            --section-padding-y: 3.5px;

            --table-padding-y: 3.5px;
            --table-margin-bottom: 5px;
        }

        /* =========================================================
           VERY DENSE
           7 - 8 DOKUMEN
           ========================================================= */

        body.layout-very-dense {

            --header-padding: 7px;
            --header-margin: 7px;

            --section-margin-top: 7px;
            --section-margin-bottom: 3px;
            --section-padding-y: 3px;

            --table-padding-y: 3px;
            --table-margin-bottom: 4px;
        }

        /* =========================================================
           EXTRA DENSE
           9+ DOKUMEN
           ========================================================= */

        body.layout-extra-dense {

            --header-padding: 6px;
            --header-margin: 6px;

            --section-margin-top: 5px;
            --section-margin-bottom: 2px;
            --section-padding-y: 2.5px;

            --table-padding-y: 2.5px;
            --table-margin-bottom: 3px;
        }

        /* =========================================================
           FOOTER
           ========================================================= */

        .footer-sign {
            margin-top: 30px;
            float: right;
            text-align: center;
            width: 250px;
        }

        .space {
            height: 60px;
        }

    </style>
</head>

<body class="{{ $layoutClass }}">

    <table class="header-table">

        <tr>

            <td class="logo-cell">

                <img
                    src="{{ $documentLogoPath }}"
                    style="width: 70px; height: 84px;"
                >

            </td>

            <td class="text-cell">

                <div class="instansi">
                    {{ $systemSettings['branding.government_name'] ?? 'PEMERINTAH KABUPATEN BOLAANG MONGONDOW' }}
                </div>

                <div class="dinas">
                    {{ $systemSettings['branding.agency_name'] ?? 'BADAN KEUANGAN DAERAH' }}
                </div>

                <div class="alamat">

                    {{ $systemSettings['branding.office_address'] ?? 'Jl. Trans Sulawesi Desa Lalow Kecamatan Lolak, Kab. Bolaang Mongondow Provinsi Sulawesi Utara' }}

                    <br>

                    {{ $systemSettings['branding.contact'] ?? 'bkd@bolmongkab.go.id' }}

                </div>

            </td>

        </tr>

    </table>

    <div
        style="
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 15px;
            text-transform: uppercase;
        "
    >
        LEMBAR PENGAJUAN SPP/SPM
    </div>

    <div class="section-title">
        A. Informasi Pengajuan & Kegiatan
    </div>

    <table class="table-data">

        <tr>
            <th>
                Nomor / Tanggal SPP
            </th>

            <td>
                No. {{ $submission->nomor_spp }}
                (Tgl: {{ $submission->tanggal_spp }})
            </td>
        </tr>

        <tr>
            <th>
                Nomor / Tanggal SPM
            </th>

            <td>
                No. {{ $submission->nomor_spm }}
                (Tgl: {{ $submission->tanggal_spm }})
            </td>
        </tr>

        <tr>
            <th>
                Jenis Pencairan
            </th>

            <td>
                {{ $submission->jenis_pencairan }}
                /
                {{ $submission->sumber_dana }}
            </td>
        </tr>

        <tr>
            <th>
                Kegiatan
            </th>

            <td>
                {{ $submission->judul_kegiatan }}
            </td>
        </tr>

        <tr>
            <th>
                Sub Kegiatan
            </th>

            <td>
                {{ $submission->judul_sub_kegiatan }}
            </td>
        </tr>

        <tr>
            <th>
                Perihal
            </th>

            <td>
                {{ $submission->perihal }}
            </td>
        </tr>

        <tr>
            <th>
                Total Pencairan
            </th>

            <td>
                Rp {{ number_format($submission->total_pencairan, 0, ',', '.') }}
            </td>
        </tr>

        <tr>

            <th>
                Status Pengajuan
            </th>

            <td>

                @php
                    $status = strtolower($submission->status);
                @endphp

                @switch($status)

                    @case('pending')

                        <span class="badge bg-secondary">
                            Proses Verifikasi
                        </span>

                    @break

                    @case('proses validasi')

                        <span class="badge bg-primary">
                            Proses Validasi
                        </span>

                    @break

                    @case('disetujui')

                        <span class="badge bg-success">
                            Pengajuan Disetujui. (Upload SP2D)
                        </span>

                    @break

                    @case('revisi')

                        <span class="badge bg-warning text-dark">
                            Salah satu dokumen ditolak verifikator
                        </span>

                    @break

                    @case('direvisi')

                        <span class="badge bg-info">
                            Proses verifikasi
                        </span>

                    @break

                    @case('reset')

                        <span class="badge bg-dark">
                            Pengajuan transaksi direset
                        </span>

                    @break

                    @case('expired')

                        <span class="badge bg-danger">
                            Transaksi Kadaluarsa
                        </span>

                    @break

                    @default

                        <span class="badge bg-light text-dark">
                            {{ strtoupper($submission->status) }}
                        </span>

                @endswitch

            </td>

        </tr>

        <tr>

            <th>
                Keterangan Berkas
            </th>

            <td>

                @if($hasSp2d)

                    <strong style="color: #15803d;">
                        SP2D Telah Di-upload — Dokumen Lengkap
                    </strong>

                @else

                    <span style="color: #b91c1c;">
                        Belum Upload SP2D
                    </span>

                @endif

            </td>

        </tr>

    </table>

    <div class="section-title">
        B. Daftar Lampiran Dokumen
    </div>

    <table class="table-doc">

        <thead>

            <tr>

                <th width="5%">
                    No
                </th>

                <th width="45%">
                    Jenis Dokumen
                </th>

                <th width="25%">
                    Status
                </th>

                <th width="25%">
                    Keterangan
                </th>

            </tr>

        </thead>

        <tbody>

            @foreach($submission->documents as $index => $doc)

                <tr>

                    <td style="text-align: center;">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ strtoupper($doc->jenis_dokumen) }}
                    </td>

                    <td style="text-align: center;">
                        {{ strtoupper($doc->status_dokumen) }}
                    </td>

                    <td style="text-align: center;">

                        @if(strtolower($doc->jenis_dokumen) === 'sp2d')

                            <strong>
                                Dokumen Lengkap
                            </strong>

                        @else

                            Arsip Tersedia

                        @endif

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

    <div class="section-title">
        C. SKPD
    </div>

    <table class="table-doc">

        <thead>

            <tr>

                <th width="90%">
                    Pengelola
                </th>

                <th width="10%">
                    Paraf
                </th>

            </tr>

        </thead>

        <tbody>

            <tr>
                <td>
                    PPTK
                </td>

                <td></td>
            </tr>

            <tr>
                <td>
                    BENDAHARA PENGELUARAN
                </td>

                <td></td>
            </tr>

            <tr>
                <td>
                    PPK SKPD
                </td>

                <td></td>
            </tr>

            <tr>
                <td>
                    PENGGUNA ANGGARAN / KUASA PENGGUNA ANGGARAN
                </td>

                <td></td>
            </tr>

        </tbody>

    </table>

    <div class="section-title">
        D. BADAN KEUANGAN DAERAH
    </div>

    <table class="table-doc">

        <thead>

            <tr>

                <th width="90%">
                    Pengelola
                </th>

                <th width="10%">
                    Paraf
                </th>

            </tr>

        </thead>

        <tbody>

            <tr>

                <td>
                    KASIE BELANJA DAERAH
                </td>

                <td></td>

            </tr>

            <tr>

                <td>
                    KASIE BENDAHARAWAN UMUM DAERAH
                </td>

                <td></td>

            </tr>

            <tr>

                <td>
                    KABID PERBENDAHARAAN
                </td>

                <td></td>

            </tr>

        </tbody>

    </table>

</body>
</html>
