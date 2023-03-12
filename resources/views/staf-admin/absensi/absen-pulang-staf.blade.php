@extends('master.staf')

@section('title')
    <title>Cakra Krisna Manggala</title>
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Absensi Kepulangan <b>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</b></h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-sm-6">
                    <form action="{{ route('staf-admin.absen.upload-staf-pulang') }}" method="POST">
                        @csrf
                        <div class="form-group ml-7">
                            <label for="token">Scan Token</label>
                            <input type="text" class="form-control" name="token" autofocus placeholder="Masukkan Token" required autocomplete="off"/>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-warning" type="submit"><i class="fas fa-pen"></i> Absensi</button>
                        </div>
                    </form>
                    </div>
                </div>
                <hr>
                <h5><i class="fas fa-hashtag text-warning"></i> Staf</h5>
                    <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                              <th>No.</th>
                              <th>Nama</th>
                              <th>Datang</th>
                              <th>Pulang</th>
                              <th>Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($staf as $item)
                                <tr>
                                <td>{{$no++}}</td>
                                <td>{{$item->nama}}</td>
                                <td>{{ \Carbon\Carbon::parse($item->datang)->isoFormat('HH:mm') }}</td>
                                @if ($item->pulang == null)
                                    <td>Belum Pulang</td>
                                @else
                                    <td>{{ \Carbon\Carbon::parse($item->pulang)->isoFormat('HH:mm') }}</td>
                                @endif
                                <td>
                                    <a href="{{ route('staf-admin.absensi.hapus-izin-staf', [$item->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin menghapus akun ini ?')"><i class="fas fa-trash"></i></a>
                                </td>
                              </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
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
                                    @foreach ($izin_staf as $item)
                                    <tr>
                                        <td>{{$no++}}</td>
                                        <td>{{$item->nama}}</td>
                                        <td>{{ $item->role }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                        <td>
                                            <a href="{{ route('staf-admin.absensi.hapus-izin-staf', [$item->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin menghapus akun ini ?')"><i class="fas fa-trash"></i></a>
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
@endsection

@section('js')

@endsection
