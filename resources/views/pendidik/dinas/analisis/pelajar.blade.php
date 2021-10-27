@extends('master.master')

@section('title')
<title>Computer Assisted Test - Cakra Krisna Manggala</title>
    <link href="{{asset('vendor/datatables/datatables.min.css')}}" rel="stylesheet">
@endsection

@section('content')
    <!-- Begin Page Content -->
<div class="container">

    <!-- Page Heading -->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><b>Daftar pelajar yang mengikuti tes</b></h5>
        </div>
        <div class="row p-3">
            <div class="container">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                      <tr>
                          <th style="max-width: 20px">No.</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $no = 1;
                      @endphp
                      @foreach ($pelajar as $item)
                      <tr>
                        <td>{{ $no++ }}</td>
                      <td>{{$item->nama}}</td>
                      <td>{{ $item->kelas }}</td>
                      <td>
                            <form action="{{ route('pendidik.dinas.jawabanpelajar') }}">
                                <input type="text" name="token" hidden value="{{ $item->status }}">
                                <input type="text" name="tes" hidden value="{{ $item->dn_tes_id }}">
                                <input type="text" name="auth" hidden value="{{ $item->id }}">
                                <button type="submit" class="btn btn-sm btn-warning"><i class="fas fa-list-ol"></i> Jawaban Pelajar</button>
                            </form>
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

