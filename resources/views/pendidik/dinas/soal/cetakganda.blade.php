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
                <h5>Cakra Krisna Manggala</h5>
                <h6>Soal Mata Pelajaran {{ $data->mapel }}</h6>
                <p class="mb-3">Pendidik : {{ $data->nama }}</p>
	</center>

    <div class="container-fluid">
        <table>
                @foreach ($soal as $item)
                <tr>
                    <td style="vertical-align: top;"><b>{{$item->nomor_soal}}</b></td>
                    <td class="pl-4 pb-4">
                        <div style="text-align: justify"><b>{!!$item->soal!!}</b></div> <br>
                        <ol type="A">
                            <li>{!!$item->opsi_a!!}</li>
                            <li>{!!$item->opsi_b!!}</li>
                            <li>{!!$item->opsi_c!!}</li>
                            <li>{!!$item->opsi_d!!}</li>
                            @if (isset($item->opsi_e))
                            <li>{!!$item->opsi_e!!}</li>
                            @endif
                        </ol>
                    </td>
                </tr>
                @endforeach
        </table>
    </div>



</body>
</html>


