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
            <h5><i class="fas fa-hashtag text-warning"></i> Daftar Pengguna Pelajar</h5>
            <div class="p-3 mt-3">
                @if ($role == 2)
                    <a href="{{ route('super.penggunapelajar.cetak') }}" class="btn btn-sm btn-warning mb-4" target="_blank"><i class="fas fa-print"></i> Cetak</a>
                    <a href="{{ route('admin.beranda') }}" class="btn btn-sm btn-danger mb-4">Kembali</a>
                @elseif ($role == 7)
                    <a href="{{ route('staf-admin.penggunapelajar.cetak') }}" class="btn btn-sm btn-warning mb-4" target="_blank"><i class="fas fa-print"></i> Cetak</a>
                    <a href="{{ route('staf-admin.beranda') }}" class="btn btn-sm btn-danger mb-4">Kembali</a>
                @endif
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                          <th>No.</th>
                          <th>Nama</th>
                          <th>Nomor ID</th>
                          <th>Kelas</th>
                          <th>Email</th>
                          <th>Aksi</th>
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
                            @if($item->nomor_registrasi == null)
                                <td>Belum Tersedia</td>
                            @else
                                <td>{{ $item->nomor_registrasi }}</td>
                            @endif
                            <td>{{ $item->kelas }}</td>
                            <td>{{ $item->email }}</td>
                            <td>
                              @if ($role == 2)
                              <a href="{{ route('super.penggunapelajar.lihat', [$item->id]) }}" class="btn btn-sm btn-success"><i class="fas fa-eye"></i></a>
                              <a href="{{ route('super.penggunapelajar.edit', [$item->id]) }}" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>
                              <a href="{{ route('super.penggunapelajar.hapus', [$item->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin menghapus akun ini ?')"><i class="fas fa-trash"></i></a>
                              @elseif ($role == 7)
                              <a href="{{ route('staf-admin.penggunapelajar.lihat', [$item->id]) }}" class="btn btn-sm btn-success"><i class="fas fa-eye"></i></a>
                              <a href="{{ route('staf-admin.penggunapelajar.edit', [$item->id]) }}" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>
                              @endif
                              </td>
                          </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $pelajar->links() }}
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
