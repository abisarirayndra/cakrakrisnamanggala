@extends('master.staf')

@section('title')
    <title>Cakra Krisna Manggala</title>
    <link href="{{asset('vendor/datatables/datatables.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Absensi Kehadiran</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                                <h6 class="m-0 font-weight-bold text-white">{{\Carbon\Carbon::parse($jadwal->mulai)->isoFormat('dddd, D MMMM Y')}}</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-s mb-0">Mapel <b>{{ $jadwal->mapel }}</b></p>
                                <p class="text-s mb-0">Kelas <b>{{ $jadwal->kelas }}</b></p>
                                <p class="text-s mb-0">{{ $jadwal->nama }}</p>
                                <p class="text-s mb-0"><b>{{\Carbon\Carbon::parse($jadwal->mulai)->isoFormat('HH:mm')}} - {{ \Carbon\Carbon::parse($jadwal->selesai)->isoFormat('HH:mm') }}</b></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                                <h6 class="m-0 font-weight-bold text-white">Scan Token</h6>
                            </div>
                            <div class="card-body">
                                Waktu :  <b><h7 id="jam"></h7></b>
                                <form action="{{ route('staf-admin.absensi.upload-absensi') }}" method="POST">
                                    @csrf
                                    <div class="form-group ml-7">
                                        <input type="text" class="form-control" name="token" autofocus required placeholder="Masukkan Token" autocomplete="off"/>
                                        <input type="text" name="jadwal_id" value="{{ $jadwal->id }}" readonly hidden>
                                        {{-- <input type="text" name="datang" id="jam"> --}}
                                        @php
                                            $now = \Carbon\Carbon::now();
                                        @endphp
                                        @if ($now < $jadwal->mulai)
                                            <input type="text" name="status" value="1" readonly hidden>
                                        @elseif($now > $jadwal->mulai)
                                            <input type="text" name="status" value="0" readonly hidden>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-warning" type="submit"><i class="fas fa-pen"></i> Absensi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                                <h6 class="m-0 font-weight-bold text-white">Izin/Tanpa Keterangan</h6>
                            </div>
                            <div class="card-body">
                                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#tambahdata"><i class="fas fa-pen"></i> Pelajar</button>
                                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#tambahpendidik"><i class="fas fa-pen"></i> Pendidik</button>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                    <div class="float-right">
                        <button class="btn btn-sm btn-danger"><i class="fas fa-pen"></i> Manual</button>
                    </div>
                    <h5><i class="fas fa-hashtag text-warning"></i> Pendidik</h5>

                        <div class="card mb-4 border-bottom-warning mt-3">
                            <div class="card-body">
                                <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                          <th>No.</th>
                                          <th>Nama</th>
                                          <th>Status</th>
                                          <th>Datang</th>
                                          <th>Pulang</th>
                                          <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            @php
                                                $no = 1;
                                            @endphp
                                            @foreach ($pendidik as $item)
                                            <tr>
                                            <td>{{$no++}}</td>
                                            <td>{{$item->nama}}</td>
                                            @if ($item->status == 0)
                                                <td class="text-danger"><b>Terlambat</b></td>
                                            @elseif ($item->status == 1)
                                                <td class="text-success"><b>Ontime</b></td>
                                            @endif
                                            <td>{{ \Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm') }}</td>
                                            @if ($item->pulang == null)
                                                <td>Belum Pulang</td>
                                            @else
                                                <td>{{ \Carbon\Carbon::parse($item->pulang)->isoFormat('HH:mm') }}</td>
                                            @endif
                                            <td>
                                                <a href="{{ route('staf-admin.absensi.hapus-izin-pendidik', [$item->pendidik_id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin menghapus akun ini ?')"><i class="fas fa-trash"></i></a>
                                            </td>
                                          </tr>
                                            @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                <hr>
                <h5><i class="fas fa-hashtag text-warning"></i> Pelajar</h5>
                        <div class="card mb-4 border-bottom-warning">
                            <div class="card-body">
                                <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                          <th>No.</th>
                                          <th>Nama</th>
                                          <th>Status</th>
                                          <th>Datang</th>
                                          <th>Pulang</th>
                                          <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            @php
                                                $no = 1;
                                            @endphp
                                            @foreach ($pelajar as $item)
                                            <tr>
                                            <td>{{$no++}}</td>
                                            <td>{{$item->nama}}</td>
                                            @if ($item->status == 0)
                                                <td class="text-danger"><b>Terlambat</b></td>
                                            @elseif ($item->status == 1)
                                                <td class="text-success"><b>Ontime</b></td>
                                            @endif
                                            <td>{{ \Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm') }}</td>
                                            @if ($item->pulang == null)
                                                <td>Belum Pulang</td>
                                            @else
                                                <td>{{ \Carbon\Carbon::parse($item->pulang)->isoFormat('HH:mm') }}</td>
                                            @endif
                                            <td>
                                                <a href="{{ route('staf-admin.absensi.hapus-izin-pelajar', [$item->pelajar_id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin menghapus akun ini ?')"><i class="fas fa-trash"></i></a>
                                            </td>
                                          </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr>
                <h5><i class="fas fa-hashtag text-warning"></i> Izin Tidak Masuk</h5>
                <div class="card mb-4 border-bottom-warning">
                    <div class="card-body">
                        <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                  <th>No.</th>
                                  <th>Nama</th>
                                  <th>Role</th>
                                  <th>Keterangan</th>
                                  <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($izin_pendidik as $item)
                                    <tr>
                                        <td>{{$no++}}</td>
                                        <td>{{$item->nama}}</td>
                                        <td>{{ $item->role }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                        <td>
                                            <a href="{{ route('staf-admin.absensi.hapus-izin-pendidik', [$item->pendidik_id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin menghapus akun ini ?')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @foreach ($izin_pelajar as $item)
                                    <tr>
                                        <td>{{$no++}}</td>
                                        <td>{{$item->nama}}</td>
                                        <td>{{ $item->role }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                        <td>
                                            <a href="{{ route('staf-admin.absensi.hapus-izin-pelajar', [$item->pelajar_id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin menghapus akun ini ?')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="tambahdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-dark" id="exampleModalLabel">Tambah Data Pelajar</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
                <form action="{{ route('staf-admin.absensi.upload-izin-pelajar') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pelajar</label>
                            <select name="pelajar_id" id="pelajar_id" class="form-control">
                                @foreach ($nama_pelajar as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="text" name="jadwal_id" value="{{ $jadwal->id }}" readonly hidden>
                        <input type="text" name="status" value="2" readonly hidden>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" id="keterangan" cols="30" rows="10" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Keluar</button>
                        <button type="submit" class="btn btn-warning">Simpan</button>
                    </div>
                </form>
          </div>
        </div>
    </div>
    <div class="modal fade" id="tambahpendidik" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-dark" id="exampleModalLabel">Tambah Data Pendidik</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
                <form action="{{ route('staf-admin.absensi.upload-izin-pendidik') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pelajar</label>
                            <select name="pendidik_id" id="pendidik_id" class="form-control">
                                @foreach ($nama_pendidik as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="text" name="jadwal_id" value="{{ $jadwal->id }}" readonly hidden>
                        <input type="text" name="status" value="2" readonly hidden>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" id="keterangan" cols="30" rows="10" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Keluar</button>
                        <button type="submit" class="btn btn-warning">Simpan</button>
                    </div>
                </form>
          </div>
        </div>
    </div>


</div>
@endsection

@section('js')
<!-- Page level plugins -->
<script src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('vendor/datatables/datatables.min.js')}}"></script>

<!-- Page level custom scripts -->
<script>
    // Call the dataTables jQuery plugin
    $(document).ready(function() {
    $('.dataTable').DataTable();
    });
</script>
<script type="text/javascript">
    window.onload = function() { jam(); }

    function jam() {
     var e = document.getElementById("jam"),
     d = new Date(), h, m, s;
     h = d.getHours();
     m = set(d.getMinutes());
     s = set(d.getSeconds());

     e.innerHTML = h +':'+ m +':'+ s;

     setTimeout('jam()', 1000);


    }

    function set(e) {
     e = e < 10 ? '0'+ e : e;
     return e;
    }
   </script>

@endsection
