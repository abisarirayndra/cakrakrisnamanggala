<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Paket CAT - Cakra Krisna Manggala</title>
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
        <th>Rank</th>
        <th>Nama</th>
        <th>Kelas</th>
        <th>Status</th>
        @foreach ($jadwal->banks as $bank)
            <th>{{ $bank->nama }}</th>
        @endforeach
        <th>Total</th>
    </tr>
    @forelse ($baris as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item['nama'] }}</td>
            <td>{{ $item['kelas'] }}</td>
            <td>{{ $item['status'] }}</td>
            @foreach ($item['banks'] as $bank)
                <td>{{ $bank['skor'] ?? '-' }}</td>
            @endforeach
            <td>{{ $item['total'] }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="{{ 5 + $jadwal->banks->count() }}">Belum ada pelajar.</td>
        </tr>
    @endforelse
</table>

<p class="catatan">
    Dicetak oleh {{ $pencetak }} pada {{ now()->format('d/m/Y H:i') }}.
</p>
</body>
</html>
