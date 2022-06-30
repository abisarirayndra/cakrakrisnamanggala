@extends('master.super')

@section('title')

@endsection

@section('content')
    <!-- Begin Page Content -->
<div class="container">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Detail Pendaftar</h5>
            <div class="p-3 mt-3">
                <div class="row">
                    <div class="col-xl-4 col-sm-4 text-center">
                        <img src="{{asset('/img/pelajar/'. $pendaftar->foto)}}" width="120" alt="">
                    </div>
                    <div class="col-xl-8 col-sm-8">
                        <div class="row">
                            <table>
                                <tr>
                                    <td><b>Nama</b></td>
                                    <td class="pl-4">{{$pendaftar->nama}}</td>
                                </tr>
                                <tr>
                                    <td><b>Email</b></td>
                                    <td class="pl-4">{{$pendaftar->email}}</td>
                                </tr>
                                <tr>
                                    <td><b>NIK</b></td>
                                    <td class="pl-4">{{$pendaftar->nik}}</td>
                                </tr>
                                <tr>
                                    <td><b>NISN</b></td>
                                    <td class="pl-4">{{$pendaftar->nisn}}</td>
                                </tr>
                                <tr>
                                    <td><b>Tempat, Tanggal Lahir</b></td>
                                    <td class="pl-4">{{$pendaftar->tempat_lahir}}, {{\Carbon\Carbon::parse($pendaftar->tanggal_lahir)->isoFormat('D MMMM Y')}}</td>
                                </tr>
                                <tr>
                                    <td><b>Alamat</b></td>
                                    <td class="pl-4">{{$pendaftar->alamat}}</td>
                                </tr>
                                <tr>
                                    <td><b>Asal Sekolah</b></td>
                                    <td class="pl-4">{{$pendaftar->sekolah}}</td>
                                </tr>
                                <tr>
                                    <td><b>No. Telpon/WhatsApp</b></td>
                                    <td class="pl-4">{{$pendaftar->wa}}</td>
                                </tr>
                                <tr>
                                    <td><b>Nama Ibu</b></td>
                                    <td class="pl-4">{{$pendaftar->ibu}}</td>
                                </tr>
                                <tr>
                                    <td><b>Nama Wali</b></td>
                                    <td class="pl-4">{{$pendaftar->wali}}</td>
                                </tr>
                                <tr>
                                    <td><b>Markas Yang Dipilih</b></td>
                                    <td class="pl-4">{{$pendaftar->markas}}</td>
                                </tr>
                                <tr>
                                    <td><b>Tanggal Daftar</b></td>
                                    <td class="pl-4">{{\Carbon\Carbon::parse($pendaftar->created_at)->isoFormat('dddd, D MMMM Y HH:mm')}}</td>
                                </tr>
                            </table>
                                {{-- <a href="" class="btn btn-success mt-4"><i class="fas fa-cloud-download-alt"></i> Unduh PDF</a>
                                <a href="" class="btn btn-danger mt-4 ml-3"><i class="fas fa-trash"></i> Hapus Data</a> --}}

                        </div>
                        <div class="row">
                            <button data-toggle="modal" data-target="#migrasidata" class="btn btn-warning mt-4 ml-3"><i class="fas fa-edit"></i> Migrasi Data</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="migrasidata" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-warning" id="exampleModalLabel">Migrasi Ke Pelajar</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
                <form action="{{ route('super.penggunapendaftar.migrasi', [$pendaftar->pelajar_id]) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama">Masukkan ke kelas ?</label>
                            <select name="kelas_id" class="form-control" required>
                                @foreach ($kelas as $item)
                                    <option value="{{$item->id}}">{{$item->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Keluar</button>
                        <button type="submit" class="btn btn-warning">Simpan</button>
                    </div>
                </form>
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
