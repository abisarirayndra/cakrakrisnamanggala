@extends('master.admin')

@section('title')
    <title>Computer Assisted Test - Cakra Krisna Manggala</title>
    <link href="{{asset('vendor/datatables/datatables.min.css')}}" rel="stylesheet">
@endsection

@section('content')
    <!-- Begin Page Content -->
<div class="container">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Paket Soal</h1>
    <p class="mb-4">Paket-paket yang disiapkan oleh admin untuk persiapan <i>Computer Assisted Test</i>.</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
                <div class="float-right">
                    <a href="{{route('admin.dinas.paket')}}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                </div>
            <h5><i class="fas fa-hashtag text-warning"></i> Paket {{$paket->nama_paket}}</h5>
            <div class="mt-3">
                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#tambahkelas"><i class="fas fa-plus-square"></i> Tambah Kelas</button>
            </div>
            <div class="p-3 mt-3">
                <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                          <th style="max-width: 30px">No.</th>
                          <th>Kelas Yang Mengikuti</th>
                          <th style="max-width: 60px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                            @php
                                $no = 1;
                            @endphp
                        @foreach ($kelas as $item)
                          <tr>
                            <td>{{$no++}}</td>
                            <td>{{$item->nama}}</td>
                            <td>
                              <a href="{{route('admin.dinas.hapuskelas', [$item->id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin Menghapus ?')"><i class="fas fa-trash"></i> Hapus</a>
                            </td>
                          </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <hr>
            <h5 class="mt-4"><i class="fas fa-hashtag text-warning"></i> Daftar Tes {{$paket->nama_paket}}</h5>
            <div class="mt-3">
                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#tambahtes"><i class="fas fa-plus-square"></i> Tambah Tes</button>
            </div>
            <div class="p-3 mt-3">
                <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                          <th style="max-width: 30px">No.</th>
                          <th>Mapel</th>
                          <th style="max-width: 50px">Nilai Pokok/Bobot</th>
                          <th>Mulai</th>
                          <th>Selesai</th>
                          {{-- <th>Durasi (/Menit)</th> --}}
                          <th>Pengajar</th>
                          <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                            @php
                                $no = 1;
                            @endphp
                        @foreach ($tes as $item)
                          <tr>
                            <td>{{$no++}}</td>
                            <td>{{$item->mapel}}</td>
                            <td>{{$item->nilai_pokok}}</td>
                            <td>{{\Carbon\Carbon::parse($item->mulai)->isoFormat('dddd, D MMMM Y HH:mm')}}</td>
                            <td>{{\Carbon\Carbon::parse($item->selesai)->isoFormat('dddd, D MMMM Y HH:mm')}}</td>
                            {{-- <td>{{$item->durasi}}</td> --}}
                            <td>{{$item->nama}}</td>
                            <td>
                              <a href="{{route('admin.dinas.edittes', [$item->id])}}" class="btn btn-sm btn-success"><i class="fas fa-feather-alt"></i> Edit</a>
                              <a href="{{route('admin.dinas.hapustes', [$item->id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin Menghapus ?')"><i class="fas fa-trash"></i> Hapus</a>
                            </td>
                          </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- The Modal -->
    <div class="modal fade" id="tambahkelas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-warning" id="exampleModalLabel">Tambah Kelas</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
                <form action="{{route('admin.dinas.tambahkelas', [$paket->id])}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama">Kelas</label>
                            <select name="kelas_id" class="form-control">
                                @foreach ($list_kelas as $item)
                                    <option value="{{$item->id}}">{{$item->nama}}</option>
                                @endforeach
                            </select>
                            <input type="text" name="nama_paket" hidden value="{{$paket->nama_paket}}">
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
    <div class="modal fade" id="tambahtes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-warning" id="exampleModalLabel">Tambah Tes</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
                <form action="{{route('admin.dinas.tambahtes', [$paket->id])}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama">Mata Pelajaran</label>
                            <select name="mapel_id" class="form-control" required>
                                @foreach ($mapel as $item)
                                    <option value="{{$item->id}}">{{$item->mapel}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nama">Nilai Pokok/Bobot</label>
                            <input type="number" class="form-control" name="nilai_pokok" required>
                        </div>
                        <div class="form-group">
                            <label for="nama">Waktu Mulai</label>
                            <input type="datetime-local" class="form-control" name="mulai" required>
                        </div>
                        <div class="form-group">
                            <label for="nama">Waktu Selesai</label>
                            <input type="datetime-local" class="form-control" name="selesai" required>
                        </div>
                        {{-- <div class="form-group">
                            <label for="nama">Durasi (Dalam Menit)</label>
                            <input type="number" class="form-control" name="durasi" required>
                        </div> --}}
                        <div class="form-group">
                            <label for="nama">Pengajar</label>
                            <select name="pengajar_id" class="form-control" required>
                                @foreach ($pengajar as $item)
                                    <option value="{{$item->id}}">{{$item->nama}}</option>
                                @endforeach
                            </select>
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
        $(document).ready(function() {
            $('.dataTable').DataTable();
        });
    </script>
@endsection
