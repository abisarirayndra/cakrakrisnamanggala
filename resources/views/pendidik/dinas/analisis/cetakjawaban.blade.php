<!DOCTYPE html>
<html>
<head>
	<title>Computer Assisted Test - Cakra Krisna Manggala</title>
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link media="all" type="text/css" rel="stylesheet" href="{{ ltrim(public_path('css/sb-admin-2.min.css'), '/') }}">
</head>
<body>
	<style type="text/css">

        table tr td,
		table tr th{
			font-size: 9pt;
            color: #000;

		}
        body{
            color: #000;
        }
        p{
            font-size: 9pt;
        }
	</style>
	<center>
		<img src="{!!$logo!!}" width="80" alt="">
                <h5 style="margin-bottom: 15px">Cakra Krisna Manggala</h5>
                <div class="container">
                    <div class="row p-3">
                        <div class="col-6">
                            <table>
                                <tr>
                                   <td><b>Nama Pelajar<b></td>
                                   <td class="pl-3">{{ $pelajar->pelajar }}</td>
                                </tr>
                                <tr>
                                    <td><b>Kelas<b></td>
                                    <td class="pl-3">{{ $pelajar->kelas }}</td>
                                </tr>
                                <tr>
                                    <td><b>Jumlah Soal<b></td>
                                    <td class="pl-3">{{ $soal }}</td>
                                </tr>
                                <tr>
                                    <td><b>Jumlah Terjawab<b></td>
                                    <td class="pl-3">{{ $soal_terjawab }}</td>
                                </tr>
                                <tr>
                                    <td><b>Nilai<b></td>
                                    <td class="pl-3">{{ $nilai->nilai }}</td>
                                </tr>
                                <tr>
                                    <td><b>Nilai Akumulasi<b></td>
                                    <td class="pl-3">{{ $nilai->akumulasi }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
	</center>

    <div class="row p-3">
        <div class="container">
            <table>
                @foreach ($jawaban as $item)
                <tr>
                    <td style="vertical-align: top;"><b>{{$item->nomor_soal}}</b></td>
                    <td class="pl-4 pb-4">
                        <div style="text-align: justify"><b>{!!$item->soal!!}</b> @if($item->nilai == 0) <div style="color: rgb(209, 52, 52)"><b>(Salah)</b></div> @else <div style="color: rgb(97, 209, 52)"><b>(Benar)</b></div> @endif</div> <br>
                        <div class="form-group">
                            <div class="form-check">
                              <input class="form-check-input" type="radio" @if($item->jawaban == "A") {{'checked="checked"'}} @endif>
                              <label class="form-check-label">
                                @if ( $item->kunci == "A")
                                <div style="color: rgb(97, 209, 52)">
                                    {!!$item->opsi_a!!}
                                </div>
                                @else
                                    {!!$item->opsi_a!!}
                                @endif
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" @if($item->jawaban == "B") {{'checked="checked"'}} @endif>
                              <label class="form-check-label">
                                @if ( $item->kunci == "B")
                                <div style="color: rgb(97, 209, 52)">
                                    {!!$item->opsi_b!!}
                                </div>
                                @else
                                    {!!$item->opsi_b!!}
                                @endif
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" @if($item->jawaban == "C") {{'checked="checked"'}} @endif>
                              <label class="form-check-label">
                                @if ( $item->kunci == "C")
                                <div style="color: rgb(97, 209, 52)">
                                    {!!$item->opsi_c!!}
                                </div>
                                @else
                                    {!!$item->opsi_c!!}
                                @endif
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" @if($item->jawaban == "D") {{'checked="checked"'}} @endif>
                              <label class="form-check-label">
                                @if ( $item->kunci == "D")
                                <div style="color: rgb(97, 209, 52)">
                                    {!!$item->opsi_d!!}
                                </div>
                                @else
                                    {!!$item->opsi_d!!}
                                @endif
                              </label>
                            </div>
                            @if ($item->opsi_e == null)

                            @else
                            <div class="form-check">
                              <input class="form-check-input" type="radio" @if($item->jawaban == "E") {{'checked="checked"'}} @endif>
                              <label class="form-check-label">
                                @if ( $item->kunci == "E")
                                <div style="color: rgb(97, 209, 52)">
                                    {!!$item->opsi_e!!}
                                </div>
                                @else
                                    {!!$item->opsi_e!!}
                                @endif
                              </label>
                            </div>
                            @endif
                          </div>

                    </td>
                </tr>
                @endforeach
            </table>
        </div>

    </div>



</body>
</html>


