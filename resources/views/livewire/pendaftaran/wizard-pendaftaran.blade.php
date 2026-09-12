<div class="ck-card p-4 p-md-5">
    <p class="ck-hint mb-2">Pendaftaran Peserta Didik</p>
    <h1 class="h3 mb-4">Lengkapi data sesuai identitas resmi</h1>

    <ol class="ck-steps">
        <li class="{{ $step === 1 ? 'is-active' : ($step > 1 ? 'is-done' : '') }}">Akun</li>
        <li class="{{ $step === 2 ? 'is-active' : ($step > 2 ? 'is-done' : '') }}">Data</li>
        <li class="{{ $step === 3 ? 'is-active' : '' }}">Bukti</li>
    </ol>

    @if ($errors->any())
        <div class="alert alert-ck mb-4" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    @if ($step === 1)
        <form wire:submit.prevent="simpanAkun" class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="nama">Nama lengkap</label>
                <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" wire:model.blur="nama" autocomplete="name">
                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="email">Email</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" wire:model.live.debounce.400ms="email" autocomplete="email">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6" x-data="{ show: false }">
                <label class="form-label" for="password">Password</label>
                <div class="input-group">
                    <input id="password" :type="show ? 'text' : 'password'" class="form-control @error('password') is-invalid @enderror" wire:model="password" autocomplete="new-password">
                    <button class="btn btn-outline-secondary" type="button" @click="show = !show" aria-label="Tampilkan password">
                        <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                    </button>
                </div>
                @error('password') <div class="text-danger ck-error small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password_confirmation">Ulangi password</label>
                <input id="password_confirmation" type="password" class="form-control" wire:model="password_confirmation" autocomplete="new-password">
            </div>
            <div class="col-12 d-flex justify-content-end pt-2">
                <button type="submit" class="btn btn-ck">Lanjut</button>
            </div>
        </form>
    @endif

    @if ($step === 2)
        <form wire:submit.prevent="simpanBiodata" class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="nik">NIK / No. KTP</label>
                <input id="nik" type="text" class="form-control @error('nik') is-invalid @enderror" wire:model.blur="nik">
                @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="nisn">NISN</label>
                <input id="nisn" type="text" class="form-control @error('nisn') is-invalid @enderror" wire:model.blur="nisn">
                @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="ibu">Nama ibu kandung</label>
                <input id="ibu" type="text" class="form-control @error('ibu') is-invalid @enderror" wire:model.blur="ibu">
                @error('ibu') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="tempat_lahir">Tempat lahir</label>
                <input id="tempat_lahir" type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" wire:model.blur="tempat_lahir">
                @error('tempat_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="tanggal_lahir">Tanggal lahir</label>
                <input id="tanggal_lahir" type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" wire:model.blur="tanggal_lahir">
                @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="alamat">Alamat</label>
                <input id="alamat" type="text" class="form-control @error('alamat') is-invalid @enderror" wire:model.blur="alamat">
                @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="sekolah">Asal sekolah</label>
                <input id="sekolah" type="text" class="form-control @error('sekolah') is-invalid @enderror" wire:model.blur="sekolah">
                @error('sekolah') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="status_sekolah">Status sekolah</label>
                <select id="status_sekolah" class="form-select @error('status_sekolah') is-invalid @enderror" wire:model="status_sekolah">
                    <option value="0">Belum Lulus</option>
                    <option value="1">Lulus</option>
                </select>
                @error('status_sekolah') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="wa">No. Telepon / WhatsApp</label>
                <input id="wa" type="text" class="form-control @error('wa') is-invalid @enderror" wire:model.blur="wa">
                @error('wa') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="wali">Nama wali</label>
                <input id="wali" type="text" class="form-control @error('wali') is-invalid @enderror" wire:model.blur="wali">
                @error('wali') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="wa_wali">WhatsApp wali</label>
                <input id="wa_wali" type="text" class="form-control @error('wa_wali') is-invalid @enderror" wire:model.blur="wa_wali">
                @error('wa_wali') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="markas_id">Markas yang dituju</label>
                <select id="markas_id" class="form-select @error('markas_id') is-invalid @enderror" wire:model="markas_id">
                    <option value="">Pilih markas</option>
                    @foreach ($markasList as $item)
                        <option value="{{ $item->id }}">{{ $item->markas }}</option>
                    @endforeach
                </select>
                @error('markas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12" x-data="ckFotoCropper">
                <label class="form-label" for="foto">Foto diri 3×4</label>
                <p class="ck-hint mb-2">Pilih foto, lalu potong sesuai bingkai 3:4. Hasil disimpan 600×800 px (maks. 500 Kb).</p>
                <input id="foto" type="file" class="form-control @error('foto') is-invalid @enderror" accept=".jpg,.jpeg,.png,image/jpeg,image/png" @change="open($event)">
                @error('foto') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                <div class="ck-photo-frame mt-3">
                    @if ($foto)
                        <img src="{{ $foto->temporaryUrl() }}" alt="Pratinjau foto">
                    @elseif ($existingFoto)
                        <img src="{{ asset('img/pelajar/'.$existingFoto) }}" alt="Foto pendaftar">
                    @else
                        <i class="bi bi-person fs-2 text-secondary"></i>
                    @endif
                </div>

                <div class="modal fade" tabindex="-1" wire:ignore x-ref="cropModal" aria-labelledby="cropFotoLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header border-0">
                                <h2 class="modal-title h5" id="cropFotoLabel">Potong foto 3×4</h2>
                                <button type="button" class="btn-close" @click="cancel()" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body pt-0">
                                <p class="ck-hint mb-3">Geser dan perbesar sampai wajah pas di dalam bingkai.</p>
                                <div class="ck-cropper-wrap">
                                    <img x-ref="cropImage" alt="Foto untuk dipotong">
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-ck-ghost" @click="cancel()">Batal</button>
                                <button type="button" class="btn btn-ck" @click="apply()" :disabled="uploading">
                                    <span x-show="!uploading">Gunakan foto ini</span>
                                    <span x-cloak x-show="uploading">Menyimpan…</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 d-flex justify-content-between pt-2">
                <button type="button" class="btn btn-ck-ghost" wire:click="kembali">Kembali</button>
                <button type="submit" class="btn btn-ck" wire:loading.attr="disabled">Simpan &amp; lihat bukti</button>
            </div>
        </form>
    @endif

    @if ($step === 3 && $receipt)
        <div class="border-0">
            <div class="ck-receipt-head d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <p class="ck-serif fs-4 mb-1">Bukti Pendaftaran</p>
                    <p class="small mb-0 opacity-75">Tunggu validasi admin. Nomor registrasi akan terbit setelah diterima.</p>
                </div>
                <div
                    id="qrcode"
                    class="ck-qr"
                    wire:ignore
                    data-qr-url="{{ $receiptUrl }}"
                    x-data
                    x-init="
                        if (typeof QRCode !== 'undefined' && !$el.dataset.ready) {
                            new QRCode($el, {
                                text: $el.dataset.qrUrl,
                                width: 132,
                                height: 132,
                                correctLevel: QRCode.CorrectLevel.M
                            });
                            $el.dataset.ready = '1';
                        }
                    "
                ></div>
            </div>
            <div class="p-4">
                @include('pendaftaran.partials.kartu-bukti', ['data' => $receipt])
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a class="btn btn-ck" href="{{ route('pendaftar.cetak-formulir-pdf', $receipt->id) }}" target="_blank">
                        <i class="bi bi-download me-1"></i> Unduh PDF
                    </a>
                    @if ($whatsappUrl)
                        <a class="btn btn-ck-ghost" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp me-1"></i> Hubungi admin
                        </a>
                    @endif
                    <button type="button" class="btn btn-ck-ghost" wire:click="kembali">Perbarui data</button>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    Alpine.data('ckFotoCropper', () => ({
        cropper: null,
        uploading: false,
        open(event) {
            const file = event.target.files?.[0];
            event.target.value = '';
            if (!file || !file.type.startsWith('image/')) {
                return;
            }
            if (typeof Cropper === 'undefined') {
                this.$wire.upload('foto', file);
                return;
            }

            const image = this.$refs.cropImage;
            const objectUrl = URL.createObjectURL(file);
            const modalEl = this.$refs.cropModal;
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

            const startCropper = () => {
                this.cropper?.destroy();
                this.cropper = new Cropper(image, {
                    aspectRatio: 3 / 4,
                    viewMode: 1,
                    autoCropArea: 1,
                    background: false,
                    dragMode: 'move',
                    responsive: true,
                });
            };

            modalEl.addEventListener('shown.bs.modal', startCropper, { once: true });
            modalEl.addEventListener('hidden.bs.modal', () => {
                this.cropper?.destroy();
                this.cropper = null;
                if (image.src.startsWith('blob:')) {
                    URL.revokeObjectURL(image.src);
                }
            }, { once: true });

            image.onload = () => modal.show();
            image.src = objectUrl;
        },
        cancel() {
            this.cropper?.destroy();
            this.cropper = null;
            bootstrap.Modal.getOrCreateInstance(this.$refs.cropModal).hide();
        },
        apply() {
            if (!this.cropper || this.uploading) {
                return;
            }
            this.uploading = true;
            this.cropper.getCroppedCanvas({
                width: 600,
                height: 800,
                imageSmoothingQuality: 'high',
            }).toBlob((blob) => {
                const cropped = new File([blob], 'foto-3x4.jpg', { type: 'image/jpeg' });
                this.$wire.upload('foto', cropped, () => {
                    this.uploading = false;
                    this.cancel();
                }, () => {
                    this.uploading = false;
                });
            }, 'image/jpeg', 0.88);
        },
    }));
</script>
@endscript
