<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Analisis Soal CAT - Cakra Krisna Manggala</title>
</head>
<body>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
    .kop { text-align: center; margin-bottom: 14px; }
    .kop img { width: 56px; height: auto; }
    .kop p { margin: 2px 0; }
    .judul { font-size: 15px; font-weight: bold; margin-top: 6px; }
    table.info td { padding: 2px 8px 2px 0; vertical-align: top; }
    table.tabel { border-collapse: collapse; width: 100%; margin-top: 10px; }
    table.tabel th, table.tabel td { border: 1px solid #222; padding: 5px 6px; text-align: left; vertical-align: top; }
    table.tabel th { background: #f3ead3; }
    .catatan-p { margin-top: 18px; font-size: 10px; color: #444; }
</style>
<div class="kop">
    @if ($logo)
        <img src="{{ $logo }}" alt="Cakra Krisna Manggala">
    @endif
    <p class="judul">Cakra Krisna Manggala</p>
    <p>Analisis Soal — Computer Assisted Test</p>
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
    <tr>
        <td>Bank soal</td>
        <td><b>{{ $banks->pluck('nama')->filter()->join(' · ') ?: '—' }}</b></td>
    </tr>
    <tr>
        <td>Peserta / selesai</td>
        <td><b>{{ $ringkasan['peserta'] }} / {{ $ringkasan['selesai'] }}</b></td>
    </tr>
    <tr>
        <td>Soal tersulit</td>
        <td><b>
            @if ($ringkasan['tersulit'] && $ringkasan['tersulit']['persen_benar'] !== null)
                No. {{ $ringkasan['tersulit']['nomor'] }} · {{ $ringkasan['tersulit']['bank'] }} · {{ $ringkasan['tersulit']['persen_benar'] }}% benar
            @else
                —
            @endif
        </b></td>
    </tr>
    <tr>
        <td>Soal termudah</td>
        <td><b>
            @if ($ringkasan['termudah'] && $ringkasan['termudah']['persen_benar'] !== null)
                No. {{ $ringkasan['termudah']['nomor'] }} · {{ $ringkasan['termudah']['bank'] }} · {{ $ringkasan['termudah']['persen_benar'] }}% benar
            @else
                —
            @endif
        </b></td>
    </tr>
</table>

<table class="tabel">
    <tr>
        <th>No</th>
        <th>Bank</th>
        <th>Soal</th>
        <th>Kunci</th>
        <th>Benar</th>
        <th>Salah</th>
        <th>Kosong</th>
        <th>% benar</th>
        <th>Pilihan terbanyak</th>
    </tr>
    @forelse ($soal as $item)
        <tr>
            <td>{{ $item['nomor'] }}</td>
            <td>{{ $item['bank'] }}</td>
            <td>{{ $item['soal'] }}</td>
            <td>{{ $item['kunci'] }}</td>
            <td>{{ $item['benar'] }}</td>
            <td>{{ $item['salah'] }}</td>
            <td>{{ $item['kosong'] }}</td>
            <td>{{ $item['persen_benar'] === null ? '—' : $item['persen_benar'].'%' }}</td>
            <td>{{ $item['pilihan_terbanyak'] ?: '—' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="9">Belum ada soal pada bank ini.</td>
        </tr>
    @endforelse
</table>

<p class="catatan-p">
    Dicetak oleh {{ $pencetak }} pada {{ now()->format('d/m/Y H:i') }}. Persentase dihitung dari sesi yang sudah dikumpulkan.
</p>
</body>
</html>
