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
        .table td, .table th {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: center;
        }
        .table th{
            background-color: orange;
        }
        .nilai{
            background-color: yellow;

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
                <h6>Hasil Rekap {{ $paket->nama_paket }} - TNI/Polri</h6>
	</center>

    <div class="container-fluid">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 20px; text-align: center;">Ranking</th>
                    <th style="text-align: center;">Nama Pelajar</th>
                    <th style="text-align: center;">Kelas</th>
                    <th style="text-align: center;">Matematika</th>
                    <th style="text-align: center;">Bahasa Indonesia</th>
                    <th style="text-align: center;">Bahasa Inggris</th>
                    <th style="text-align: center;">Wawasan Kebangsaan</th>
                    <th style="width: 100px; text-align: center;">Total Akumulasi</th>
                </tr>
              </thead>
              <tbody>
                @php
                $no = 1;
                @endphp
                @foreach ($hasil as $item)
                    <tr>
                        <td style="text-align: center;">{{$no++}}</td>
                        <td>{{$item->nama}}</td>
                        <td>{{ $item->kelas }}</td>
                        <td style="text-align: center;">{{$item->mtk}}</td>
                        <td style="text-align: center;">{{$item->bin}}</td>
                        <td style="text-align: center;">{{$item->bing}}</td>
                        <td style="text-align: center;">{{$item->ipu_wk}}</td>
                        <td class="nilai" style="text-align: center;">{{$item->total_nilai}}</td>
                    </tr>
                  @endforeach
              </tbody>
        </table>
    </div>



</body>
</html>


