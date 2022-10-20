@extends('master.pelajar')

@section('title')
<title>Computer Assisted Test - Cakra Krisna Manggala</title>
@endsection

@section('content')
    <!-- Begin Page Content -->
<div class="container">

    <!-- Page Heading -->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><b>Review Jawaban</b></h5>
        </div>
        <div class="row ml-3 mb-3">
            <h6>Anda yakin untuk mengumpulkan ?</h6>
            <form action="{{ route('pelajar.dinas.kumpulkangandapoin', [$id]) }}" method="POST">
                @csrf
                <input type="text" hidden name="n" value="{{ $total_nilai }}">
                <input type="text" hidden name="p" value="{{ $nilai_pokok->nilai_pokok }}">
                <input type="text" hidden name="s" value="{{ $soal }}">
                <input type="text" hidden name="akm" value="{{ $total_akumulasi }}">
                <button type="submit" class="btn btn-danger ml-3" onclick="return confirm('Anda yakin ingin selesai sekarang ?')"><i class="fas fa-cloud-upload-alt"></i> Selesai dan Lihat Nilai</button>
            </form>
        </div>
        <div class="row p-3">
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jumlah Soal</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $soal }}</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-tasks fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Jumlah Terjawab</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $soal_terjawab }}</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

        </div>
        <div class="row p-3">
            <div class="container">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                      <tr>
                        <th>Nomor Soal</th>
                        <th>Jawaban</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $no = 1;
                      @endphp
                      @foreach ($review as $item)
                      <tr>
                      <td>{{$item->nomor_soal}}</td>
                      @if (isset($item->jawaban))
                      <td>Sudah Terjawab</td>
                      @else
                      <td>Belum Terjawab</td>
                      @endif
                      </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>


@endsection


