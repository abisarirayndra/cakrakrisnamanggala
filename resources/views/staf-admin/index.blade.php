@extends('master.staf')

@section('title')
    <title>Computer Assisted Test - Cakra Krisna Manggala</title>
    <style>
        td{
            font-size: 80%
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Data Diri</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-xl-4 col-sm-4 text-center pb-4">
                        <img src="{{ asset('/pendidik/img/'. $data->foto) }}" width="120" alt="Belum ada foto">
                    </div>
                    <div class="col-xl-8 col-sm-8">
                            <table>
                                <tr>
                                    <td><b>Nama</b></td>
                                    <td class="pl-4">{{$user}}</td>
                                </tr>
                                <tr>
                                    <td><b>NIK</b></td>
                                    @if ($data->nik == null)
                                        <td class="pl-4">--Belum Tersedia--</td>
                                    @else
                                        <td class="pl-4">{{$data->nik}}</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td><b>NIP</b></td>
                                    @if ($data->nip == null)
                                        <td class="pl-4">--Belum Tersedia--</td>
                                    @else
                                        <td class="pl-4">{{$data->nip}}</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td><b>Tempat, Tanggal Lahir</b></td>
                                    <td class="pl-4">{{$data->tempat_lahir}}, {{\Carbon\Carbon::parse($data->tanggal_lahir)->isoFormat('D MMMM Y')}}</td>
                                </tr>
                                <tr>
                                    <td><b>Alamat</b></td>
                                    <td class="pl-4">{{$data->alamat}}</td>
                                </tr>
                                <tr>
                                    <td><b>Mata Pelajaran</b></td>
                                    <td class="pl-4">{{$data->mapel}}</td>
                                </tr>
                                <tr>
                                    <td><b>No. Telpon/WhatsApp</b></td>
                                    <td class="pl-4">{{$data->wa}}</td>
                                </tr>
                                <tr>
                                    <td><b>Nama Ibu Kandung</b></td>
                                    @if ($data->ibu == null)
                                        <td class="pl-4">--Belum Tersedia--</td>
                                    @else
                                        <td class="pl-4">{{$data->ibu}}</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td><b>Status Dapodik</b></td>
                                    <td class="pl-4">{{$data->status_dapodik}}</td>
                                </tr>
                                <tr>
                                    <td><b>Tanggal Daftar</b></td>
                                    <td class="pl-4">{{\Carbon\Carbon::parse($data->created_at)->isoFormat('dddd, D MMMM Y HH:mm')}}</td>
                                </tr>
                            </table>
                                {{-- <a href="" class="btn btn-sm btn-success mt-4"><i class="fas fa-cloud-download-alt"></i> Unduh CV</a> --}}
                                <button class="btn btn-warning btn-sm mt-4 ml-3" data-toggle="modal" data-target="#edit"><i class="fas fa-edit"></i> Edit</button>
                                <input id="text" value="{{ $token }}" hidden/>
                                <button id="btn-qrcode" class="btn btn-success btn-sm mt-4 ml-3" data-toggle="modal" data-target="#qrcode-modal"><i class="fas fa-qrcode"></i> QR-code</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Menu</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-xl-3 col-md-3 text-center">
                        <form action="{{ route('staf-admin.jadwal') }}" method="GET">
                            @if ($data->markas_id == 1)
                                <input type="text" value="1" name="kelas" hidden>
                            @elseif ($data->markas_id == 2)
                                <input type="text" value="4" name="kelas" hidden>
                            @elseif ($data->markas_id == 3)
                                <input type="text" value="9" name="kelas" hidden>
                            @endif
                            <input type="text" value="{{ $sekarang->format('m') }}" hidden name="bulan">
                            <input type="text" value="{{ $sekarang->format('Y') }}" hidden name="tahun">
                            <button type="submit" style="background-color: transparent; border: 0px">
                                <span class="fa-stack fa-3x">
                                    <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                    <i class="fas fa-calendar fa-stack-1x fa-inverse"></i>
                                </span>
                                    <h6 class="my-3 text-dark">Jadwal</h6>
                            </button>
                        </form>

                    </div>
                    <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('staf-admin.absensi.beranda') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-qrcode fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Absensi Pembelajaran</h6>
                        </a>
                    </div>
                    <div class="col-xl-3 col-md-3 text-center">
                        <form action="{{ route('staf-admin.absensi.rekap-pembelajaran') }}" method="GET">
                            @if ($data->markas_id == 1)
                                <input type="text" value="1" name="kelas" hidden>
                            @elseif ($data->markas_id == 2)
                                <input type="text" value="4" name="kelas" hidden>
                            @elseif ($data->markas_id == 3)
                                <input type="text" value="9" name="kelas" hidden>
                            @endif
                            <input type="text" value="{{ $sekarang->format('m') }}" hidden name="bulan">
                            <input type="text" value="{{ $sekarang->format('Y') }}" hidden name="tahun">
                            <button style="background-color: transparent; border: 0px">
                                <span class="fa-stack fa-3x">
                                    <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                    <i class="fas fa-list fa-stack-1x fa-inverse"></i>
                                </span>
                                    <h6 class="my-3 text-dark">Rekap Absensi Pembelajaran</h6>
                            </button>
                        </form>
                    </div>
                    <div class="col-xl-3 col-md-3 text-center">
                        <a href="{{ route('staf-admin.absen.staf') }}">
                            <span class="fa-stack fa-3x">
                                <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                <i class="fas fa-qrcode fa-stack-1x fa-inverse"></i>
                            </span>
                                <h6 class="my-3 text-dark">Absensi Staf</h6>
                        </a>
                    </div>
                    <div class="col-xl-3 col-md-3 text-center">
                        <form action="{{ route('staf-admin.absensi.rekap-staf') }}" method="GET">
                            <input type="text" value="{{ $sekarang->format('m') }}" hidden name="bulan">
                            <input type="text" value="{{ $sekarang->format('Y') }}" hidden name="tahun">
                            <button style="background-color: transparent; border: 0px">
                                <span class="fa-stack fa-3x">
                                    <i class="fas fa-circle fa-stack-2x text-warning"></i>
                                    <i class="fas fa-list fa-stack-1x fa-inverse"></i>
                                </span>
                                    <h6 class="my-3 text-dark">Rekap Absensi Staf</h6>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Edit Profil</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('staf-admin.update-profil', [$data->pendidik_id]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control form-control-user" id="exampleFirstName"
                                placeholder="Nama Lengkap" name="nama" value="{{$user}}" readonly>
                        </div>
                        <div class="col-sm-4">
                            <label for="tempat">Tempat Lahir</label>
                            <input type="text" autofocus class="form-control form-control-user" name="tempat_lahir" placeholder="Tempat Lahir" >
                        </div>
                        <div class="col-sm-4">
                            <label for="tanggal">Tanggal Lahir</label>
                            <input type="date" class="form-control form-control-user" name="tanggal_lahir" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="nik">Nomor Induk Kependudukan</label>
                            <input type="text" class="form-control form-control-user" id="exampleFirstName"
                                placeholder="Nama Lengkap" name="nik" >
                        </div>
                        <div class="col-sm-4">
                            <label for="nisn">Nomor Induk Pegawai (NIP/NIPPPK) *</label>
                            <input type="text" autofocus class="form-control form-control-user" name="nip" placeholder="NIP/NIPPPK">
                        </div>
                        <div class="col-sm-4">
                            <label for="tanggal">Nama Ibu Kandung</label>
                            <input type="text" class="form-control form-control-user" name="ibu" placeholder="Nama Ibu Kandung" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="alamat">Alamat</label>
                                <input type="text" class="form-control form-control-user" id="alamat"
                                    placeholder="Alamat" name="alamat" >
                        </div>
                        <div class="col-sm-4">
                            <label for="wa">No. Telepon/WhatsApp</label>
                            <input type="number" class="form-control form-control-user" name="wa" placeholder="Nomor Telepon/Whatsapp" >
                        </div>
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="markas">Markas</label>
                            <select name="markas" class="form-control form-control-user" >
                                <option value="Genteng">Genteng</option>
                                <option value="Banyuwangi">Banyuwangi</option>
                                <option value="Jember">Jember</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">

                        <div class="col-sm-4">
                            <label for="foto">Foto Diri (3x4) <div class="text-danger">Maksimal 500 Kb</div> </label>
                                <input type="file" id="foto"
                                name="foto" >
                        </div>
                        <div class="col-sm-4">
                            <label for="cv"><i>Curriculum Vitae </i><div class="text-danger">Maksimal 1 Mb</div> </label>
                            <input type="file" id="cv"
                            name="cv" >
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn btn-warning" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
          </div>
        </div>
    </div>
    <div class="modal fade" id="qrcode-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">QR Code Absensi</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div id="qrcode"></div>
                    <div class="text-left mt-4">
                        <p>Token : <b>{{ $token }}</b></p>
                    </div>
            </div>
          </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('/js/qrcode.min.js') }}"></script>
<script>
    let input = document.querySelector('#text');
    let button = document.querySelector('#btn-qrcode');
    let qrcode = new QRCode(document.querySelector('#qrcode'), {

        width: 200,
        height: 200,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
    button.addEventListener('click', () => {
      let inputValue = input.value;
      qrcode.makeCode(inputValue);
    })
</script>
@endsection
