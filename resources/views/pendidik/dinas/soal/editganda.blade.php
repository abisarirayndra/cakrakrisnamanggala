@extends('master.master')

@section('title')
    <title>CAT - Kedinasan</title>
@endsection

@section('content')
<div class="container">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="float-right">
                        <a href="{{route('pendidik.dinas.soalganda', [$soal->dn_tes_id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin membatalkan proses edit ?')"><i class="fas fa-times"></i></a>
                    </div>
                    <h5><i class="fas fa-hashtag text-warning"></i> Edit Soal Pilihan Ganda</h5>
                    <div class="p-3 mt-3">
                        <form action="{{route('pendidik.dinas.updatesoalganda', [$id])}}" method="POST">
                            @csrf
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
                            <div class="form-group">
                                <label for="kunci">Kunci</label>
                                <input type="text" name="kunci" class="form-control" value="{{$soal->kunci}}" placeholder="Abjad Jawaban">
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