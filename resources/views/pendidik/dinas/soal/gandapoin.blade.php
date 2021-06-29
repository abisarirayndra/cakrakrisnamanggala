@extends('master.master')

@section('title')
    <title>CAT - Kedinasan</title>
    <style>
        p{
            display: inline-block;
        }

    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="text-right mr-3 mb-3">
        <a href="{{ route('pendidik.dinas.paket') }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
    </div>
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-6">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5><i class="fas fa-hashtag text-warning"></i> Soal Pilihan Ganda Berpoin</h5>
                    <div class="p-3 mt-3">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{route('pendidik.dinas.upsoalgandapoin', [$id])}}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="nama">Soal</label>
                                <textarea name="soal" class="ckeditor form-control" id="ckditor" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="nama">Opsi A</label>
                                <textarea name="opsi_a" class="ckeditor form-control" id="ckditor" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="kunci">Poin Opsi A</label>
                                <input type="number" name="poin_a" class="form-control" placeholder="" required>
                            </div>
                            <div class="form-group">
                                <label for="nama">Opsi B</label>
                                <textarea name="opsi_b" class="ckeditor form-control" id="ckditor" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="kunci">Poin Opsi B</label>
                                <input type="number" name="poin_b" class="form-control" placeholder="" required>
                            </div>
                            <div class="form-group">
                                <label for="nama">Opsi C</label>
                                <textarea name="opsi_c" class="ckeditor form-control" id="ckditor" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="kunci">Poin Opsi C</label>
                                <input type="number" name="poin_c" class="form-control" placeholder="" required>
                            </div>
                            <div class="form-group">
                                <label for="nama">Opsi D</label>
                                <textarea name="opsi_d" class="ckeditor form-control" id="ckditor" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="kunci">Poin Opsi D</label>
                                <input type="number" name="poin_d" class="form-control" placeholder="" required>
                            </div>
                            <div class="form-group">
                                <label for="nama">Opsi E (Opsional)</label>
                                <textarea name="opsi_e" class="ckeditor form-control" id="ckditor"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="kunci">Poin Opsi E</label>
                                <input type="number" name="poin_e" class="form-control" placeholder="">
                            </div>
                            <div class="text-center mt-4">
                                <button class="btn btn-warning" type="submit">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5><i class="fas fa-hashtag text-warning"></i> Preview Soal</h5>
                    <div class="p-3 mt-3">
                        <div>
                            <table>
                                @php
                                    $a = 1;
                                @endphp
                                @foreach ($soal as $item)
                                <tr>
                                    <td style="vertical-align: top;"><b>{{$a++}}</b></td>
                                    <td class="pl-4 pb-4">
                                        <div style="text-align: justify"><b>{!!$item->soal!!}</b></div> <br>
                                        <ol type="A">
                                            <li>{!!$item->opsi_a!!} <b>(Poin : {{$item->poin_a}})</b></li>
                                            <li>{!!$item->opsi_b!!} <b>(Poin : {{$item->poin_b}})</b></li>
                                            <li>{!!$item->opsi_c!!} <b>(Poin : {{$item->poin_c}})</b></li>
                                            <li>{!!$item->opsi_d!!} <b>(Poin : {{$item->poin_d}})</b></li>
                                            @if (isset($item->opsi_e))
                                            <li>{!!$item->opsi_e!!} <b>(Poin : {{$item->poin_e}})</b></li>
                                            @endif
                                        </ol>
                                        <a href="{{route('pendidik.dinas.editsoalgandapoin', [$item->id])}}" class="btn btn-sm btn-success"><i class="fas fa-feather-alt"></i> Edit</a>
                                        <a href="{{route('pendidik.dinas.hapussoalgandapoin', [$item->id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin Menghapus ?')"><i class="fas fa-trash"></i> Hapus</a>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection

@section('js')
<script type="text/javascript" src="{{asset('vendor/ckeditor/ckeditor.js')}}"></script>
@endsection
