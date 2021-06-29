@extends('master.master')

@section('title')
    <title>CAT - Kedinasan</title>
    <link href="{{asset('vendor/datatables/datatables.min.css')}}" rel="stylesheet">
@endsection

@section('content')
    <!-- Begin Page Content -->
<div class="container">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Paket Soal Kedinasan</h1>
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
                            <td>
                              <a href="{{route('admin.dinas.editpaket', [$item->id])}}" class="btn btn-sm btn-success"><i class="fas fa-feather-alt"></i> Edit</a>
                              <a href="{{route('admin.dinas.lihatpaket', [$item->id])}}"><button type="button" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i> Lihat</button></a>
                              <a href="{{route('admin.dinas.hapuspaket', [$item->id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin Menghapus ?')"><i class="fas fa-trash"></i> Hapus</a>
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