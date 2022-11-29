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
            <h5><i class="fas fa-hashtag text-warning"></i> Daftar Paket Soal</h5>
            <div class="mt-3">
                <a href="{{route('admin.dinas.tambahpaket')}}" class="btn btn-sm btn-warning"><i class="fas fa-plus-square"></i> Tambah</a>
            </div>
            <div class="p-3 mt-3">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                          <th style="max-width: 20px">No.</th>
                          <th>Paket Soal</th>
                          <th style="max-width: 60px">Status</th>
                          <th>Kategori</th>
                          <th style="max-width: 120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($paket as $item)
                            <tr>
                            <td>{{$no++}}</td>
                            <td>{{$item->nama_paket}}</td>
                            @if ($item->status == 1)
                            <td>Aktif</td>
                            @else
                            <td> Tidak Aktif</td>
                            @endif
                            <td>{{ $item->kategori }}</td>
                            <td>
                              <a href="{{route('admin.dinas.editpaket', [$item->id])}}" class="btn btn-sm btn-success"><i class="fas fa-feather-alt"></i> </a>
                              <a href="{{route('admin.dinas.lihatpaket', [$item->id])}}"><button type="button" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i> </button></a>
                              @if ($item->kategori == "Kedinasan")
                              <a href="{{ route('admin.dinas.live_hasildinas', [$item->id]) }}" class="btn btn-sm btn-warning" target="_blank"><i class="fa fa-play" aria-hidden="true"></i></a>
                              <a href="{{ route('admin.dinas.hasildinas', [$item->id]) }}" class="btn btn-sm btn-warning" target="_blank"><i class="fas fa-list-alt"></i></a>
                              @elseif ($item->kategori == "TNI/Polri")
                              <a href="{{ route('admin.dinas.live_hasiltnipolri', [$item->id]) }}" class="btn btn-sm btn-warning"><i class="fa fa-play" aria-hidden="true"></i></a>
                              <a href="{{ route('admin.dinas.hasiltnipolri', [$item->id]) }}" class="btn btn-sm btn-warning"><i class="fas fa-list-alt"></i></a>
                              @elseif ($item->kategori == "Psikotes")
                              <a href="{{ route('admin.dinas.live_hasilpsikotes', [$item->id]) }}" class="btn btn-sm btn-warning"><i class="fa fa-play" aria-hidden="true"></i></a>
                              <a href="{{ route('admin.dinas.hasil_psikotes', [$item->id]) }}" class="btn btn-sm btn-warning"><i class="fas fa-list-alt"></i></a>
                              @endif
                              <a href="{{route('admin.dinas.hapuspaket', [$item->id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin Menghapus ?')"><i class="fas fa-trash"></i></a>
                              </td>
                          </tr>
                        @endforeach
                    </tbody>
                </table>
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
    <script src="{{asset('js/demo/datatables-demo.js')}}"></script>
@endsection
