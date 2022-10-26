@extends('master.master')

@section('title')
<title>Computer Assisted Test - Cakra Krisna Manggala</title>
@endsection

@section('content')
<div class="container">
    <div class="text-right mr-3 mb-3">
        <a href="{{ route('pendidik.dinas.paket') }}" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Preview Soal</h5>
            <div class="p-3 mt-3">
                <div>
                    <table>
                        @foreach ($soal as $item)
                        <tr>
                            <td style="vertical-align: top;"><b>{{$item->nomor_soal}}</b></td>
                            <td class="pl-4 pb-4">
                                <div style="text-align: justify"><b>{!!$item->soal!!}</b></div> <br>
                                <ol type="A">
                                    <li>{!!$item->opsi_a!!}</li>
                                    <li>{!!$item->opsi_b!!}</li>
                                    <li>{!!$item->opsi_c!!}</li>
                                    <li>{!!$item->opsi_d!!}</li>
                                    @if (isset($item->opsi_e))
                                    <li>{!!$item->opsi_e!!}</li>
                                    @endif
                                </ol>
                                <b>Kunci :</b> {{$item->kunci}} <br>
                                <a href="{{route('pendidik.dinas.editsoalganda', [$item->id])}}" class="btn btn-sm btn-success"><i class="fas fa-feather-alt"></i> Edit</a>
                                <a href="{{route('pendidik.dinas.hapussoalganda', [$item->id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin Menghapus ?')"><i class="fas fa-trash"></i> Hapus</a>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Soal Pilihan Ganda</h5>
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

                <form action="{{route('pendidik.dinas.upsoalganda', [$id])}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nama">Nomor Soal</label>
                        @if (isset($ada_soal))
                            <input name="nomor_soal" type="number" class="form-control" value="{{ $ada_soal->nomor_soal + 1 }}" required>
                        @else
                            <input name="nomor_soal" type="number" class="form-control" required>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="nama">Soal</label>
                        <textarea name="soal" class=" form-control ckeditor" autofocus required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="nama">Opsi A</label>
                        <textarea name="opsi_a" class="ckeditor form-control" id="ckditor" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="nama">Opsi B</label>
                        <textarea name="opsi_b" class="ckeditor form-control" id="ckditor" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="nama">Opsi C</label>
                        <textarea name="opsi_c" class="ckeditor form-control" id="ckditor" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="nama">Opsi D</label>
                        <textarea name="opsi_d" class="ckeditor form-control" id="ckditor" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="nama">Opsi E</label>
                        <textarea name="opsi_e" class="ckeditor form-control" id="ckditor"></textarea>
                    </div>
                    <div class="form-group ">
                        <label for="kunci"><b>Kunci</b> </label>
                        {{-- <input type="text" name="kunci" class="form-control" placeholder="Abjad Jawaban"> --}}
                        <div class="form-check">
                            <input type="radio" name="kunci" class="form-check-input" value="A">
                            <label for="A" class="form-check-label"> A </label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="kunci" class="form-check-input" value="B">
                            <label for="A" class="form-check-label"> B </label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="kunci" class="form-check-input" value="C">
                            <label for="A" class="form-check-label"> C </label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="kunci" class="form-check-input" value="D">
                            <label for="A" class="form-check-label"> D </label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="kunci" class="form-check-input" value="E">
                            <label for="A" class="form-check-label"> E </label>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn btn-warning" type="submit">Simpan</button>
                    </div>
                </form>
                {{-- <hr> --}}
                {{-- <form action="{{ route('pendidik.dinas.importganda') }}" class="mt-4" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="file">Import Soal (Opsional)</label>
                        <input type="file" class="form-control" name="file">
                    </div>
                    <div class="form-group">
                        <label for="file">Kode Tes (Opsional)</label>
                        <input type="text" class="form-control" readonly value="{{ $id }}">
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn btn-success" type="submit">Import</button>
                    </div>
                </form> --}}
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="{{asset('vendor/ckeditor/ckeditor.js')}}"></script>
@endsection
