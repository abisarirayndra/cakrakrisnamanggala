<?php

namespace Database\Seeders;

use App\BankOpsi;
use App\BankPaket;
use App\BankSoal;
use App\CatJadwal;
use App\CatJawaban;
use App\CatSesi;
use App\Kelas;
use App\Pelajar;
use App\Pendidik;
use App\Support\BankSoalBentuk;
use App\Support\BankSoalTipe;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CatBankSoalSeeder extends Seeder
{
    public const TOKEN = 'SEED01';

    public const PASSWORD = 'password';

    public const EMAIL_ALPHA = 'seed.cat.alpha@cakra.test';

    public const EMAIL_BETA = 'seed.cat.beta@cakra.test';

    public const EMAIL_SELESAI_A = 'seed.cat.selesai.a@cakra.test';

    public const EMAIL_SELESAI_B = 'seed.cat.selesai.b@cakra.test';

    public function run(): void
    {
        $guru = $this->pendidikUji();
        $admin = User::query()->where('role_id', 2)->orderBy('id')->first();

        if (! $guru || ! $admin) {
            $this->command?->error('Butuh minimal 1 pendidik dan 1 admin di database.');

            return;
        }

        $mapelId = $guru->pendidik?->mapel_id;
        $tunggal = $this->buatPaket($guru, $mapelId, '[SEED] Jawaban Tunggal', BankSoalTipe::TUNGGAL, BankSoalBentuk::BIASA);
        $this->isiTunggal($tunggal, $this->bankTunggalBiasa(), 20);

        $matematis = $this->buatPaket($guru, $mapelId, '[SEED] Matematis', BankSoalTipe::TUNGGAL, BankSoalBentuk::MATEMATIS);
        $this->isiTunggal($matematis, $this->bankTunggalMatematis(), 10);

        $bobot = $this->buatPaket($guru, $mapelId, '[SEED] Pembobotan', BankSoalTipe::PEMBOBOTAN, BankSoalBentuk::BIASA);
        $this->isiPembobotan($bobot, $this->bankPembobotan(), 15);

        $jadwal = CatJadwal::query()->updateOrCreate(
            ['token' => self::TOKEN],
            [
                'admin_id' => $admin->id,
                'nama' => '[SEED] UTS Uji Coba',
                'mulai' => now()->subHour(),
                'selesai' => now()->addHours(4),
            ]
        );
        $jadwal->pasangBanks([$tunggal->id, $matematis->id, $bobot->id]);

        $markasId = $guru->pendidik?->markas_id;
        $kelasA = $this->buatKelas('[SEED] Kelas A', $markasId);
        $kelasB = $this->buatKelas('[SEED] Kelas B', $markasId);
        $alpha = $this->buatPelajarUji(self::EMAIL_ALPHA, 'Siswa Seed Alpha', $kelasA->id, $markasId);
        $beta = $this->buatPelajarUji(self::EMAIL_BETA, 'Siswa Seed Beta', $kelasB->id, $markasId);
        $selesaiA = $this->buatPelajarUji(self::EMAIL_SELESAI_A, 'Siswa Seed Selesai A', $kelasA->id, $markasId);
        $selesaiB = $this->buatPelajarUji(self::EMAIL_SELESAI_B, 'Siswa Seed Selesai B', $kelasB->id, $markasId);
        $this->isiSesiUji($jadwal, $alpha, $beta);
        $this->isiSesiSelesai($jadwal, $selesaiA, 'tinggi');
        $this->isiSesiSelesai($jadwal, $selesaiB, 'rendah');

        $this->command?->info('Seed CAT siap. Pendidik: '.$guru->nama);
        $this->command?->info('Paket: '.$jadwal->nama.' · 3 bank soal · token '.$jadwal->token);
        $this->command?->info('Pelajar uji berjalan: '.self::EMAIL_ALPHA.' / '.self::EMAIL_BETA.' (password: '.self::PASSWORD.')');
        $this->command?->info('Pelajar uji selesai: '.self::EMAIL_SELESAI_A.' / '.self::EMAIL_SELESAI_B.' (password: '.self::PASSWORD.')');
    }

    private function buatKelas(string $nama, ?int $markasId): Kelas
    {
        return Kelas::query()->updateOrCreate(
            ['nama' => $nama],
            ['markas_id' => $markasId]
        );
    }

    private function buatPelajarUji(string $email, string $nama, ?int $kelasId, ?int $markasId): User
    {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'nama' => $nama,
                'password' => Hash::make(self::PASSWORD),
                'role_id' => 4,
                'kelas_id' => $kelasId,
            ]
        );

        Pelajar::query()->updateOrCreate(
            ['pelajar_id' => $user->id],
            ['markas_id' => $markasId]
        );

        return $user;
    }

    private function isiSesiUji(CatJadwal $jadwal, User $alpha, User $beta): void
    {
        $bankPertama = $jadwal->fresh()->banks->first();
        $sesiAlpha = CatSesi::query()->updateOrCreate(
            ['jadwal_id' => $jadwal->id, 'pelajar_id' => $alpha->id, 'bank_paket_id' => $bankPertama->id],
            [
                'status' => CatSesi::BERJALAN,
                'nilai' => null,
                'started_at' => now(),
                'submitted_at' => null,
            ]
        );

        $pertama = $jadwal->fresh()->daftarSoalBank((int) $bankPertama->id)->first();

        if ($pertama instanceof BankSoal) {
            CatJawaban::query()->updateOrCreate(
                ['sesi_id' => $sesiAlpha->id, 'bank_soal_id' => $pertama->id],
                [
                    'kode' => $pertama->kunci ?: 'A',
                    'poin' => (int) ($pertama->poin ?? 0),
                ]
            );
        }

        CatSesi::query()->updateOrCreate(
            ['jadwal_id' => $jadwal->id, 'pelajar_id' => $beta->id, 'bank_paket_id' => $bankPertama->id],
            [
                'status' => CatSesi::SELESAI,
                'nilai' => 0,
                'started_at' => now()->subMinutes(20),
                'submitted_at' => now()->subMinutes(5),
            ]
        );
    }

    private function isiSesiSelesai(CatJadwal $jadwal, User $pelajar, string $pola): void
    {
        $jadwal->loadMissing(['banks.soal.opsi']);

        foreach ($jadwal->banks as $index => $bank) {
            $sesi = CatSesi::query()->updateOrCreate(
                [
                    'jadwal_id' => $jadwal->id,
                    'pelajar_id' => $pelajar->id,
                    'bank_paket_id' => $bank->id,
                ],
                [
                    'status' => CatSesi::SELESAI,
                    'started_at' => now()->subHours(2)->addMinutes($index * 10),
                    'submitted_at' => now()->subHour()->addMinutes($index * 10),
                ]
            );
            $sesi->jawaban()->delete();

            $nilai = 0;
            foreach ($jadwal->daftarSoalBank((int) $bank->id) as $soal) {
                [$kode, $poin] = $this->pilihJawabanSeed($soal, $bank->tipe, $pola);
                CatJawaban::query()->create([
                    'sesi_id' => $sesi->id,
                    'bank_soal_id' => $soal->id,
                    'kode' => $kode,
                    'poin' => $poin,
                ]);
                $nilai += $poin;
            }

            $sesi->update(['nilai' => $nilai, 'status' => CatSesi::SELESAI]);
        }
    }

    private function pilihJawabanSeed(BankSoal $soal, string $tipe, string $pola): array
    {
        $soal->loadMissing('opsi');

        if ($tipe === BankSoalTipe::PEMBOBOTAN) {
            $opsi = $pola === 'tinggi'
                ? $soal->opsi->sortByDesc('poin')->first()
                : $soal->opsi->sortBy('poin')->first();

            return [$opsi?->kode ?: 'A', (int) ($opsi?->poin ?? 0)];
        }

        if ($pola === 'tinggi') {
            return [$soal->kunci ?: 'A', (int) ($soal->poin ?? 0)];
        }

        $salah = $soal->opsi->first(fn (BankOpsi $opsi) => $opsi->kode !== $soal->kunci);

        return [$salah?->kode ?: 'A', 0];
    }

    private function pendidikUji(): ?User
    {
        $id = Pendidik::query()
            ->whereNotNull('mapel_id')
            ->orderBy('pendidik_id')
            ->value('pendidik_id');

        if ($id) {
            return User::query()->where('role_id', 3)->whereKey($id)->first();
        }

        return User::query()->where('role_id', 3)->orderBy('id')->first();
    }

    private function buatPaket(User $guru, ?int $mapelId, string $nama, string $tipe, string $bentuk): BankPaket
    {
        $paket = BankPaket::query()->updateOrCreate(
            [
                'pendidik_id' => $guru->id,
                'nama' => $nama,
            ],
            [
                'mapel_id' => $mapelId,
                'tipe' => $tipe,
                'bentuk' => $bentuk,
            ]
        );

        foreach ($paket->soal()->with('opsi')->get() as $soal) {
            $soal->opsi()->delete();
            $soal->delete();
        }

        return $paket->fresh();
    }

    private function isiTunggal(BankPaket $paket, array $bank, int $jumlah): void
    {
        foreach ($this->acakAmbil($bank, $jumlah) as $item) {
            $kunci = $item['kunci'];
            $soal = BankSoal::create([
                'paket_id' => $paket->id,
                'pendidik_id' => $paket->pendidik_id,
                'mapel_id' => $paket->mapel_id,
                'soal' => $item['soal'],
                'poin' => $item['poin'] ?? 5,
                'kunci' => $kunci,
            ]);
            $this->simpanOpsi($soal, $item['opsi']);
        }
    }

    private function isiPembobotan(BankPaket $paket, array $bank, int $jumlah): void
    {
        foreach ($this->acakAmbil($bank, $jumlah) as $item) {
            $soal = BankSoal::create([
                'paket_id' => $paket->id,
                'pendidik_id' => $paket->pendidik_id,
                'mapel_id' => $paket->mapel_id,
                'soal' => $item['soal'],
                'poin' => null,
                'kunci' => null,
            ]);
            $this->simpanOpsi($soal, $item['opsi'], $item['poin'] ?? []);
        }
    }

    private function simpanOpsi(BankSoal $soal, array $opsi, array $poin = []): void
    {
        $urutan = 1;
        foreach ($opsi as $kode => $teks) {
            BankOpsi::create([
                'bank_soal_id' => $soal->id,
                'kode' => $kode,
                'teks' => $teks,
                'poin' => $poin[$kode] ?? 0,
                'urutan' => $urutan++,
            ]);
        }
    }

    private function acakAmbil(array $bank, int $jumlah): array
    {
        shuffle($bank);

        if (count($bank) >= $jumlah) {
            return array_slice($bank, 0, $jumlah);
        }

        $hasil = $bank;
        while (count($hasil) < $jumlah) {
            $hasil[] = $bank[array_rand($bank)];
        }

        return $hasil;
    }

    private function bankTunggalBiasa(): array
    {
        return [
            ['soal' => 'Ibu kota negara Indonesia adalah …', 'kunci' => 'A', 'opsi' => ['A' => 'Jakarta', 'B' => 'Bandung', 'C' => 'Surabaya', 'D' => 'Medan']],
            ['soal' => 'Lambang negara Indonesia adalah …', 'kunci' => 'A', 'opsi' => ['A' => 'Garuda Pancasila', 'B' => 'Burung Merak', 'C' => 'Harimau Sumatera', 'D' => 'Komodo']],
            ['soal' => 'Sila pertama Pancasila berbunyi …', 'kunci' => 'A', 'opsi' => ['A' => 'Ketuhanan Yang Maha Esa', 'B' => 'Kemanusiaan yang adil dan beradab', 'C' => 'Persatuan Indonesia', 'D' => 'Keadilan sosial bagi seluruh rakyat Indonesia']],
            ['soal' => 'UUD 1945 disahkan pada tanggal …', 'kunci' => 'C', 'opsi' => ['A' => '17 Agustus 1945', 'B' => '1 Juni 1945', 'C' => '18 Agustus 1945', 'D' => '22 Juni 1945']],
            ['soal' => 'Lembaga yang berwenang membuat undang-undang adalah …', 'kunci' => 'B', 'opsi' => ['A' => 'MA', 'B' => 'DPR', 'C' => 'MK', 'D' => 'KY']],
            ['soal' => 'Presiden dipilih untuk masa jabatan … tahun.', 'kunci' => 'A', 'opsi' => ['A' => '5', 'B' => '4', 'C' => '6', 'D' => '7']],
            ['soal' => 'Bhinneka Tunggal Ika berarti …', 'kunci' => 'D', 'opsi' => ['A' => 'Bersatu kita teguh', 'B' => 'Satu nusa satu bangsa', 'C' => 'Gotong royong', 'D' => 'Berbeda-beda tetapi tetap satu']],
            ['soal' => 'Pahlawan yang memproklamasikan kemerdekaan bersama Hatta adalah …', 'kunci' => 'A', 'opsi' => ['A' => 'Soekarno', 'B' => 'Sudirman', 'C' => 'Hatma', 'D' => 'Diponegoro']],
            ['soal' => 'Pulau terbesar di Indonesia adalah …', 'kunci' => 'C', 'opsi' => ['A' => 'Jawa', 'B' => 'Sumatera', 'C' => 'Kalimantan', 'D' => 'Sulawesi']],
            ['soal' => 'Mata uang Indonesia adalah …', 'kunci' => 'B', 'opsi' => ['A' => 'Ringgit', 'B' => 'Rupiah', 'C' => 'Dollar', 'D' => 'Peso']],
            ['soal' => 'Organisasi ASEAN didirikan pada tahun …', 'kunci' => 'A', 'opsi' => ['A' => '1967', 'B' => '1945', 'C' => '1950', 'D' => '1998']],
            ['soal' => 'Gunung tertinggi di Indonesia adalah …', 'kunci' => 'D', 'opsi' => ['A' => 'Merapi', 'B' => 'Semeru', 'C' => 'Kerinci', 'D' => 'Puncak Jaya']],
            ['soal' => 'Hari Pendidikan Nasional diperingati setiap …', 'kunci' => 'B', 'opsi' => ['A' => '1 Mei', 'B' => '2 Mei', 'C' => '20 Mei', 'D' => '17 Agustus']],
            ['soal' => 'Lagu kebangsaan Indonesia adalah …', 'kunci' => 'A', 'opsi' => ['A' => 'Indonesia Raya', 'B' => 'Garuda Pancasila', 'C' => 'Halo-halo Bandung', 'D' => 'Bagimu Negeri']],
            ['soal' => 'Majelis yang mengubah UUD adalah …', 'kunci' => 'C', 'opsi' => ['A' => 'DPR', 'B' => 'DPD', 'C' => 'MPR', 'D' => 'MA']],
            ['soal' => 'Contoh hak asasi manusia adalah …', 'kunci' => 'A', 'opsi' => ['A' => 'Hak hidup', 'B' => 'Hak menipu', 'C' => 'Hak merusak', 'D' => 'Hak memaksa']],
            ['soal' => 'Sistem pemerintahan Indonesia adalah …', 'kunci' => 'B', 'opsi' => ['A' => 'Parlementer', 'B' => 'Presidensial', 'C' => 'Monarki', 'D' => 'Federal']],
            ['soal' => 'Simbol sila kedua Pancasila adalah …', 'kunci' => 'A', 'opsi' => ['A' => 'Rantai', 'B' => 'Pohon beringin', 'C' => 'Kepala banteng', 'D' => 'Padi dan kapas']],
            ['soal' => 'Negara yang berbatasan darat dengan Indonesia di Kalimantan adalah …', 'kunci' => 'C', 'opsi' => ['A' => 'Thailand', 'B' => 'Filipina', 'C' => 'Malaysia', 'D' => 'Singapura']],
            ['soal' => 'KPK bertugas memberantas …', 'kunci' => 'B', 'opsi' => ['A' => 'Terorisme', 'B' => 'Korupsi', 'C' => 'Narkoba', 'D' => 'Pencurian']],
            ['soal' => 'Jumlah provinsi Indonesia saat ini lebih dari …', 'kunci' => 'A', 'opsi' => ['A' => '30', 'B' => '10', 'C' => '15', 'D' => '20']],
            ['soal' => 'Dasar negara Indonesia adalah …', 'kunci' => 'D', 'opsi' => ['A' => 'UUD 1945', 'B' => 'GBHN', 'C' => 'Proklamasi', 'D' => 'Pancasila']],
            ['soal' => 'Tokoh yang dijuluki Bapak Pendidikan Nasional adalah …', 'kunci' => 'B', 'opsi' => ['A' => 'Soekarno', 'B' => 'Ki Hajar Dewantara', 'C' => 'Hatta', 'D' => 'Kartini']],
            ['soal' => 'Warna bendera Indonesia adalah …', 'kunci' => 'A', 'opsi' => ['A' => 'Merah putih', 'B' => 'Merah kuning', 'C' => 'Biru putih', 'D' => 'Hijau putih']],
            ['soal' => 'Sidang BPUPKI pertama membahas …', 'kunci' => 'C', 'opsi' => ['A' => 'Pemilu', 'B' => 'APBN', 'C' => 'Dasar negara', 'D' => 'Perdagangan']],
        ];
    }

    private function bankTunggalMatematis(): array
    {
        return [
            ['soal' => 'Hasil dari $1 + 1$ adalah …', 'kunci' => 'B', 'opsi' => ['A' => '$1$', 'B' => '$2$', 'C' => '$3$', 'D' => '$0$']],
            ['soal' => 'Hasil dari $\frac{1}{2} + \frac{1}{2}$ adalah …', 'kunci' => 'A', 'opsi' => ['A' => '$1$', 'B' => '$\frac{1}{4}$', 'C' => '$2$', 'D' => '$0$']],
            ['soal' => 'Nilai dari $3^2$ adalah …', 'kunci' => 'C', 'opsi' => ['A' => '$6$', 'B' => '$5$', 'C' => '$9$', 'D' => '$8$']],
            ['soal' => 'Akar dari $16$ adalah …', 'kunci' => 'A', 'opsi' => ['A' => '$4$', 'B' => '$8$', 'C' => '$2$', 'D' => '$6$']],
            ['soal' => 'Hasil dari $10 \times 0$ adalah …', 'kunci' => 'D', 'opsi' => ['A' => '$10$', 'B' => '$1$', 'C' => '$100$', 'D' => '$0$']],
            ['soal' => 'Hasil dari $\frac{8}{2}$ adalah …', 'kunci' => 'B', 'opsi' => ['A' => '$2$', 'B' => '$4$', 'C' => '$6$', 'D' => '$16$']],
            ['soal' => 'Jika $x + 5 = 12$ maka $x$ adalah …', 'kunci' => 'A', 'opsi' => ['A' => '$7$', 'B' => '$17$', 'C' => '$5$', 'D' => '$12$']],
            ['soal' => 'Keliling persegi sisi $4$ adalah …', 'kunci' => 'C', 'opsi' => ['A' => '$8$', 'B' => '$12$', 'C' => '$16$', 'D' => '$4$']],
            ['soal' => 'Hasil dari $2^3$ adalah …', 'kunci' => 'B', 'opsi' => ['A' => '$6$', 'B' => '$8$', 'C' => '$9$', 'D' => '$5$']],
            ['soal' => 'Nilai dari $\pi$ paling dekat dengan …', 'kunci' => 'A', 'opsi' => ['A' => '$3{,}14$', 'B' => '$2{,}14$', 'C' => '$4{,}14$', 'D' => '$1{,}41$']],
            ['soal' => 'Hasil dari $15 - 7$ adalah …', 'kunci' => 'D', 'opsi' => ['A' => '$6$', 'B' => '$7$', 'C' => '$9$', 'D' => '$8$']],
            ['soal' => 'Luas persegi panjang $5 \times 3$ adalah …', 'kunci' => 'A', 'opsi' => ['A' => '$15$', 'B' => '$8$', 'C' => '$16$', 'D' => '$10$']],
        ];
    }

    private function bankPembobotan(): array
    {
        $soal = [
            'Jika melihat teman menyontek, sikap terbaik Anda adalah …',
            'Saat ditugaskan ketua kelompok, Anda akan …',
            'Teman Anda terlambat mengumpulkan tugas. Anda …',
            'Jika ada perbedaan pendapat dalam diskusi, Anda …',
            'Ketika melihat sampah di kelas, Anda …',
            'Jika diminta membantu panitia kegiatan, Anda …',
            'Saat mendapat kritik dari pendidik, Anda …',
            'Jika ada teman yang kesulitan belajar, Anda …',
            'Ketika jadwal tes diubah mendadak, Anda …',
            'Jika menemukan uang di lantai kelas, Anda …',
            'Saat kerja kelompok macet, Anda …',
            'Jika diminta menjadi juru bicara kelas, Anda …',
            'Ketika melihat pelanggaran tata tertib, Anda …',
            'Jika nilai ujian kurang memuaskan, Anda …',
            'Saat ada teman yang di-bully, Anda …',
            'Jika tugas menumpuk, Anda …',
            'Ketika rapat kelas berlangsung, Anda …',
            'Jika diminta menggantikan teman yang berhalangan, Anda …',
        ];

        $opsiDasar = [
            'A' => 'Langsung bertindak dengan tanggung jawab',
            'B' => 'Membahas dulu dengan teman terdekat',
            'C' => 'Menunggu arahan orang lain',
            'D' => 'Mengabaikan karena bukan urusan saya',
            'E' => 'Menolak dan mencari alasan',
        ];

        $hasil = [];
        foreach ($soal as $teks) {
            $kode = array_keys($opsiDasar);
            shuffle($kode);
            $opsi = [];
            $poin = [];
            $nilai = [5, 4, 3, 2, 1];
            foreach ($kode as $i => $huruf) {
                $opsi[$huruf] = $opsiDasar[$huruf];
                $poin[$huruf] = $nilai[$i];
            }
            ksort($opsi);
            $poinUrut = [];
            foreach (array_keys($opsi) as $huruf) {
                $poinUrut[$huruf] = $poin[$huruf];
            }
            $hasil[] = [
                'soal' => $teks,
                'opsi' => $opsi,
                'poin' => $poinUrut,
            ];
        }

        return $hasil;
    }
}
