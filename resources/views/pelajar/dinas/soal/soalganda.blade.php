@extends('master.pelajar')

@section('title')
<title>Computer Assisted Test - Cakra Krisna Manggala</title>
    <style>
        p{
            padding: 0;
            margin: 0;
            display: inline;
        }
        .jawaban{
            margin: 20px;
        }
        .opsi{
            padding: 15px;
            text-align: justify;
        }
    </style>
@endsection

@section('content')
    <!-- Begin Page Content -->
<div class="container">

    <!-- Page Heading -->
    <p class="mb-4">Perhatikan waktu mulai dan selesai disetiap tes, soal akan dibuka dan ditutup sesuai waktu yang tertera.</p>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div>Waktu yang tersisa adalah <b><span id="demo"> </span></b></div>
            <div class="p-3 mt-1">
                @php
                    $a = 1;
                @endphp
                <div class="row">
                    @foreach ($nomor as $item)
                        <form action="{{ route('pelajar.dinas.soalganda', [$item->dn_tes_id]) }}">
                            <input type="text" hidden name="q" value="{{ $item->id }}">
                            <button type="submit" class="btn btn-outline-warning">{{ $a++ }}</button>
                        </form>
                    @endforeach
                    <div class="float-right">
                        <a href="{{ route('pelajar.dinas.review', [$id]) }}" class="btn btn-sm btn-danger ml-3" onclick="return confirm('Anda yakin ingin selesai sekarang ?')"><i class="fas fa-cloud-upload-alt"></i> Selesai</a>
                    </div>
                </div>

                    @foreach ($ganda as $item)
                        <div class="card mt-3" style="border: solid 1px rgb(209, 194, 194); padding: 15px;">
                            <div class="text-justify text-dark mt-4 mb-4" style="border-bottom: solid 1px rgb(209, 194, 194); display:inline">
                                <h6>{{ $item->nomor_soal }}.</h6> <h6>{!! $item->soal !!}</h6>
                            </div>
                            <form class="jawaban" action="{{ route('pelajar.dinas.upjawabanganda', [$item->id]) }}" method="POST">
                                @csrf
                                    <input hidden name="soal" value={{$item->id}}>
                                    <div class="form-group">
                                      <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jawaban" value="A" @if($sudah_jawab->jawaban == "A") {{'checked="checked"'}} @endif>
                                        <label class="form-check-label">
                                          {!!$item->opsi_a!!}
                                        </label>
                                      </div>
                                      <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jawaban" value="B" @if($sudah_jawab->jawaban == "B") {{'checked="checked"'}} @endif>
                                        <label class="form-check-label">
                                          {!!$item->opsi_b!!}
                                        </label>
                                      </div>
                                      <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jawaban" value="C" @if($sudah_jawab->jawaban == "C") {{'checked="checked"'}} @endif>
                                        <label class="form-check-label">
                                          {!!$item->opsi_c!!}
                                        </label>
                                      </div>
                                      <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jawaban" value="D" @if($sudah_jawab->jawaban == "D") {{'checked="checked"'}} @endif>
                                        <label class="form-check-label">
                                          {!!$item->opsi_d!!}
                                        </label>
                                      </div>
                                      @if ($item->opsi_e == null)

                                      @else
                                      <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jawaban" value="E" @if($sudah_jawab->jawaban == "E") {{'checked="checked"'}} @endif>
                                        <label class="form-check-label">
                                          {!!$item->opsi_e!!}
                                        </label>
                                      </div>
                                      @endif
                                      <button type="submit" class="btn btn-success mt-4" id="simpan"><i class="fas fa-check-circle"></i> Simpan Jawaban</button>
                                    </div>
                            </form>
                        </div>
                    @endforeach
            </div>
        </div>
    </div>

</div>


@endsection

@section('js')
<script>
    var date = {!!json_encode($selesai->selesai)!!}
      // Set the date we're counting down to
      var countDownDate = new Date(date).getTime();

      // Update the count down every 1 second
      var x = setInterval(function() {

        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Output the result in an element with id="demo"
        document.getElementById("demo").innerHTML = days + "d " + hours + "h "
        + minutes + "m " + seconds + "s ";

        // If the count down is over, write some text
        if (distance < 0) {
          clearInterval(x);
          document.getElementById("demo").innerHTML = "Waktu Habis";

          {{--
          var id = {!!json_encode($tema_id)!!}
          window.location = ('https://cat.cakrakrisnamanggala.com/public/pelajar/cat/kumpulkan/'+id); --}}
        }
      }, 1000);
          </script>
@endsection
