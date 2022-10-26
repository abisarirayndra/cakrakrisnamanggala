@extends('master.admin')

@section('title')
    <meta http-equiv="refresh" content="10">
    <title>Computer Assisted Test - Cakra Krisna Manggala</title>
    <link href="{{asset('vendor/datatables/datatables.min.css')}}" rel="stylesheet">
@endsection

@section('content')
    <!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="text-right mr-3">
        <a href="{{ route('admin.dinas.paket') }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
    </div>
    <p class="mb-4"></p>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h1 class="h3 mb-2 text-gray-800">Live Skor Akumulasi Penilaian Kompetensi Dasar</h1>
            <div class="p-3">
                {{-- <div class="row mb-3">
                    <a href="{{ route('admin.dinas.cetakhasildinas', [$id]) }}" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-file-pdf"></i> Unduh PDF</a>
                </div> --}}
                {{-- @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}
                {{-- <div class="row mb-3">
                    @if ($selected == "")
                    <button type="button" class="btn btn-sm btn-warning mr-3" data-toggle="modal" data-target="#arsip">
                        <i class="fas fa-file-archive"></i> Arsipkan
                    </button>
                    @endif
                    <form action="{{ route('pendidik.dinas.cetak_hasil', [$id]) }}" method="GET">
                        <input name="kelas" value="{{ $selected }}" hidden>
                        <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-file-pdf"></i> Unduh PDF</button>
                    </form>
                </div> --}}
                {{-- <div class="mb-3">
                    <form action="{{ route('pendidik.dinas.penilaian', [$id]) }}" method="GET">
                        <div class="form-group">
                            <label for="kelas"><b>Filter Kelas</b></label>
                            <div class="row">
                                <div class="col-6">
                                    <select name="kelas" id="" class="form-control">
                                        <option value="" @if($selected == "") {{'selected="selected"'}} @endif >Semua Kelas</option>
                                        @foreach ($kelas as $item)
                                            <option value="{{ $item->id }}" @if($item->id == $selected) {{'selected="selected"'}} @endif >{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <div style="display: block">
                                        <button class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filter</button>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div> --}}
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <th>Ranking</th>
                          <th>Nama Pelajar</th>
                          <th>Kelas</th>
                          <th>Tes Wawasan Kebangsaan (TWK)</th>
                          <th>Tes Intelegensi Umum (TIU)</th>
                          <th>Tes Karakteristik Pribadi (TKP)</th>
                          <th>Total Akumulasi</th>
                        </tr>
                      </thead>
                      <tbody>
                        @php
                        $no = 1;
                        @endphp
                        @foreach ($hasil as $item)
                        <tr>
                        <td>{{$no++}}</td>
                        <td>{{$item->nama}}</td>
                        <td>{{$item->kelas }}</td>
                        <td>{{$item->twk}}</td>
                        <td>{{$item->tiu}}</td>
                        <td>{{$item->tkp}}</td>
                        <td>{{$item->total_nilai}}</td>
                        </tr>
                          @endforeach
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- <!-- Modal -->
    <div class="modal fade" id="arsip" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Arsipkan Hasil</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form action="{{ route('pendidik.dinas.arsipkan', [$id]) }}" method="POST">
            @csrf
            <div class="modal-body">
                    <div class="form-group">
                    <label class="col-form-label">Kode</label>
                    <input type="text" class="form-control" name="kode" value="{{ $uniqode }}" readonly>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-warning" onclick="return confirm('Anda yakin ingin mengarsipkan sekarang ?')">Arsipkan</button>
            </div>
        </form>
        </div>
    </div>
    </div> --}}
</div>

@endsection

@section('js')
<!-- Page level plugins -->
<script src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('vendor/datatables/datatables.min.js')}}"></script>

<!-- Page level custom scripts -->
<script src="{{asset('js/demo/datatables-demo.js')}}"></script>
<script>
    window.setTimeout( function() {
    window.location.reload();
    }, 10000);
</script>
@endsection

