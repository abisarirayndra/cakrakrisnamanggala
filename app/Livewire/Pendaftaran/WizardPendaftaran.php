<?php

namespace App\Livewire\Pendaftaran;

use App\Markas;
use App\Pelajar;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Intervention\Image\Facades\Image;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.guest-cakra')]
#[Title('Pendaftaran Peserta Didik')]
class WizardPendaftaran extends Component
{
    use WithFileUploads;

    public int $step = 1;

    public string $nama = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $tempat_lahir = '';

    public string $tanggal_lahir = '';

    public string $nik = '';

    public string $nisn = '';

    public string $ibu = '';

    public string $alamat = '';

    public string $sekolah = '';

    public int $status_sekolah = 0;

    public string $wa = '';

    public string $wali = '';

    public string $wa_wali = '';

    public $markas_id = '';

    public $foto = null;

    public ?string $existingFoto = null;

    public ?int $pelajarId = null;

    public string $receiptUrl = '';

    public function mount(): void
    {
        $user = Auth::user();

        if (!$user || !$user->isPendaftar()) {
            return;
        }

        $this->hydrateFromPendaftar($user);
    }

    public function simpanAkun(): void
    {
        $user = Auth::user();

        if ($user && $user->isPendaftar()) {
            $this->validate([
                'nama' => 'required',
            ], $this->akunMessages());

            $user->update(['nama' => $this->nama]);
            $this->step = 2;

            return;
        }

        $this->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
        ], $this->akunMessages());

        $user = User::create([
            'nama' => $this->nama,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role_id' => 5,
        ]);

        Pelajar::create([
            'pelajar_id' => $user->id,
        ]);

        Auth::login($user);

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        $this->password = '';
        $this->password_confirmation = '';
        $this->step = 2;
    }

    public function simpanBiodata(): void
    {
        $user = Auth::user();

        if (!$user || !$user->isPendaftar()) {
            $this->addError('nama', 'Silakan selesaikan langkah akun terlebih dahulu.');

            return;
        }

        $this->validate([
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'nik' => 'required',
            'nisn' => 'required',
            'wa' => 'required',
            'ibu' => 'required',
            'wali' => 'required',
            'wa_wali' => 'required',
            'foto' => $this->existingFoto
                ? 'nullable|mimes:jpg,jpeg,png|max:512'
                : 'required|mimes:jpg,jpeg,png|max:512',
            'markas_id' => 'required',
            'sekolah' => 'required',
            'status_sekolah' => 'required',
        ], $this->biodataMessages());

        $pelajar = Pelajar::firstOrCreate(['pelajar_id' => $user->id]);
        $imageName = $pelajar->foto;

        if ($this->foto) {
            if ($imageName && Storage::disk('pelajar_foto')->exists($imageName)) {
                Storage::disk('pelajar_foto')->delete($imageName);
            }

            $imageName = $this->storeFotoTigaEmpat($user->id);
        }

        $pelajar->update([
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'alamat' => $this->alamat,
            'sekolah' => $this->sekolah,
            'wa' => $this->wa,
            'wali' => $this->wali,
            'wa_wali' => $this->wa_wali,
            'foto' => $imageName,
            'markas_id' => $this->markas_id,
            'nik' => $this->nik,
            'nisn' => $this->nisn,
            'ibu' => $this->ibu,
            'status_sekolah' => (int) $this->status_sekolah,
        ]);

        $this->pelajarId = $pelajar->id;
        $this->existingFoto = $imageName;
        $this->receiptUrl = URL::signedRoute('pendaftar.bukti', $pelajar->id);
        $this->step = 3;
    }

    public function kembali(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function render(): View
    {
        $receipt = null;
        $whatsappUrl = null;

        if ($this->step === 3 && $this->pelajarId) {
            $receipt = Pelajar::buktiPendaftaran($this->pelajarId);
            $whatsappUrl = Markas::whatsappAdminForName($receipt->markas);
        }

        return view('livewire.pendaftaran.wizard-pendaftaran', [
            'markasList' => Markas::query()->orderBy('markas')->get(),
            'receipt' => $receipt,
            'whatsappUrl' => $whatsappUrl,
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function akunMessages(): array
    {
        return [
            'nama.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email salah ex. johndoe@gmail.com',
            'email.unique' => 'Email sudah terdaftar, coba login atau daftar dengan email lain',
            'password.required' => 'password harus diisi',
            'password.confirmed' => 'Password Tidak Cocok',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function biodataMessages(): array
    {
        return [
            'tempat_lahir.required' => 'Tempat lahir harus diisi',
            'tanggal_lahir.required' => 'Tanggal lahir harus diisi',
            'tanggal_lahir.date' => 'Format harus berupa tanggal',
            'alamat.required' => 'Alamat harus diisi',
            'nik.required' => 'NIK harus diisi',
            'nisn.required' => 'NISN harus diisi',
            'wa.required' => 'WA harus diisi',
            'wali.required' => 'Nama wali harus diisi',
            'wa_wali.required' => 'Nomor WA Wali harus disertakan',
            'ibu.required' => 'Nama Ibu harus diisi',
            'foto.required' => 'Foto harus diisi',
            'foto.mimes' => 'Format foto hanya jpg, jpeg, png !',
            'foto.max' => 'Ukuran file terlalu besar, max 500 Kb',
            'markas_id.required' => 'Markas belum dipilih',
            'sekolah.required' => 'Asal sekolah harus diisi',
            'status_sekolah.required' => 'Status sekolah harus dipilih',
        ];
    }

    private function storeFotoTigaEmpat(int $userId): string
    {
        $imageName = 'Pelajar'.$userId.'.jpg';
        $image = Image::make($this->foto->getRealPath())
            ->orientate()
            ->fit(600, 800);

        Storage::disk('pelajar_foto')->put($imageName, (string) $image->encode('jpg', 85));

        return $imageName;
    }

    private function hydrateFromPendaftar(User $user): void
    {
        $this->nama = $user->nama;
        $this->email = (string) $user->email;

        $pelajar = $user->pelajar;

        if (!$pelajar) {
            $this->step = 2;

            return;
        }

        $this->pelajarId = $pelajar->id;
        $this->tempat_lahir = (string) $pelajar->tempat_lahir;
        $this->tanggal_lahir = optional($pelajar->tanggal_lahir)->format('Y-m-d') ?? '';
        $this->nik = (string) $pelajar->nik;
        $this->nisn = (string) $pelajar->nisn;
        $this->ibu = (string) $pelajar->ibu;
        $this->alamat = (string) $pelajar->alamat;
        $this->sekolah = (string) $pelajar->sekolah;
        $this->status_sekolah = (int) $pelajar->status_sekolah;
        $this->wa = (string) $pelajar->wa;
        $this->wali = (string) $pelajar->wali;
        $this->wa_wali = (string) $pelajar->wa_wali;
        $this->markas_id = $pelajar->markas_id ?? '';
        $this->existingFoto = $pelajar->foto;

        if ($pelajar->tempat_lahir && $pelajar->nik) {
            $this->receiptUrl = URL::signedRoute('pendaftar.bukti', $pelajar->id);
            $this->step = 3;
        } else {
            $this->step = 2;
        }
    }
}
