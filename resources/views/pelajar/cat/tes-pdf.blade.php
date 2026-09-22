<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tes CAT - Cakra Krisna Manggala</title>
</head>
<body>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
    .kop { text-align: center; margin-bottom: 16px; }
    .kop img { width: 64px; height: auto; }
    .kop p { margin: 2px 0; }
    .judul { font-size: 16px; font-weight: bold; margin-top: 8px; }
    table.info td { padding: 2px 8px 2px 0; vertical-align: top; }
    table.tabel { border-collapse: collapse; width: 100%; margin-top: 10px; }
    table.tabel th, table.tabel td { border: 1px solid #222; padding: 6px 8px; text-align: left; }
    table.tabel th { background: #f3ead3; }
    .total td { font-weight: bold; }
    .catatan { margin-top: 18px; font-size: 11px; color: #444; }
</style>
<div class="kop">
    @if ($logo)
        <img src="{{ $logo }}" alt="Cakra Krisna Manggala">
    @endif
    <p class="judul">Cakra Krisna Manggala</p>
    <p>Laporan Hasil Computer Assisted Test</p>
</div>

<table class="info">
    <tr>
        <td>Nama pelajar</td>
        <td><b>{{ $pelajar->nama }}</b></td>
    </tr>
    <tr>
        <td>Kelas</td>
        <td><b>{{ $pelajar->kelas?->nama ?: '—' }}</b></td>
    </tr>
    <tr>
        <td>Paket</td>
        <td><b>{{ $jadwal->nama }}</b></td>
    </tr>
    <tr>
        <td>Waktu pelaksanaan</td>
        <td><b>{{ $jadwal->labelWaktuPelaksanaan() }}</b></td>
    </tr>
</table>

<table class="tabel">
    <tr>
        <th>Bank soal</th>
        <th>Status</th>
        <th>Benar</th>
        <th>Salah</th>
        <th>Skor</th>
    </tr>
    @foreach ($ringkasan['banks'] as $bank)
        <tr>
            <td>{{ $bank['nama'] }}</td>
            <td>{{ $bank['status'] }}</td>
            <td>{{ $bank['laporan']['benar'] ?? '—' }}</td>
            <td>{{ $bank['laporan']['salah'] ?? '—' }}</td>
            <td>{{ $bank['skor'] ?? '—' }}</td>
        </tr>
    @endforeach
    <tr class="total">
        <td colspan="4">Total</td>
        <td>{{ $ringkasan['total'] }}</td>
    </tr>
</table>

<p class="catatan">
    Dicetak pada {{ now()->format('d/m/Y H:i') }}.
</p>
</body>
</html>
