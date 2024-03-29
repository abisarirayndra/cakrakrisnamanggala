<!DOCTYPE html>
<html>
<head>
	<title>Jurnal Harian - Cakra Krisna Manggala</title>
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
	<style>
        .tabel {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }

        .sel, .sel-head {
          border: 1px solid #000;
          text-align: left;
          padding: 2px;
        }

        </style>
	<center>
		<img src="{!!$logo!!}" width="80" alt="">
                <p>Cakra Krisna Manggala</p>
                <p>Laporan Jurnal Harian</p>
	</center>

    <table>
        <tr>
            <td>Mapel</td>
            <td class="pl-2"><b>{{ $jadwal->mapel }}</b></td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td class="pl-2"><b>{{ $jadwal->kelas }}</b></td>
        </tr>
        <tr>
            <td>Pendidik</td>
            <td class="pl-2"><b>{{ $jadwal->nama }}</b></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="pl-2"><b>{{\Carbon\Carbon::parse($jadwal->mulai)->isoFormat('dddd, D MMMM Y')}}</b></td>
        </tr>
        <tr>
            <td>Waktu</td>
            <td class="pl-2"><b>{{\Carbon\Carbon::parse($jadwal->mulai)->isoFormat('HH:mm')}} - {{ \Carbon\Carbon::parse($jadwal->selesai)->isoFormat('HH:mm') }} </b></td>
        </tr>
    </table>
    <div style="margin-top: 20px">
        <p>Pendidik</p>
    </div>
    <table class="tabel" style="margin-top:5px">
        <tr>
            <th class="sel-head">No</th>
            <th class="sel-head">Nama</th>
            <th class="sel-head">Datang - Pulang</th>
            <th class="sel-head">Status</th>
            <th class="sel-head">Laporan</th>
        </tr>
        @php
        $no = 1;
        @endphp
        @foreach ($pendidik as $item)
        <tr>
            <td class="sel">{{ $no++ }}</td>
            <td class="sel">{{ $item->nama }}</td>
            <td class="sel">
                @if ($item->pulang ==  null)
                    {{\Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm')}} - ????</td>
                @else
                    {{\Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm')}} - {{ \Carbon\Carbon::parse($item->pulang)->isoFormat('HH:mm') }}</td>
                @endif
            <td class="sel">
                @if ($item->status == 0)
                        <p class="text-danger"><i class="fas fa-circle"></i><b> Terlambat</b></p>
                @elseif ($item->status == 1)
                        <p class="text-success"><i class="fas fa-circle"></i><b> Ontime</b></p>
                @endif
            </td>
            <td class="sel">
                @if ($item->jurnal ==  null)
                    <p class="text-danger"><b> Belum Absen Pulang</b></p>
                @else
                    {{ $item->jurnal }}
                @endif
            </td>
        </tr>
        @endforeach
      </table>
      <div style="margin-top: 10px">
        <p>Pelajar</p>
        </div>
      <table class="tabel" style="margin-top:5px">
        <tr>
            <th class="sel-head">No</th>
            <th class="sel-head">Nama</th>
            <th class="sel-head">Datang - Pulang</th>
            <th class="sel-head">Status</th>
        </tr>
        @php
        $no = 1;
        @endphp
        @foreach ($pelajar as $item)
        <tr>
            <td class="sel">{{ $no++ }}</td>
            <td class="sel">{{ $item->nama }}</td>
            <td class="sel">
                @if ($item->pulang ==  null)
                    {{\Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm')}} - ????</td>
                @else
                    {{\Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm')}} - {{ \Carbon\Carbon::parse($item->pulang)->isoFormat('HH:mm') }}</td>
                @endif
            <td class="sel">
                @if ($item->status == 0)
                        <p class="text-danger"><i class="fas fa-circle"></i><b> Terlambat</b></p>
                @elseif ($item->status == 1)
                        <p class="text-success"><i class="fas fa-circle"></i><b> Ontime</b></p>
                @endif
            </td>
        </tr>
        @endforeach
      </table>
      <div style="margin-top: 10px">
        <p>Izin Tidak Masuk</p>
        </div>
      <table class="tabel" style="margin-top:5px">
        <tr>
            <th class="sel-head">No</th>
            <th class="sel-head">Nama</th>
            <th class="sel-head">Role</th>
            <th class="sel-head">Keterangan</th>
        </tr>
        @php
        $no = 1;
        @endphp
        @foreach ($izin_pendidik as $item)
        <tr>
            <td class="sel">{{ $no++ }}</td>
            <td class="sel">{{ $item->nama }}</td>
            <td class="sel">{{ $item->role }}</td>
            <td class="sel">{{ $item->keterangan }}</td>
        </tr>
        @endforeach
        @foreach ($izin_pelajar as $item)
        <tr>
            <td class="sel">{{ $no++ }}</td>
            <td class="sel">{{ $item->nama }}</td>
            <td class="sel">{{ $item->role }}</td>
            <td class="sel">{{ $item->keterangan }}</td>
        </tr>
        @endforeach
      </table>
      <div style="margin-top: 15px">
        <p>Dibuat Oleh : <b>{{ $user }}</b> </p>
        @if ($markas->markas_id == 1)
        <p>Pengawas : <b>Yayan Ferdiyan, M.Pd.</b></p>
        @elseif ($markas->markas_id == 2)
        <p>Pengawas : <b>Yayan Ferdiyan, M.Pd.</b></p>
        @elseif ($markas->markas_id == 3)
        <p>Pengawas : <b>Agung Sedayu, S.Pd.</b></p>
        @endif
      </div>


</body>
</html>


