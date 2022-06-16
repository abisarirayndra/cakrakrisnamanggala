@extends('master.staf')

@section('title')
    <title>Cakra Krisna Manggala</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/css/datepicker.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Jadwal Pembelajaran</h5>
            <div class="p-3 mt-3">
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
                <div class="mt-2">
                    <a href="{{ route('staf-admin.absensi.rekap-pembelajaran.cetak', [$jadwal->id]) }}" class="btn btn-sm btn-warning" target="_blank"><i class="fas fa-print"></i> Cetak Bulan Ini</a>
                </div>
                <div class="row mt-4">
                    <h5><i class="fas fa-hashtag text-warning"></i> Pendidik</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Datang - Pulang</th>
                            <th>Status</th>
                            <th>Laporan</th>
                        </tr>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($pendidik as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $item->nama }}</td>

                            <td>
                                @if ($item->pulang ==  null)
                                    {{\Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm')}} - ????</td>
                                @else
                                    {{\Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm')}} - {{ \Carbon\Carbon::parse($item->pulang)->isoFormat('HH:mm') }}</td>
                                @endif
                            <td>
                                @if ($item->status == 0)
                                        <p class="text-danger"><i class="fas fa-circle"></i><b> Terlambat</b></p>
                                @elseif ($item->status == 1)
                                        <p class="text-success"><i class="fas fa-circle"></i><b> Ontime</b></p>
                                @endif
                            </td>
                            <td>
                                @if ($item->jurnal ==  null)
                                    <p class="text-danger"><b> Belum Absen Pulang</b></p>
                                @else
                                    {{ $item->jurnal }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
                <div class="row mt-4">
                    <h5><i class="fas fa-hashtag text-warning"></i> Pelajar</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Datang - Pulang</th>
                            <th>Status</th>
                        </tr>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($pelajar as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>
                                @if ($item->pulang ==  null)
                                    {{\Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm')}} - ????</td>
                                @else
                                    {{\Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm')}} - {{ \Carbon\Carbon::parse($item->pulang)->isoFormat('HH:mm') }}</td>
                                @endif
                            <td>
                                @if ($item->status == 0)
                                        <p class="text-danger"><i class="fas fa-circle"></i><b> Terlambat</b></p>
                                @elseif ($item->status == 1)
                                        <p class="text-success"><i class="fas fa-circle"></i><b> Ontime</b></p>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

@endsection
