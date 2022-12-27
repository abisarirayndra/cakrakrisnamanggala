@extends('master.master ')

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
                    {{-- <div class="col-sm-8">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                                <h6 class="m-0 font-weight-bold text-white">Coach</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <form action="{{ route('pendidik.absensi.upload-absensi') }}" method="POST" class="qr-code">
                                            @csrf
                                            <div class="form-group ml-7">
                                                <tr>
                                                    <td>
                                                        <select name="tambah[0][pendidik_id]" autofocus class="form-control">
                                                            @foreach ($nama_pendidik as $item)
                                                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <button name="add" id="add" class="btn btn-sm btn-success">Tambah</button>
                                                        <input type="text" name="jadwal_id" value="{{ $jadwal->id }}" readonly hidden>
                                                    </td>
                                                </tr>
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-sm btn-success" type="submit"><i class="fas fa-pen"></i> Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div> --}}
                    {{-- <div class="col-sm-4">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                                <h6 class="m-0 font-weight-bold text-white">Izin/Tanpa Keterangan</h6>
                            </div>
                            <div class="card-body">
                                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#tambahdata"><i class="fas fa-pen"></i> Pelajar</button>
                                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#tambahpendidik"><i class="fas fa-pen"></i> Pendidik</button>
                            </div>
                        </div>
                    </div> --}}
                </div>

                <hr>
                @if (session()->has('success'))
                    <div class="alert alert-success">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        <p>{{ session()->get('success') }}</p>
                    </div>
                @endif
                <h5><i class="fas fa-hashtag text-warning"></i> Absensi Coach</h5>
                <form action="{{ route('pendidik.absensi.upload_absensi_jasmani') }}" method="POST">
                    @csrf
                    <table class="table table-bordered" id="dynamicTable" width="100%" cellspacing="0" >
                        <thead>
                            <tr>
                                <th style="10px">No</th>
                            <th>Nama</th>
                            <th>Jurnal</th>
                            <th>Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                                @php
                                    $a=1;
                                @endphp
                                @foreach ($pendidik as $item)
                                <tr>
                                    <td>{{ $a++}}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->jurnal }}</td>
                                    <td><a href="{{ route('pendidik.absensi.hapus-izin-pendidik', [$item->pendidik_id]) }}" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td></td>
                                    <td>
                                        <select name="tambah[0][pendidik_id]" autofocus class="form-control">
                                            @foreach ($nama_pendidik as $item)
                                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="tambah[0][jadwal_id]" value="{{ $jadwal->id }}" readonly hidden>
                                        <input type="datetime" name="tambah[0][datang]" value="{{ $jadwal->mulai }}" readonly hidden>
                                        <input type="datetime" name="tambah[0][pulang]" value="{{ $jadwal->selesai }}" readonly hidden>
                                        <input type="text" name="tambah[0][status]" value="3" readonly hidden>

                                    </td>
                                    <td>
                                        <input type="text" name="tambah[0][jurnal]" required class="form-control" Placeholder="Ngajar apa hari ini ?">
                                    </td>
                                    <td>
                                        <button type="button" name="add" id="add" class="btn btn-sm btn-success"><i class="fas fa-plus"></i></button>
                                    </td>
                                </tr>

                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-success">Simpan Pendidik</button>
                    </form>
                <hr>
                @if (session()->has('success'))
                    <div class="alert alert-success">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        <p>{{ session()->get('success-pelajar') }}</p>
                    </div>
                @endif
                <h5><i class="fas fa-hashtag text-warning"></i> Absensi Pelajar</h5>
                <form action="{{ route('pendidik.absensi.upload_absensi_jasmani.pelajar') }}" method="POST">
                    @csrf
                    <table class="table table-bordered" id="dynamicTable2" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                            <th style="width: 20px">No.</th>
                            <th>Nama</th>
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
                                    <td>
                                        <a href="{{ route('pendidik.absensi.hapus-izin-pelajar', [$item->pelajar_id]) }}" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td></td>
                                    <td>
                                        <select name="tambah2[0][pelajar_id]" autofocus class="form-control">
                                            @foreach ($nama_pelajar as $item)
                                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="tambah2[0][jadwal_id]" value="{{ $jadwal->id }}" readonly hidden>
                                        <input type="datetime" name="tambah2[0][datang]" value="{{ $jadwal->mulai }}" readonly hidden>
                                        <input type="datetime" name="tambah2[0][pulang]" value="{{ $jadwal->selesai }}" readonly hidden>
                                        <input type="text" name="tambah2[0][status]" value="3" readonly hidden>

                                    </td>
                                    <td>
                                        <button type="button" name="add2" id="add2" class="btn btn-sm btn-success"><i class="fas fa-plus"></i></button>
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-success">Simpan Pelajar</button>
                </form>
                <div class="mb-3 mt-4">

                    <h5><i class="fas fa-hashtag text-warning"></i> Izin Tidak Masuk</h5>
                    <div class="mt-2">
                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#tambahdata"><i class="fas fa-pen"></i> Pelajar</button>
                            <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#tambahpendidik"><i class="fas fa-pen"></i> Pendidik</button>
                    </div>
                </div>
                    <div><b>Pelajar</b></div>
                    @foreach ($izin_pelajar as $item)
                    <tr>
                        <td>
                            {{ $item->nama }}
                        </td>
                        <td>
                            : {{ $item->keterangan }}
                        </td>
                        <td>
                            <a href="{{ route('pendidik.absensi.hapus-izin-pelajar', [$item->pelajar_id]) }}" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    @endforeach
                    <div class="mt-3"><b>Pendidik</b></div>
                        @foreach ($izin_pendidik as $item)
                        <tr>
                            <td>
                                {{ $item->nama }}
                            </td>
                            <td>
                                : {{ $item->keterangan }}
                            </td>
                            <td>
                                <a href="{{ route('pendidik.absensi.hapus-izin-pendidik', [$item->pendidik_id]) }}" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach


            </div>
        </div>
    </div>
    <div class="modal fade" id="tambahdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-dark" id="exampleModalLabel">Tambah Data Pelajar</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
                <form action="{{ route('pendidik.absensi.upload-izin-pelajar') }}" method="POST">
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
        <div class="modal-dialog modal-sm" role="document">
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
    $('.dataTable').DataTable({
        responsive: true
    });
    });
