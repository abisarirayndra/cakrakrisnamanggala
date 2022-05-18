@extends('master.master')

@section('title')
<title>Computer Assisted Test - Cakra Krisna Manggala</title>
@endsection

@section('content')
<div class="container">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="float-right">
                        <a href="{{route('pendidik.dinas.soalgandapoin', [$soal->dn_tes_id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin membatalkan proses edit ?')"><i class="fas fa-times"></i></a>
                    </div>
                    <h5><i class="fas fa-hashtag text-warning"></i> Edit Soal Pilihan Ganda Berpoin</h5>
                    <div class="p-3 mt-3">
                        <form action="{{route('pendidik.dinas.updatesoalgandapoin', [$id])}}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Nomor Soal</label>
                                <input type="text" name="nomor_soal" class="form-control" value="{{$soal->nomor_soal}}" required>
                            </div>
                            <div class="form-group">
                                <label for="nama">Soal</label>
                                <textarea name="soal" class="ckeditor form-control" id="ckditor">{{$soal->soal}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="nama">Opsi A</label>
                                <textarea name="opsi_a" class="ckeditor form-control" id="ckditor">{{$soal->opsi_a}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="nama">Opsi B</label>
                                <textarea name="opsi_b" class="ckeditor form-control" id="ckditor">{{$soal->opsi_b}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="nama">Opsi C</label>
                                <textarea name="opsi_c" class="ckeditor form-control" id="ckditor">{{$soal->opsi_c}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="nama">Opsi D</label>
                                <textarea name="opsi_d" class="ckeditor form-control" id="ckditor">{{$soal->opsi_d}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="nama">Opsi E</label>
                                <textarea name="opsi_e" class="ckeditor form-control" id="ckditor">{{$soal->opsi_e}}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="kunci">Poin A</label>
                                        <input type="number" name="poin_a" class="form-control" value="{{$soal->poin_a}}"required>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="kunci">Poin B</label>
                                        <input type="number" name="poin_b" class="form-control" value="{{$soal->poin_b}}" required>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="kunci">Poin C</label>
                                        <input type="number" name="poin_c" class="form-control" value="{{$soal->poin_c}}" required>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="kunci">Poin D</label>
                                        <input type="number" name="poin_d" class="form-control" value="{{$soal->poin_d}}" required>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="kunci">Poin E</label>
                                        <input type="number" name="poin_e" class="form-control" value="{{$soal->poin_e}}">
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <a href="{{route('pendidik.dinas.soalganda', [$soal->dn_tes_id])}}" class="btn btn-danger" onclick="return confirm('Anda yakin ingin membatalkan proses edit ?')">Batal</a>
                                <button class="btn btn-warning" type="submit">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </div>


</div>
@endsection

@section('js')
<script type="text/javascript" src="{{asset('vendor/ckeditor/ckeditor.js')}}"></script>
@endsection
