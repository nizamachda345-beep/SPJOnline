<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Arsip Dokumen</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .footer { margin-top: 30px; text-align: right; }
        .nominal { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">LAPORAN ARSIP DOKUMEN JUARA</h2>
        <p style="margin:5px 0;">Tanggal Cetak: {{ date('d/m/Y H:i') }} WITA</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
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
                <td align="center">{{ $index + 1 }}</td>
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