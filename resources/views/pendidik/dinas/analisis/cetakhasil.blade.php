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
	</center>

    <div class="container-fluid">
        <table class="table table-bordered">
            <thead>
                <tr>
                  <th style="width: 20px">Ranking</th>
                  <th>Nama Pelajar</th>
                  <th>Kelas</th>
                  <th style="width: 50px">Nilai</th>
                  <th style="width: 50px">Akumulasi</th>
                  <th>Waktu Pengumpulan</th>
                </tr>
              </thead>
              <tbody>
                @php
                $no = 1;
                @endphp
                @foreach ($nilai as $item)
                <tr>
                <td class="text-center">{{$no++}}</td>
                <td>{{$item->nama}}</td>
                <td>{{ $item->kelas }}</td>
                <td>{{$item->nilai}}</td>
                <td>{{$item->akumulasi}}</td>
                <td>{{\Carbon\Carbon::parse($item->created_at)->isoFormat('dddd, D MMMM Y HH:mm')}}</td>
                </tr>
                  @endforeach
              </tbody>
        </table>
    </div>



</body>
</html>


