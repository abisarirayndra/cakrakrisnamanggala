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
            @if ($tes_selanjutnya == "Selesai" || $kategori->kategori == "TNI/Polri")
            <div class="text-right mr-3 mt-3">
                <a href="{{ route('pelajar.dinas.beranda') }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
            </div>
            @endif
            {{-- <a href="{{ route('pelajar.dinas.tes', [$prosentase->dn_paket_id]) }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a> --}}
        @if ($kategori->kategori == "Kedinasan")
        <div class="p-3 mt-2">
            @if ($tes_selanjutnya == "Selesai")
                <div class="text-center">
                    <h3>Tes Kompetensi Dasar Selesai</h3>
                </div>
            @else
            <h5><i class="fas fa-hashtag text-warning"></i> Tes Selanjutnya</h5>
            <div class="mt-3">
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class=" font-weight-bold text-dark text-uppercase mb-1">
                                        {{ $tes_selanjutnya->mapel }}</div>
                                        <div class="font-weight-bold mb-1" style="font-size: 10pt;">
                                            <a href="{{ route('pelajar.dinas.persiapan', [$tes_selanjutnya->id]) }}" class="btn btn-sm btn-warning mt-3"><i class="fas fa-folder-open"></i> Buka</a>
                                        </div>
                                    </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
        <div class="card-body">
            <h5><b>Hasil Penilaian</b></h5>
        </div>
        <div class="row p-3">
            <div class="col-6">
                <table>
                    <tr>
                       <td><b>Nama Pelajar<b></td>
                       <td class="pl-3">{{ $pelajar->nama }}</td>
                    </tr>
                    <tr>
                        <td><b>Kelas<b></td>
                        <td class="pl-3">{{ $kelas->nama }}</td>
                    </tr>
                    <tr>
                        <td><b>Mapel<b></td>
                        <td class="pl-3">{{ $mapel->mapel }}</td>
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
                      <i class="fas fa-tasks fa-2x text-primary-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            @if($jenis == 1)
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
            @endif
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
        @if ($kategori->kategori == "Kedinasan")
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
              <div class="card-body">
                <div class="row no-gutters align-items-center">
                  <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Total Nilai SKD</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $nilai->akumulasi }}</div>
                  </div>
                  <div class="col-auto">
                    <i class="fas fa-percentage fa-2x text-dark-300"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @else
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
        @endif
        </div>

    </div>
</div>


@endsection


