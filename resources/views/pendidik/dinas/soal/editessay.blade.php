@extends('master.master')

@section('title')
<title>Computer Assisted Test - Cakra Krisna Manggala</title>
@endsection

@section('content')
<div class="container">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="float-right">
                        <a href="{{route('pendidik.dinas.soalessay', [$soal->dn_tes_id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin membatalkan proses edit ?')"><i class="fas fa-times"></i></a>
                    </div>
                    <h5><i class="fas fa-hashtag text-warning"></i> Edit Soal Essay</h5>
                    <div class="p-3 mt-3">
                        <form action="{{route('pendidik.dinas.updatesoalessay', [$id])}}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="nama">Soal</label>
                                <textarea name="soal" class="ckeditor form-control" id="ckditor">{{$soal->soal}}</textarea>
                            </div>
                            <div class="text-center mt-4">
                                <a href="{{route('pendidik.dinas.soalessay', [$soal->dn_tes_id])}}" class="btn btn-danger" onclick="return confirm('Anda yakin ingin membatalkan proses edit ?')">Batal</a>
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
