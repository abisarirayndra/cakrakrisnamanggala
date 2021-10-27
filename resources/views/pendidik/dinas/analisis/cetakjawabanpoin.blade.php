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
                                <div style="text-align: justify"><b>{!!$item->soal!!}</b> <div>Poin : <b> {{ $item->nilai }}</b></div></div> <br>
                                <div class="form-group">
                                    <div class="form-check" style="margin-bottom: 10px">
                                        <input class="form-check-input" type="radio" @if($item->jawaban == "A") {{'checked="checked"'}} @endif>
                                        <label class="form-check-label">
                                        <b>{!!$item->opsi_a!!}</b> (Poin {{ $item->poin_a }})
                                        </label>
                                    </div>
                                    <div class="form-check" style="margin-bottom: 10px">
                                        <input class="form-check-input" type="radio" @if($item->jawaban == "B") {{'checked="checked"'}} @endif>
                                        <label class="form-check-label">
                                        <b>{!!$item->opsi_b!!}</b> (Poin {{ $item->poin_b }})
                                        </label>
                                    </div>
                                    <div class="form-check" style="margin-bottom: 10px">
                                        <input class="form-check-input" type="radio" @if($item->jawaban == "C") {{'checked="checked"'}} @endif>
                                        <label class="form-check-label">
                                        <b>{!!$item->opsi_c!!}</b> (Poin {{ $item->poin_c }})
                                        </label>
                                    </div>
                                    <div class="form-check" style="margin-bottom: 10px">
                                        <input class="form-check-input" type="radio" @if($item->jawaban == "D") {{'checked="checked"'}} @endif>
                                        <label class="form-check-label">
                                        <b>{!!$item->opsi_d!!}</b> (Poin {{ $item->poin_d }})
                                        </label>
                                    </div>
                                    @if ($item->opsi_e == null)

                                    @else
                                    <div class="form-check" style="margin-bottom: 10px">
                                        <input class="form-check-input" type="radio" @if($item->jawaban == "E") {{'checked="checked"'}} @endif>
                                        <label class="form-check-label">
                                        <b>{!!$item->opsi_e!!}</b> (Poin {{ $item->poin_e }})
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


