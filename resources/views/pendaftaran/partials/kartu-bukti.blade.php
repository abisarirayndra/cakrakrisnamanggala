<div class="row g-4">
    <div class="col-md-4 text-center">
        <div class="ck-photo-frame mx-auto">
            @if ($data->foto)
                <img src="{{ asset('img/pelajar/'.$data->foto) }}" alt="Foto {{ $data->nama }}">
            @endif
        </div>
    </div>
    <div class="col-md-8">
        <dl class="ck-meta row mb-0">
            <div class="col-sm-6">
                <dt>Nama</dt>
                <dd>{{ $data->nama }}</dd>
            </div>
            <div class="col-sm-6">
                <dt>Email</dt>
                <dd>{{ $data->email }}</dd>
            </div>
            <div class="col-sm-6">
                <dt>NIK</dt>
                <dd>{{ $data->nik ?: '— Belum tersedia —' }}</dd>
            </div>
            <div class="col-sm-6">
                <dt>NISN</dt>
                <dd>{{ $data->nisn ?: '— Belum tersedia —' }}</dd>
            </div>
            <div class="col-sm-6">
                <dt>Tempat, tanggal lahir</dt>
                <dd>{{ $data->tempat_lahir }}, {{ \Carbon\Carbon::parse($data->tanggal_lahir)->isoFormat('D MMMM Y') }}</dd>
            </div>
            <div class="col-sm-6">
                <dt>Nama ibu kandung</dt>
                <dd>{{ $data->ibu ?: '— Belum tersedia —' }}</dd>
            </div>
            <div class="col-12">
                <dt>Alamat</dt>
                <dd>{{ $data->alamat }}</dd>
            </div>
            <div class="col-sm-6">
                <dt>Asal sekolah</dt>
                <dd>{{ $data->sekolah }}</dd>
            </div>
            <div class="col-sm-6">
                <dt>Status sekolah</dt>
                <dd>{{ (int) $data->status_sekolah === 1 ? 'Lulus' : 'Belum Lulus' }}</dd>
            </div>
            <div class="col-sm-6">
                <dt>WhatsApp</dt>
                <dd>{{ $data->wa }}</dd>
            </div>
            <div class="col-sm-6">
                <dt>Nama wali</dt>
                <dd>{{ $data->wali }}</dd>
            </div>
            <div class="col-sm-6">
                <dt>WhatsApp wali</dt>
                <dd>{{ $data->wa_wali ?: '— Belum tersedia —' }}</dd>
            </div>
            <div class="col-sm-6">
                <dt>Markas</dt>
                <dd>{{ $data->markas }}</dd>
            </div>
            <div class="col-sm-6">
                <dt>Tanggal daftar</dt>
                <dd>{{ \Carbon\Carbon::parse($data->created_at)->isoFormat('dddd, D MMMM Y HH:mm') }}</dd>
            </div>
        </dl>
    </div>
</div>
