@extends('master.super')

@section('title')
<link href="{{asset('vendor/datatables/datatables.min.css')}}" rel="stylesheet">

@endsection

@section('content')
    <!-- Begin Page Content -->
<div class="container">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Daftar Pengguna Pendaftar</h5>
            <div class="p-3 mt-3">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama</th>
                          <th>Email</th>
                          <th>Tanggal Daftar</th>
                          <th style="max-width: 120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($pendaftar as $item)
                            <tr>
                            <td>{{$no++}}</td>
                            <td>{{$item->nama}}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->updated_at)->isoFormat('dddd, D MMMM YYYY HH:mm') }}</td>
                            <td>
                              <a href="{{ route('super.penggunapendaftar.lihat', [$item->id]) }}" class="btn btn-sm btn-success"><i class="fas fa-eye"></i></a>
                              <a href="{{ route('super.penggunapendaftar.hapus', [$item->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin menghapus akun ini ?')"><i class="fas fa-trash"></i></a>
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
