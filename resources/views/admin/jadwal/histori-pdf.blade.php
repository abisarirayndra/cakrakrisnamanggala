<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran Siswa - Cakra Krisna Manggala</title>
</head>
<body>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
    .kop { text-align: center; margin-bottom: 16px; }
    .kop img { width: 64px; height: auto; }
    .kop p { margin: 2px 0; }
    .judul { font-size: 16px; font-weight: bold; margin-top: 8px; }
    table.info td { padding: 2px 8px 2px 0; vertical-align: top; }
    table.tabel { border-collapse: collapse; width: 100%; margin-top: 6px; }
    table.tabel th, table.tabel td { border: 1px solid #222; padding: 4px 6px; text-align: left; }
    table.tabel th { background: #f3ead3; }
    h2 { font-size: 13px; margin: 16px 0 6px; }
    .jurnal { border: 1px solid #222; padding: 8px; min-height: 40px; }
    .catatan { margin-top: 18px; font-size: 11px; color: #444; }
</style>
<div class="kop">
    @if ($logo)
        <img src="{{ $logo }}" alt="Cakra Krisna Manggala">
    @endif
    <p class="judul">Cakra Krisna Manggala</p>
    <p>Laporan Kehadiran Siswa</p>
</div>

<table class="info">
    <tr>
        <td>Mapel</td>
        <td><b>{{ $slot->mapel?->mapel }}</b></td>
    </tr>
    <tr>
        <td>Kelas</td>
        <td><b>{{ $slot->kelas?->nama }}</b></td>
    </tr>
    <tr>
        <td>Pendidik</td>
        <td><b>{{ $slot->pendidik?->nama }}</b></td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td><b>{{ $slot->mulai->translatedFormat('l, d F Y') }}</b></td>
    </tr>
    <tr>
        <td>Waktu</td>
        <td><b>{{ $slot->mulai->format('H:i') }} – {{ $slot->selesai->format('H:i') }}</b></td>
    </tr>
</table>

<h2>Jurnal pelajaran</h2>
<div class="jurnal">{{ filled($jurnal) ? $jurnal : 'Belum ada jurnal.' }}</div>

<h2>Kehadiran pendidik</h2>
<table class="tabel">
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Status</th>
        <th>Datang</th>
        <th>Pulang</th>
        <th>Keterangan</th>
    </tr>
    @php $no = 1; @endphp
    @forelse ($datangPendidik->concat($izinPendidik) as $row)
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $row->pendidik?->nama }}</td>
            <td>{{ \App\Support\AbsensiStatus::tampilkan((int) $row->status, $row->datang, $slot->mulai) }}</td>
            <td>{{ $row->datang?->format('H:i') ?: '—' }}</td>
            <td>{{ $row->pulang?->format('H:i') ?: '—' }}</td>
            <td>{{ $row->keterangan ?: '—' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6">Tidak ada data kehadiran pendidik.</td>
        </tr>
    @endforelse
</table>

<h2>Kehadiran siswa</h2>
<table class="tabel">
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Status</th>
        <th>Datang</th>
        <th>Pulang</th>
        <th>Keterangan</th>
    </tr>
    @php $no = 1; @endphp
    @forelse ($datangPelajar->concat($izinPelajar) as $row)
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $row->pelajar?->nama }}</td>
            <td>{{ \App\Support\AbsensiStatus::tampilkan((int) $row->status, $row->datang, $slot->mulai) }}</td>
            <td>{{ $row->datang?->format('H:i') ?: '—' }}</td>
            <td>{{ $row->pulang?->format('H:i') ?: '—' }}</td>
            <td>{{ $row->keterangan ?: '—' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6">Tidak ada data kehadiran siswa.</td>
        </tr>
    @endforelse
</table>

<p class="catatan">
    Dicetak oleh {{ $pencetak }} pada {{ now()->format('d/m/Y H:i') }} untuk pelaporan ke orang tua siswa.
</p>
</body>
</html>
