<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use App\Pendidik;
use App\Mapel;
use Validator;
use Alert;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Image;

class PengajarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $akun = Auth::user();
        $user = $akun->nama;
        $nomor_registrasi = $akun->nomor_registrasi;
        $data = Pendidik::select('users.nama','adm_pendidik.tempat_lahir','adm_pendidik.tanggal_lahir',
                                'adm_pendidik.alamat','adm_pendidik.nik','adm_pendidik.nip','mapels.mapel',
                                'adm_pendidik.wa','adm_pendidik.ibu','adm_pendidik.foto','adm_pendidik.cv',
                                'adm_pendidik.markas','adm_pendidik.status_dapodik','adm_pendidik.created_at')
                ->join('users','users.id','=','adm_pendidik.pendidik_id')
                ->join('mapels','mapels.id','=','adm_pendidik.mapel_id')
                ->where('adm_pendidik.pendidik_id', $akun->id)
                ->firstOrFail();

        return view('pendidik.dinas.beranda', compact('user', 'data', 'nomor_registrasi'));
    }

    public function kartuAbsensi()
    {
        $akun = Auth::user();
        abort_unless(filled($akun->nomor_registrasi), 404);

        $data = Pendidik::with('mapel')->where('pendidik_id', $akun->id)->first();
        $pdf = Pdf::loadView('pendidik.kartu-absensi-pdf', [
            'nama' => $akun->nama,
            'mapelNama' => $data?->mapel?->mapel,
            'nomor_registrasi' => $akun->nomor_registrasi,
            'qr' => $this->qrDataUri($akun->nomor_registrasi),
            'foto' => $this->imageDataUri(public_path('pendidik/img/'.($data?->foto ?? ''))),
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

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

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
    public function edit()
    {
        $user = Auth::user()->nama;
        $mapel = Mapel::select('id','mapel')->get();
        return view('pendidik.dinas.edit-data', compact('user', 'mapel'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //validate data
        $validator = Validator::make($request->all(), [
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'nik' => 'required',
            'mapel_id' => 'required',
            'wa' => 'required',
            'ibu' => 'required',
            'foto' => 'required|mimes:jpg,jpeg,png|max:512',
            'markas' => 'required'
        ],[
            'tempat_lahir.required' => 'Tempat lahir harus diisi',
            'tanggal_lahir.required' => 'Tanggal lahir harus diisi',
            'tanggal_lahir.date' => 'Format harus berupa tanggal',
            'alamat.required' => 'Alamat harus diisi',
            'nik.required' => 'NIK harus diisi',
            'mapel_id.required' => 'Mapel harus diisi',
            'wa.required' => 'WA harus diisi',
            'ibu.required' => 'Nama Ibu harus diisi',
            'foto.required' => 'Foto harus diisi',
            'foto.mimes' => 'Format foto hanya jpg, jpeg, png !',
            'foto.max' => 'Ukuran file terlalu besar, max 500 Kb',
            'markas.required' => 'Markas belum dipilih'
        ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }else{
                $id = Auth::user()->id;
                $data = Pendidik::where('pendidik_id', $id)->first();
                // if($request->file('cv')){
                //     $cv = $request->file('cv');
                //     $nama_file = 'cv'.$id.'.'.$request->file('cv')->extension();
                //     $path = public_path('pendidik/cv/');
                //     $cv->move($path, $nama_file);
                // }
                if($request->file('foto')){
                    $foto = $request->file('foto');
                    $nama_foto = 'pendidik'.$id.'.'.$request->file('foto')->extension();
                    $path = public_path('pendidik/img/');
                    $foto->move($path, $nama_foto);
                }
                $data->update([
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'alamat' => $request->alamat,
                    'nik' => $request->nik,
                    'nip' => $request->nip,
                    'mapel_id' => $request->mapel_id,
                    'wa' => $request->wa,
                    'ibu' => $request->ibu,
                    'foto' => $nama_foto,
                    // 'cv' => $nama_file,
                ]);



                Alert::toast('Pembaruan data diri berhasil','success');
                return redirect()->route('pendidik.dinas.beranda');
            }


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
