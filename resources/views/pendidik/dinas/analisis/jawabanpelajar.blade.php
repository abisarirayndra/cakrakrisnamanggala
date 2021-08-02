@extends('master.master')

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
            <h5><b>Analisis Jawaban</b></h5>
        </div>
        <div class="row p-3">
            <div class="col-6">
                <table>
                    <tr>
                       <td><b>Nama Pelajar<b></td>
                       <td class="pl-3">{{ $pelajar->pelajar }}</td>
                    </tr>
                    <tr>
                        <td><b>Kelas<b></td>
                        <td class="pl-3">{{ $pelajar->kelas }}</td>
                    </tr>
                </table>
            </div>
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
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Nilai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $nilai->nilai }}</div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Akumulasi Nilai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $nilai->akumulasi }}</div>
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
                <table>
                    @foreach ($jawaban as $item)
                    <tr>
                        <td style="vertical-align: top;"><b>{{$item->nomor_soal}}</b></td>
                        <td class="pl-4 pb-4">
                            <div style="text-align: justify"><b>{!!$item->soal!!}</b> @if($item->nilai == 0) <div style="color: rgb(209, 52, 52)"><b>(Salah)</b></div> @else <div style="color: rgb(97, 209, 52)"><b>(Benar)</b></div> @endif</div> <br>
                            <div class="form-group">
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" @if($item->jawaban == "A") {{'checked="checked"'}} @endif>
                                  <label class="form-check-label">
                                    @if ( $item->kunci == "A")
                                    <div style="color: rgb(97, 209, 52)">
                                        {!!$item->opsi_a!!}
                                    </div>
                                    @else
                                        {!!$item->opsi_a!!}
                                    @endif
                                  </label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" @if($item->jawaban == "B") {{'checked="checked"'}} @endif>
                                  <label class="form-check-label">
                                    @if ( $item->kunci == "B")
                                    <div style="color: rgb(97, 209, 52)">
                                        {!!$item->opsi_b!!}
                                    </div>
                                    @else
                                        {!!$item->opsi_b!!}
                                    @endif
                                  </label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" @if($item->jawaban == "C") {{'checked="checked"'}} @endif>
                                  <label class="form-check-label">
                                    @if ( $item->kunci == "C")
                                    <div style="color: rgb(97, 209, 52)">
                                        {!!$item->opsi_c!!}
                                    </div>
                                    @else
                                        {!!$item->opsi_c!!}
                                    @endif
                                  </label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" @if($item->jawaban == "D") {{'checked="checked"'}} @endif>
                                  <label class="form-check-label">
                                    @if ( $item->kunci == "D")
                                    <div style="color: rgb(97, 209, 52)">
                                        {!!$item->opsi_d!!}
                                    </div>
                                    @else
                                        {!!$item->opsi_d!!}
                                    @endif
                                  </label>
                                </div>
                                @if ($item->opsi_e == null)

                                @else
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" @if($item->jawaban == "E") {{'checked="checked"'}} @endif>
                                  <label class="form-check-label">
                                    @if ( $item->kunci == "E")
                                    <div style="color: rgb(97, 209, 52)">
                                        {!!$item->opsi_e!!}
                                    </div>
                                    @else
                                        {!!$item->opsi_e!!}
                                    @endif
                                  </label>
                                </div>
                                @endif
                              </div>

                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>

        </div>
    </div>
</div>


@endsection


