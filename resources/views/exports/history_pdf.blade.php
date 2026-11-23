{{-- View: exports/history_pdf --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Arsip Dokumen</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 16mm 14mm 22mm;
        }

        body {
            color: #202833;
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
        }

        .branding {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 12px;
        }

        .branding td {
            padding: 0;
            vertical-align: middle;
        }

        .branding-logo {
            width: 58px;
            padding-right: 12px !important;
            text-align: left;
        }

        .branding-logo img {
            display: block;
            width: 42px;
            height: 50px;
        }

        .government-name {
            margin: 0 0 3px;
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: .2px;
        }

        .agency-name {
            margin: 0;
            font-size: 9pt;
            font-weight: bold;
        }

        .report-title {
            margin: 13px 0 4px;
            font-size: 14pt;
            text-align: center;
        }

        .print-date {
            margin: 0 0 14px;
            color: #4b5563;
            font-size: 8.5pt;
            text-align: center;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 8px;
            font-size: 9pt;
        }

        .history-table thead {
            display: table-header-group;
        }

        .history-table th,
        .history-table td {
            border: 1px solid #8993a0;
            padding: 8px 6px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .history-table th {
            background: #e9edf2;
            color: #202833;
            font-weight: bold;
            line-height: 1.25;
            text-align: center;
        }

        .history-table tbody tr {
            page-break-inside: avoid;
        }

        .center {
            text-align: center;
        }

        .nominal {
            text-align: right;
            white-space: nowrap;
        }

        .footer {
            margin-top: 24px;
            padding-top: 7px;
            border-top: 1px solid #c4cbd3;
            color: #4b5563;
            font-size: 8pt;
            text-align: right;
        }
    </style>
</head>
<body>
    @php
        $documentLogoSetting = $systemSettings['branding.document_logo'] ?? '';
        $documentLogoDisk = \Illuminate\Support\Facades\Storage::disk('public');
        $documentLogoPath = filled($documentLogoSetting) && $documentLogoDisk->exists($documentLogoSetting)
            ? $documentLogoDisk->path($documentLogoSetting)
            : (file_exists(public_path('logo_bolmong.png')) ? public_path('logo_bolmong.png') : null);
    @endphp
    <table class="branding">
        <tr>
            <td class="branding-logo">
                @if($documentLogoPath)
                    <img src="{{ $documentLogoPath }}" alt="Logo instansi">
                @endif
            </td>
            <td>
                <p class="government-name">{{ $systemSettings['branding.government_name'] ?? 'PEMERINTAH KABUPATEN BOLAANG MONGONDOW' }}</p>
                <p class="agency-name">{{ $systemSettings['branding.agency_name'] ?? 'BADAN KEUANGAN DAERAH' }}</p>
            </td>
        </tr>
    </table>

    <h2 class="report-title">LAPORAN ARSIP DOKUMEN JUARA</h2>
    <p class="print-date">Tanggal Cetak: {{ date('d/m/Y H:i') }} WITA</p>

    <table class="history-table">
        <colgroup>
            <col style="width:4%;">
            <col style="width:15%;">
            <col style="width:11%;">
            <col style="width:17%;">
            <col style="width:17%;">
            <col style="width:15%;">
            <col style="width:10%;">
            <col style="width:11%;">
        </colgroup>
        <thead>
            <tr>
                <th>No</th>
                <th>SKPD / Dinas</th>
                <th>No. SPP</th>
                <th>Kegiatan</th>
                <th>Sub Kegiatan</th>
                <th>Perihal</th>
                <th>Tanggal Pengesahan</th>
                <th>Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($histories as $index => $h)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $h->department->nama_dinas }}</td>
                <td>{{ $h->nomor_spp_spm }}</td>
                <td>{{ $h->judul_kegiatan }}</td>
                <td>{{ $h->judul_sub_kegiatan ?? '-' }}</td>
                <td>{{ $h->perihal ?? '-' }}</td>
                <td>{{ $h->updated_at->format('d/m/Y') }}</td>
                <td class="nominal">{{ number_format($h->total_pencairan, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak secara sistem oleh: {{ auth()->user()->name }}</p>
    </div>
</body>
</html>
