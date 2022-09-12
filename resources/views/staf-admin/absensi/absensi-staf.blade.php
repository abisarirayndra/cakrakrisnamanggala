@extends('master.staf')

@section('title')
    <title>Cakra Krisna Manggala</title>
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Absensi Kehadiran</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-sm-6">
                    <form action="{{ route('staf-admin.absensi.upload-absensi.staf') }}" method="POST">
                        @csrf
                        <div class="form-group ml-7">
                            <label for="token">Scan Token</label>
                            <input type="text" class="form-control" name="token" autofocus placeholder="Masukkan Token" required autocomplete="off"/>
                            <input type="text" name="datang" value="{{ \Carbon\Carbon::now() }}" readonly hidden>
                            @php
                                $now = \Carbon\Carbon::now();
                                $masuk = \Carbon\Carbon::today()->addHours(13);
                            @endphp
                            @if ($now < $masuk)
                                <input type="text" name="status" value="1" readonly hidden>
                            @elseif($now > $masuk)
                                <input type="text" name="status" value="0" readonly hidden>
                            @endif
                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-warning" type="submit"><i class="fas fa-pen"></i> Absensi</button>
                        </div>
                    </form>
                    </div>
                    <div class="col-sm-6">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                                <h6 class="m-0 font-weight-bold text-white">Izin/Tanpa Keterangan</h6>
                            </div>
                            <div class="card-body">
                                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#tambahdata"><i class="fas fa-pen"></i> Staf</button>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <h5><i class="fas fa-hashtag text-warning"></i> Staf</h5>
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
                                @foreach ($staf as $item)
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
    <div class="modal fade" id="tambahdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-dark" id="exampleModalLabel">Izin Staf</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
                <form action="{{ route('staf-admin.absensi.upload-izin-staf') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Staf</label>
                            <select name="staf_id" id="staf_id" class="form-control">
                                @foreach ($nama_staf as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
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

@endsection
