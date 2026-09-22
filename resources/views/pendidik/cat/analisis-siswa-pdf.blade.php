<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Analisis Siswa CAT - Cakra Krisna Manggala</title>
</head>
<body>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
    .lembar { page-break-after: always; }
    .lembar:last-child { page-break-after: auto; }
    .kop { text-align: center; margin-bottom: 16px; }
    .kop img { width: 64px; height: auto; }
    .kop p { margin: 2px 0; }
    .judul { font-size: 16px; font-weight: bold; margin-top: 8px; }
    table.info td { padding: 2px 8px 2px 0; vertical-align: top; }
    table.tabel { border-collapse: collapse; width: 100%; margin-top: 12px; }
    table.tabel th, table.tabel td { border: 1px solid #222; padding: 6px 8px; text-align: left; }
    table.tabel th { background: #f3ead3; }
    .catatan-judul { font-weight: bold; margin: 16px 0 6px; }
    .catatan { border: 1px solid #222; padding: 8px; min-height: 48px; }
    .catatan-p { margin-top: 18px; font-size: 11px; color: #444; }
</style>

@forelse ($siswa as $item)
    <section class="lembar">
        <div class="kop">
            @if ($logo)
                <img src="{{ $logo }}" alt="Cakra Krisna Manggala">
            @endif
            <p class="judul">Cakra Krisna Manggala</p>
            <p>Analisis Hasil Siswa — Computer Assisted Test</p>
        </div>

        <table class="info">
            <tr>
                <td>Nama siswa</td>
                <td><b>{{ $item['nama'] }}</b></td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td><b>{{ $item['kelas'] }}</b></td>
            </tr>
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
        </table>

        <table class="tabel">
            <tr>
                <th>Rank</th>
                <th>Benar</th>
                <th>Salah</th>
                <th>Total skor</th>
            </tr>
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['benar'] }}</td>
                <td>{{ $item['salah'] }}</td>
                <td>{{ $item['skor'] }}</td>
            </tr>
        </table>

        <p class="catatan-judul">Catatan analisis pendidik</p>
        <div class="catatan">{{ filled($item['catatan']) ? $item['catatan'] : 'Belum ada catatan.' }}</div>

        <p class="catatan-p">
            Dicetak oleh {{ $pencetak }} pada {{ now()->format('d/m/Y H:i') }}.
        </p>
    </section>
@empty
    <section class="lembar">
        <div class="kop">
            @if ($logo)
                <img src="{{ $logo }}" alt="Cakra Krisna Manggala">
            @endif
            <p class="judul">Cakra Krisna Manggala</p>
            <p>Analisis Hasil Siswa — Computer Assisted Test</p>
        </div>
        <p>Belum ada siswa mengerjakan bank soal ini.</p>
    </section>
@endforelse
</body>
</html>
