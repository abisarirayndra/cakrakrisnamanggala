@extends('master.staf')

@section('title')
    <title>Cakra Krisna Manggala</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/css/datepicker.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5><i class="fas fa-hashtag text-warning"></i> Jadwal Pembelajaran</h5>
            <div class="p-3 mt-3">
                <div class="row mb-3">
                    <button type="button" class="btn btn-sm btn-warning mr-3" data-toggle="modal" data-target="#tambahjadwal">
                        <i class="fas fa-file-archive"></i> Tambah
                    </button>
                </div>
                <div class="row">
                    <form action="{{ route('staf-admin.jadwal') }}" method="GET">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="kelas">Kelas</label>
                                <select name="kelas" id="" class="form-control">
                                    @foreach ($kelas as $item)
                                        <option value="{{ $item->id }}" @if ($item->id == $kelas_id) {{'selected="selected"'}}  @endif>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ml-2">
                                <label for="bulan">Bulan</label>
                                <input type="text" class="form-control" id="datepicker-month" name="bulan" value="{{ $bulan }}" placeholder="Masukkan Bulan" autocomplete="off"/>
                            </div>
                            <div class="form-group ml-2">
                                <label for="tahun">Tahun</label>
                                <input type="text" class="form-control" id="datepicker-year" name="tahun" value="{{ $tahun }}" placeholder="Masukkan Tahun" autocomplete="off"/>
                            </div>
                            <div class="form-group ml-2 pt-4">
                                <button class="btn btn-sm btn-warning">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row mt-4">
                    @foreach ($jadwal as $item)
                    <div class="col-sm-3">
                        <div class="card mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
                                <h6 class="m-0 font-weight-bold text-white">{{\Carbon\Carbon::parse($item->mulai)->isoFormat('dddd, D MMMM Y')}}</h6>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-100"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                        <div class="dropdown-header">Opsi</div>
                                        <a class="dropdown-item" href="{{ route('staf-admin.jadwal.edit', [$item->id]) }}"><i class="fas fa-pen"></i> Edit</a>
                                        <a class="dropdown-item" href="{{ route('staf-admin.jadwal.hapus', [$item->id]) }}" onclick="return confirm('Anda yakin untuk menghapus jadwal ini ?')"><i class="fas fa-trash"></i> Hapus</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-s mb-0">Mapel <b>{{ $item->mapel }}</b></p>
                                <p class="text-s mb-0">{{ $item->nama }}</p>
                                <p class="text-s mb-0"><b>{{\Carbon\Carbon::parse($item->mulai)->isoFormat('HH:mm')}} - {{ \Carbon\Carbon::parse($item->selesai)->isoFormat('HH:mm') }}</b></p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="tambahjadwal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Tambah Jadwal</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('staf-admin.jadwal.tambah') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label>Mata Pelajaran</label>
                        <input type="text" value="{{ $admin }}" name="staf_id" hidden>
                        <select name="mapel_id" id="" class="form-control">
                            @foreach ($mapel as $item)
                                <option value="{{ $item->id }}">{{ $item->mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="kelas_id" id="" class="form-control">
                            @foreach ($kelas as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pendidik (Penanggung Jawab)</label>
                        <select name="pendidik_id" id="" class="form-control">
                            @foreach ($pendidik as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Mulai</label>
                        <input type="datetime-local" class="form-control" name="mulai" required>
                    </div>
                    <div class="form-group">
                        <label>Selesai</label>
                        <input type="datetime-local" class="form-control" name="selesai" required>
                    </div>
                    <div class="text-center mt-4">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/js/bootstrap-datepicker.min.js"></script>
<script>
    $("#datepicker-month").datepicker( {
    format: "mm",
    startView: "months",
    minViewMode: "months"
});
$("#datepicker-year").datepicker( {
    format: "yyyy",
    startView: "years",
    minViewMode: "years"
});
</script>
@endsection
