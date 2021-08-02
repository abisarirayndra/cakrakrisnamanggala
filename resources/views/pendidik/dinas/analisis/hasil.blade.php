@extends('master.master')

@section('title')
<title>Computer Assisted Test - Cakra Krisna Manggala</title>
    <link href="{{asset('vendor/datatables/datatables.min.css')}}" rel="stylesheet">
@endsection

@section('content')
    <!-- Begin Page Content -->
<div class="container">

    <!-- Page Heading -->
    <div class="text-right mr-3">
        <a href="{{ route('pendidik.dinas.analisis') }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
    </div>
    <p class="mb-4"></p>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h1 class="h3 mb-2 text-gray-800">Hasil Penilaian</h1>
            <div class="p-3">
                <div class="mb-3">
                    <form action="{{ route('pendidik.dinas.hasil') }}" method="GET">
                        <div class="form-group">
                            <label for="kelas"><b>Filter Kelas</b></label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="text" name="arsip" value="{{ $arsip }}" hidden>
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
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <th>Ranking</th>
                          <th>Nama Pelajar</th>
                          <th>Kelas</th>
                          <th>Nilai</th>
                          <th>Akumulasi Bobot</th>
                          <th>Waktu Pengumpulan</th>
                        </tr>
                      </thead>
                      <tbody>
                        @php
                        $no = 1;
                        @endphp
                        @foreach ($nilai as $item)
                        <tr>
                        <td>{{$no++}}</td>
                        <td>{{$item->nama}}</td>
                        <td>{{ $item->kelas }}</td>
                        <td>{{$item->nilai}}</td>
                        <td>{{$item->akumulasi}}</td>
                        <td>{{\Carbon\Carbon::parse($item->updated_at)->isoFormat('dddd, D MMMM Y HH:mm')}}</td>
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
<!-- Page level plugins -->
<script src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('vendor/datatables/datatables.min.js')}}"></script>

<!-- Page level custom scripts -->
<script src="{{asset('js/demo/datatables-demo.js')}}"></script>
@endsection

