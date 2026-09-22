<div>
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-semibold mb-1" style="color: var(--ck-gold);">Analisis</p>
            <h1 class="h3 mb-1">{{ $jadwal->nama }}</h1>
            <p class="ck-hint mb-0">
                {{ $jadwal->labelWaktuPelaksanaan() }}
                @if ($banks->isNotEmpty())
                    · {{ $banks->pluck('nama')->filter()->join(' · ') }}
                @endif
            </p>
        </div>
        <a href="{{ route('pendidik.dinas.analisis') }}" class="btn btn-ck-ghost">Kembali</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg">
            <section class="ck-card p-3 h-100">
                <p class="ck-hint mb-1">Peserta</p>
                <p class="h4 mb-0">{{ $ringkasan['peserta'] }}</p>
            </section>
        </div>
        <div class="col-6 col-lg">
            <section class="ck-card p-3 h-100">
                <p class="ck-hint mb-1">Selesai</p>
                <p class="h4 mb-0">{{ $ringkasan['selesai'] }}</p>
            </section>
        </div>
        <div class="col-6 col-lg">
            <section class="ck-card p-3 h-100">
                <p class="ck-hint mb-1">Rata-rata skor</p>
                <p class="h4 mb-0">{{ $ringkasan['rata'] ?? '—' }}</p>
            </section>
        </div>
        <div class="col-6 col-lg">
            <section class="ck-card p-3 h-100">
                <p class="ck-hint mb-1">Skor tertinggi</p>
                <p class="h4 mb-0">{{ $ringkasan['tertinggi'] ?? '—' }}</p>
            </section>
        </div>
        <div class="col-6 col-lg">
            <section class="ck-card p-3 h-100">
                <p class="ck-hint mb-1">Skor terendah</p>
                <p class="h4 mb-0">{{ $ringkasan['terendah'] ?? '—' }}</p>
            </section>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">Soal tersulit</p>
                @if ($ringkasan['tersulit'] && $ringkasan['tersulit']['persen_benar'] !== null)
                    <p class="fw-semibold mb-1">No. {{ $ringkasan['tersulit']['nomor'] }} · {{ $ringkasan['tersulit']['bank'] }}</p>
                    <p class="ck-hint mb-0">{{ $ringkasan['tersulit']['soal'] }} · {{ $ringkasan['tersulit']['persen_benar'] }}% benar</p>
                @else
                    <p class="ck-hint mb-0">Belum ada tes selesai.</p>
                @endif
            </section>
        </div>
        <div class="col-md-6">
            <section class="ck-card p-4 h-100">
                <p class="ck-hint mb-1">Soal termudah</p>
                @if ($ringkasan['termudah'] && $ringkasan['termudah']['persen_benar'] !== null)
                    <p class="fw-semibold mb-1">No. {{ $ringkasan['termudah']['nomor'] }} · {{ $ringkasan['termudah']['bank'] }}</p>
                    <p class="ck-hint mb-0">{{ $ringkasan['termudah']['soal'] }} · {{ $ringkasan['termudah']['persen_benar'] }}% benar</p>
                @else
                    <p class="ck-hint mb-0">Belum ada tes selesai.</p>
                @endif
            </section>
        </div>
    </div>

    <section class="ck-card p-4">
        <div class="ck-tabs mb-4" role="tablist" aria-label="Detail analisis">
            <button
                type="button"
                class="ck-tab {{ $tab === 'soal' ? 'active' : '' }}"
                wire:click="pilihTab('soal')"
                role="tab"
                aria-selected="{{ $tab === 'soal' ? 'true' : 'false' }}"
            >
                Analisis soal
            </button>
            <button
                type="button"
                class="ck-tab {{ $tab === 'siswa' ? 'active' : '' }}"
                wire:click="pilihTab('siswa')"
                role="tab"
                aria-selected="{{ $tab === 'siswa' ? 'true' : 'false' }}"
            >
                Hasil siswa
            </button>
        </div>

        @if ($tab === 'soal')
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                <p class="ck-hint mb-0">Persentase dihitung dari sesi yang sudah dikumpulkan pada bank soal Anda.</p>
                <a href="{{ route('pendidik.cat.analisis.soal.pdf', $jadwal) }}" class="btn btn-sm btn-ck">Unduh analisis</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Bank</th>
                            <th>Soal</th>
                            <th>Kunci</th>
                            <th>Benar</th>
                            <th>Salah</th>
                            <th>Kosong</th>
                            <th>% benar</th>
                            <th>Pilihan terbanyak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($soal as $item)
                            <tr>
                                <td>{{ $item['nomor'] }}</td>
                                <td>{{ $item['bank'] }}</td>
                                <td>{{ $item['soal'] }}</td>
                                <td class="fw-semibold">{{ $item['kunci'] }}</td>
                                <td>{{ $item['benar'] }}</td>
                                <td>{{ $item['salah'] }}</td>
                                <td>{{ $item['kosong'] }}</td>
                                <td>{{ $item['persen_benar'] === null ? '—' : $item['persen_benar'].'%' }}</td>
                                <td>{{ $item['pilihan_terbanyak'] ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="ck-hint">Belum ada soal pada bank Anda di paket ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                <p class="ck-hint mb-0">Skor hanya dari bank soal yang Anda buat. Buka detail untuk catatan dan jawaban per soal.</p>
                <a href="{{ route('pendidik.cat.analisis.pdf', $jadwal) }}" class="btn btn-sm btn-ck">Unduh analisis</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Benar</th>
                            <th>Salah</th>
                            <th>Kosong</th>
                            <th>Skor</th>
                            <th>Durasi</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswa as $item)
                            <tr wire:key="siswa-{{ $item['id'] }}">
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $item['nama'] }}</td>
                                <td>{{ $item['kelas'] }}</td>
                                <td>{{ $item['status'] }}</td>
                                <td>{{ $item['benar'] }}</td>
                                <td>{{ $item['salah'] }}</td>
                                <td>{{ $item['kosong'] }}</td>
                                <td class="fw-semibold">{{ $item['skor'] }}</td>
                                <td>{{ $item['durasi'] }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="bukaDetail({{ $item['id'] }})">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="ck-hint">Belum ada siswa mengerjakan bank soal Anda pada paket ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    @if ($detailSiswa)
        <div class="ck-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="detailSiswaTitle">
            <section class="ck-card p-4" style="max-width: 920px; width: 100%; max-height: calc(100vh - 6rem); overflow: auto;">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                    <div>
                        <h2 class="h5 mb-1" id="detailSiswaTitle">{{ $detailSiswa['nama'] }}</h2>
                        <p class="ck-hint mb-0">{{ $detailSiswa['kelas'] }} · {{ $detailSiswa['status'] }} · Skor {{ $detailSiswa['skor'] }} · {{ $detailSiswa['durasi'] }}</p>
                    </div>
                    <button type="button" class="btn btn-ck-ghost" wire:click="tutupDetail">Tutup</button>
                </div>

                <form wire:submit="simpanCatatan({{ $detailSiswa['id'] }})" class="mb-4">
                    <label for="catatan-{{ $detailSiswa['id'] }}" class="form-label">Catatan analisis</label>
                    <textarea
                        id="catatan-{{ $detailSiswa['id'] }}"
                        class="form-control mb-2"
                        rows="3"
                        wire:model="catatan.{{ $detailSiswa['id'] }}"
                    ></textarea>
                    @error('catatan.'.$detailSiswa['id'])
                        <div class="text-danger small mb-2">{{ $message }}</div>
                    @enderror
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <button type="submit" class="btn btn-sm btn-ck">Simpan catatan</button>
                        @if ((int) $tersimpanId === (int) $detailSiswa['id'])
                            <span class="ck-hint">Tersimpan</span>
                        @endif
                    </div>
                </form>

                <p class="ck-hint mb-2">Benar {{ $detailSiswa['benar'] }} · Salah {{ $detailSiswa['salah'] }} · Kosong {{ $detailSiswa['kosong'] }}</p>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    @foreach ($detailSiswa['soal'] as $baris)
                        <span
                            class="ck-nav-nomor {{ $baris['hasil'] === 'Benar' ? 'ck-nav-nomor-isi' : ($baris['hasil'] === 'Salah' ? 'ck-analisis-nomor-salah' : 'ck-nav-nomor-kosong') }}"
                            title="{{ $baris['bank'] }} · {{ $baris['hasil'] }}"
                        >{{ $baris['nomor'] }}</span>
                    @endforeach
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Bank</th>
                                <th>Soal</th>
                                <th>Kunci</th>
                                <th>Jawaban</th>
                                <th>Hasil</th>
                                <th>Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detailSiswa['soal'] as $baris)
                                <tr>
                                    <td>{{ $baris['nomor'] }}</td>
                                    <td>{{ $baris['bank'] }}</td>
                                    <td>{{ $baris['soal'] }}</td>
                                    <td>{{ $baris['kunci'] }}</td>
                                    <td>{{ $baris['jawaban'] }}</td>
                                    <td class="fw-semibold">{{ $baris['hasil'] }}</td>
                                    <td>{{ $baris['poin'] }}/{{ $baris['poin_maks'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    @endif
</div>