</script>
<script type="text/javascript">

    var i = 0;

    $("#add").click(function(){

        ++i;

        $("#dynamicTable").append('<tr><td><select name="tambah['+i+'][pendidik_id]" autofocus class="form-control"> @foreach ($nama_pendidik as $item)<option value="{{ $item->id }}">{{ $item->nama }}</option>@endforeach </select> <input type="text" name="tambah['+i+'][jadwal_id]" value="{{ $jadwal->id }}" readonly hidden><input type="text" name="tambah['+i+'][datang]" value="{{ $jadwal->mulai }}" readonly hidden><input type="text" name="tambah['+i+'][pulang]" value="{{ $jadwal->selesai }}" readonly hidden><input type="text" name="tambah['+i+'][status]" value="3" readonly hidden></td><td><input type="text" name="tambah['+i+'][jurnal]" required class="form-control" Placeholder="Ngajar apa hari ini ?"></td><td><button type="button" class="btn btn-danger btn-sm remove-tr"><i class="fas fa-trash"></i></button></td></tr>');
    });

    $(document).on('click', '.remove-tr', function(){
         $(this).parents('tr').remove();
    });

</script>
<script type="text/javascript">

    var i = 0;

    $("#add2").click(function(){

        ++i;

        $("#dynamicTable2").append('<tr><td></td><td><select name="tambah2['+i+'][pelajar_id]" autofocus class="form-control"> @foreach ($nama_pelajar as $item)<option value="{{ $item->id }}">{{ $item->nama }}</option>@endforeach </select> <input type="text" name="tambah2['+i+'][jadwal_id]" value="{{ $jadwal->id }}" readonly hidden><input type="text" name="tambah2['+i+'][datang]" value="{{ $jadwal->mulai }}" readonly hidden><input type="text" name="tambah2['+i+'][pulang]" value="{{ $jadwal->selesai }}" readonly hidden><input type="text" name="tambah2['+i+'][status]" value="3" readonly hidden></td><td><button type="button" class="btn btn-danger btn-sm remove-pelajar"><i class="fas fa-trash"></i></button></td></tr>');
    });

    $(document).on('click', '.remove-pelajar', function(){
         $(this).parents('tr').remove();
    });

</script>

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script type="text/javascript">
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
        scanner.addListener('scan', function (content) {
            $('input[name="token"]').val(content); // Pass the scanned content value to an input field
			$('.qr-code').submit();
			scanner.stop()
        });
        Instascan.Camera.getCameras().then(function (cameras) {
          if (cameras.length > 0) {
            scanner.start(cameras[0]);
          } else {
            console.error('No cameras found.');
          }
        }).catch(function (e) {
          console.error(e);
        });
      </script> --}}
@endsection
