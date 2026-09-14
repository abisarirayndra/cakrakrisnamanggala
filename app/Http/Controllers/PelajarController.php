<?php

namespace App\Http\Controllers;

use App\AbsensiPelajar;
use App\Pelajar;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Image;

class PelajarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = Auth::user()->id;
        $akun = Auth::user()->load('kelas');
        $user = $akun->nama;
        $nomor_registrasi = $akun->nomor_registrasi;
        $kelasNama = $akun->kelas?->nama;
        $data = Pelajar::where('pelajar_id',$id)->first();
        $jumlah_ontime = AbsensiPelajar::where('pelajar_id', $id)->where('status', 1)->count();
        $jumlah_terlambat = AbsensiPelajar::where('pelajar_id', $id)->where('status', 0)->count();
        $jumlah_izin = AbsensiPelajar::where('pelajar_id', $id)->where('status', 2)->count();

        return view('pelajar.beranda', compact(
            'data',
            'user',
            'nomor_registrasi',
            'kelasNama',
            'jumlah_ontime',
            'jumlah_terlambat',
            'jumlah_izin'
        ));
    }

    public function kartuAbsensi()
    {
        $akun = Auth::user()->load('kelas');
        abort_unless(filled($akun->nomor_registrasi), 404);

        $data = Pelajar::where('pelajar_id', $akun->id)->first();
        $pdf = Pdf::loadView('pelajar.kartu-absensi-pdf', [
            'nama' => $akun->nama,
            'kelasNama' => $akun->kelas?->nama,
            'nomor_registrasi' => $akun->nomor_registrasi,
            'qr' => $this->qrDataUri($akun->nomor_registrasi),
            'foto' => $this->imageDataUri(public_path('img/pelajar/'.($data?->foto ?? ''))),
        ])->setPaper([0, 0, 243.78, 153.07]);

        return $pdf->download('kartu-absensi-'.$akun->nomor_registrasi.'.pdf');
    }

    private function qrDataUri(string $token): string
    {
        $options = new QROptions;
        $options->outputInterface = QRGdImagePNG::class;
        $options->eccLevel = EccLevel::H;
        $options->scale = 8;
        $options->outputBase64 = true;

        return (new QRCode($options))->render($token);
    }

    private function imageDataUri(string $path): ?string
    {
        if ($path === '' || ! is_file($path)) {
            return null;
        }

        try {
            return (string) Image::make($path)->encode('data-url');
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
