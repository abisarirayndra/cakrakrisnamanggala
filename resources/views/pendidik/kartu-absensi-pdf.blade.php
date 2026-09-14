<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            width: 86mm;
            height: 54mm;
            font-family: DejaVu Sans, sans-serif;
            color: #1B2430;
        }
        .card {
            width: 86mm;
            height: 54mm;
            border: 0.35mm solid #E4DDD2;
            overflow: hidden;
        }
        .head {
            background: #243044;
            color: #f7f1e6;
            padding: 3mm 4mm;
        }
        .brand { font-size: 8pt; font-weight: 700; margin: 0; }
        .sub { font-size: 6.5pt; margin: 0.6mm 0 0; opacity: 0.8; }
        .body { padding: 3mm 4mm 0; }
        table { width: 100%; border-collapse: collapse; }
        .photo {
            width: 16mm;
            height: 21mm;
            background: #F3EAD6;
            border: 0.2mm solid #E4DDD2;
        }
        .label { font-size: 5.5pt; color: #5E6B7A; text-transform: uppercase; letter-spacing: 0.04em; margin: 0 0 0.4mm; }
        .value { font-size: 8pt; font-weight: 700; margin: 0 0 2mm; }
        .qr { width: 20mm; height: 20mm; }
        .hint { font-size: 5pt; color: #5E6B7A; margin: 1mm 0 0; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <div class="head">
            <p class="brand">Cakra Krisna Manggala</p>
            <p class="sub">Kartu Absensi Pendidik</p>
        </div>
        <div class="body">
            <table>
                <tr>
                    <td style="width: 18mm; vertical-align: top;">
                        @if ($foto)
                            <img class="photo" src="{{ $foto }}" alt="">
                        @else
                            <div class="photo"></div>
                        @endif
                    </td>
                    <td style="vertical-align: top; padding: 0 3mm;">
                        <p class="label">Nama</p>
                        <p class="value">{{ $nama }}</p>
                        <p class="label">Mata pelajaran</p>
                        <p class="value">{{ $mapelNama ?: '—' }}</p>
                        <p class="label">Nomor registrasi</p>
                        <p class="value">{{ $nomor_registrasi }}</p>
                    </td>
                    <td style="width: 22mm; vertical-align: top; text-align: center;">
                        <img class="qr" src="{{ $qr }}" alt="">
                        <p class="hint">Tunjukkan ke scanner</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
