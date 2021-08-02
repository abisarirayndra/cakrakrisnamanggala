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
        <div class="text-right mr-3 mt-3">
            <a href="{{ route('pelajar.dinas.tes', [$prosentase->dn_paket_id]) }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
        </div>
        <div class="card-body">
            <h5><b>Hasil Penilaian</b></h5>
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
                      <i class="fas fa-tasks fa-2x text-primary-300"></i>
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
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Jawaban Benar</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jawab_benar }}</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-check-circle fa-2x text-success-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Nilai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $nilai->nilai }}</div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-poll fa-2x text-warning-300"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Poin Prosentase {{ $prosentase->nilai_pokok }}%</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $nilai->akumulasi }}</div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-dark-300"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
        </div>
    </div>
</div>


@endsection


