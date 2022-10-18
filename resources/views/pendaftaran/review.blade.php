<!DOCTYPE html>
<html class="no-js" lang="en">
    <head>
    <title>Bukti Pendaftaran</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

    </head>

<body>
    <div class="container">

            <center>
                <img src="{!!$logo!!}" width="80" alt="">
                <div style="font-style: bold; margin-bottom:10px; font-size:14pt;">Lembaga Kursus Dan Pelatihan (LKP)</div>
                <div style="font-style: bold; margin-bottom:10px; font-size:14pt;">Cakra Krisna Manggala</div>
                <div style="margin-bottom:10px; font-size:12pt;">Data Pendaftar Cakra Krisna Manggala</div>
            </center>

                        <div style="margin-bottom:25px; margin-top:25px;">
                            <center>
                                <img src="{!!$foto!!}" width="100" alt="">
                            </center>
                        </div>
                        <div class="row mt-4">
                            <center>
                                <table>
                                    <tr>
                                        <td><b>Nama</b></td>
                                        <td class="pl-4">{{$data->nama}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Email</b></td>
                                        <td class="pl-4">{{$data->email}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Tempat, Tanggal Lahir</b></td>
                                        <td class="pl-4">{{$data->tempat_lahir}}, {{\Carbon\Carbon::parse($data->tanggal_lahir)->isoFormat('D MMMM Y')}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>NIK</b></td>
                                        <td class="pl-4">{{$data->nik}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>NISN</b></td>
                                        <td class="pl-4">{{$data->nisn}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Nama Ibu Kandung</b></td>
                                        <td class="pl-4">{{$data->ibu}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Alamat</b></td>
                                        <td class="pl-4">{{$data->alamat}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Asal Sekolah</b></td>
                                        <td class="pl-4">{{$data->sekolah}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Status Sekolah</b></td>
                                        @if ($data->status_sekolah == 0)
                                            <td class="pl-4">Belum Lulus</td>
                                        @elseif ($data->status_sekolah  == 1)
                                            <td class="pl-4">Lulus</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td><b>No. Telepon/WhatsApp</b></td>
                                        <td class="pl-4">{{$data->wa}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Nama Wali</b></td>
                                        <td class="pl-4">{{$data->wali}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>No. Telepon/Whatsapp Wali</b></td>
                                        <td class="pl-4">{{$data->wa_wali}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Markas</b></td>
                                        <td class="pl-4">{{$data->markas}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Tanggal Daftar</b></td>
                                        <td class="pl-4">{{\Carbon\Carbon::parse($data->created_at)->isoFormat('dddd, D MMMM Y HH:mm')}}</td>
                                    </tr>
                                </table>
                            </center>
                        </div>
                        <div style="margin-top: 25px">
                            <div style="margin-bottom:10px; font-size:12pt; font-style: bold">Tahapan Pendaftaran</div>
                            <ul>
                                <li>Datang ke lokasi markas yang dipilih dengan membawa bukti pendaftaran yang sudah dicetak</li>
                                <li>Melakukan proses administrasi selanjutnya di markas</li>
                            </ul>
                        </div>


    </div>

</body>

</html>
