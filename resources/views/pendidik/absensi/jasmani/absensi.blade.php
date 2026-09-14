@extends('layouts.panel-pendidik')

@section('title', 'Absensi Lapangan - Cakra Krisna Manggala')

@section('content')
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Kehadiran</p>
            <h1 class="h3 mb-1">Absensi Kehadiran</h1>
        </div>
        <a href="{{ route('pendidik.absensi.jadwal_jasmani') }}" class="btn btn-ck-ghost">Kembali</a>
    </div>

    <section class="ck-card p-4 mb-4">
        <p class="text-uppercase small fw-semibold mb-2" style="color: var(--ck-gold);">
            {{ \Carbon\Carbon::parse($jadwal->mulai)->isoFormat('dddd, D MMMM Y') }}
        </p>
        <p class="mb-1">Mapel <b>{{ $jadwal->mapel }}</b></p>
        <p class="mb-1">Kelas <b>{{ $jadwal->kelas }}</b></p>
        <p class="mb-1">{{ $jadwal->nama }}</p>
        <p class="mb-0 ck-hint">
            {{ \Carbon\Carbon::parse($jadwal->mulai)->isoFormat('HH:mm') }} –
            {{ \Carbon\Carbon::parse($jadwal->selesai)->isoFormat('HH:mm') }}
        </p>
    </section>

    @if (session()->has('success'))
        <div class="alert alert-ck mb-4">{{ session()->get('success') }}</div>
    @endif

    <section class="ck-card p-4 p-md-5 mb-4">
        <h2 class="h5 mb-3">Absensi Coach</h2>
        <form action="{{ route('pendidik.absensi.upload_absensi_jasmani') }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table align-middle" id="dynamicTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jurnal</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendidik as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->jurnal }}</td>
                                <td>
                                    <a href="{{ route('pendidik.absensi.hapus-izin-pendidik', [$item->pendidik_id]) }}" class="btn btn-sm btn-ck-ghost">Hapus</a>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td>
                                <select name="tambah[0][pendidik_id]" class="form-select" autofocus>
                                    @foreach ($nama_pendidik as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="tambah[0][jadwal_id]" value="{{ $jadwal->id }}">
                                <input type="hidden" name="tambah[0][datang]" value="{{ $jadwal->mulai }}">
                                <input type="hidden" name="tambah[0][pulang]" value="{{ $jadwal->selesai }}">
                                <input type="hidden" name="tambah[0][status]" value="3">
                            </td>
                            <td>
                                <input type="text" name="tambah[0][jurnal]" required class="form-control" placeholder="Ngajar apa hari ini ?">
                            </td>
                            <td>
                                <button type="button" name="add" id="add" class="btn btn-sm btn-ck">Tambah</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-ck">Simpan Pendidik</button>
        </form>
    </section>

    <section class="ck-card p-4 p-md-5 mb-4">
        <h2 class="h5 mb-3">Absensi Pelajar</h2>
        <form action="{{ route('pendidik.absensi.upload_absensi_jasmani.pelajar') }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table align-middle" id="dynamicTable2">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pelajar as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>
                                    <a href="{{ route('pendidik.absensi.hapus-izin-pelajar', [$item->pelajar_id]) }}" class="btn btn-sm btn-ck-ghost">Hapus</a>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td>
                                <select name="tambah2[0][pelajar_id]" class="form-select">
                                    @foreach ($nama_pelajar as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="tambah2[0][jadwal_id]" value="{{ $jadwal->id }}">
                                <input type="hidden" name="tambah2[0][datang]" value="{{ $jadwal->mulai }}">
                                <input type="hidden" name="tambah2[0][pulang]" value="{{ $jadwal->selesai }}">
                                <input type="hidden" name="tambah2[0][status]" value="3">
                            </td>
                            <td>
                                <button type="button" name="add2" id="add2" class="btn btn-sm btn-ck">Tambah</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-ck">Simpan Pelajar</button>
        </form>
    </section>

    <section class="ck-card p-4 p-md-5">
        <h2 class="h5 mb-3">Izin Tidak Masuk</h2>
        <div class="d-flex flex-wrap gap-2 mb-4">
            <button class="btn btn-sm btn-ck-ghost" data-bs-toggle="modal" data-bs-target="#tambahdata">Pelajar</button>
            <button class="btn btn-sm btn-ck-ghost" data-bs-toggle="modal" data-bs-target="#tambahpendidik">Pendidik</button>
        </div>
        <p class="fw-semibold mb-2">Pelajar</p>
        @forelse ($izin_pelajar as $item)
            <p class="mb-2">{{ $item->nama }} : {{ $item->keterangan }}
                <a href="{{ route('pendidik.absensi.hapus-izin-pelajar', [$item->pelajar_id]) }}" class="btn btn-sm btn-ck-ghost">Hapus</a>
            </p>
        @empty
            <p class="ck-hint">Belum ada izin pelajar.</p>
        @endforelse
        <p class="fw-semibold mb-2 mt-3">Pendidik</p>
        @forelse ($izin_pendidik as $item)
            <p class="mb-2">{{ $item->nama }} : {{ $item->keterangan }}
                <a href="{{ route('pendidik.absensi.hapus-izin-pendidik', [$item->pendidik_id]) }}" class="btn btn-sm btn-ck-ghost">Hapus</a>
            </p>
        @empty
            <p class="ck-hint mb-0">Belum ada izin pendidik.</p>
        @endforelse
    </section>

    <div class="modal fade" id="tambahdata" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ck-card">
                <div class="modal-header border-0">
                    <h2 class="modal-title h5">Tambah Data Pelajar</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form action="{{ route('pendidik.absensi.upload-izin-pelajar') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label class="form-label">Pelajar</label>
                        <select name="pelajar_id" class="form-select mb-3">
                            @foreach ($nama_pelajar as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                        <input type="hidden" name="status" value="2">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="6" class="form-control"></textarea>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-ck-ghost" data-bs-dismiss="modal">Keluar</button>
                        <button type="submit" class="btn btn-ck">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambahpendidik" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content ck-card">
                <div class="modal-header border-0">
                    <h2 class="modal-title h5">Tambah Data Pendidik</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form action="{{ route('pendidik.absensi.upload-izin-pendidik') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label class="form-label">Pendidik</label>
                        <select name="pendidik_id" class="form-select mb-3">
                            @foreach ($nama_pendidik as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="6" class="form-control"></textarea>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-ck-ghost" data-bs-dismiss="modal">Keluar</button>
                        <button type="submit" class="btn btn-ck">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script>
    var i = 0;
    $("#add").click(function () {
        ++i;
        $("#dynamicTable").append('<tr><td></td><td><select name="tambah['+i+'][pendidik_id]" class="form-select">@foreach ($nama_pendidik as $item)<option value="{{ $item->id }}">{{ $item->nama }}</option>@endforeach</select><input type="hidden" name="tambah['+i+'][jadwal_id]" value="{{ $jadwal->id }}"><input type="hidden" name="tambah['+i+'][datang]" value="{{ $jadwal->mulai }}"><input type="hidden" name="tambah['+i+'][pulang]" value="{{ $jadwal->selesai }}"><input type="hidden" name="tambah['+i+'][status]" value="3"></td><td><input type="text" name="tambah['+i+'][jurnal]" required class="form-control" placeholder="Ngajar apa hari ini ?"></td><td><button type="button" class="btn btn-ck-ghost btn-sm remove-tr">Hapus</button></td></tr>');
    });
    $(document).on('click', '.remove-tr', function () {
        $(this).parents('tr').remove();
    });

    var j = 0;
    $("#add2").click(function () {
        ++j;
        $("#dynamicTable2").append('<tr><td></td><td><select name="tambah2['+j+'][pelajar_id]" class="form-select">@foreach ($nama_pelajar as $item)<option value="{{ $item->id }}">{{ $item->nama }}</option>@endforeach</select><input type="hidden" name="tambah2['+j+'][jadwal_id]" value="{{ $jadwal->id }}"><input type="hidden" name="tambah2['+j+'][datang]" value="{{ $jadwal->mulai }}"><input type="hidden" name="tambah2['+j+'][pulang]" value="{{ $jadwal->selesai }}"><input type="hidden" name="tambah2['+j+'][status]" value="3"></td><td><button type="button" class="btn btn-ck-ghost btn-sm remove-pelajar">Hapus</button></td></tr>');
    });
    $(document).on('click', '.remove-pelajar', function () {
        $(this).parents('tr').remove();
    });
</script>
@endpush
