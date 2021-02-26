@extends('master.admin1')

@section('title')
    <title>CAT - Pelajar</title>
@endsection

@section('css')
     <!-- Custom styles for this page -->
     <link href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

      <!-- Sidebar -->
      <ul class="navbar-nav bg-gradient-warning sidebar sidebar-dark accordion" id="accordionSidebar">

          <!-- Sidebar - Brand -->
          <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
             
                <img src="{{asset('assets/img/logos/krisna.png')}}" width="50" alt="">
             
          </a>

          <!-- Divider -->
          <hr class="sidebar-divider my-0">

          <!-- Nav Item - Dashboard -->
          <li class="nav-item">
              <a class="nav-link">
                  <i class="fas fa-fw fa-tachometer-alt"></i>
                  <span>Dashboard</span></a>
          </li>

          

          <!-- Sidebar Toggler (Sidebar) -->
          <div class="text-center d-none d-md-inline">
              <button class="rounded-circle border-0" id="sidebarToggle"></button>
          </div>

      </ul>
      <!-- End of Sidebar -->

      <!-- Content Wrapper -->
      <div id="content-wrapper" class="d-flex flex-column">

          <!-- Main Content -->
          <div id="content">

              <!-- Topbar -->
              <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                  <!-- Sidebar Toggle (Topbar) -->
                  <form class="form-inline">
                      <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                          <i class="fa fa-bars"></i>
                      </button>
                  </form>
                  <!-- Topbar Navbar -->
                  <ul class="navbar-nav ml-auto">
                      <!-- Nav Item - User Information -->
                      <li class="nav-item dropdown no-arrow">
                          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <span class="mr-2 d-none d-lg-inline text-gray-600 small">Hai, {{$user}}</span>
                              <img class="img-profile rounded-circle"
                                  src="{{asset('img/undraw_profile.svg')}}">
                          </a>
                          <!-- Dropdown - User Information -->
                          <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                              aria-labelledby="userDropdown">
                              <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                  Logout
                              </a>
                          </div>
                      </li>

                  </ul>

              </nav>
              <!-- End of Topbar -->

              <!-- Begin Page Content -->
              <div class="container-fluid">
                
                <!-- Main Content -->
      <div class="main-content">
        <section class="section">
              <div class="card card-primary">
                <div class="card-header">
                  <h5>Kerjakan dengan teliti !</h5>
                  <div>Waktu yang tersisa adalah <b><span id="demo"> </span></b></div>
                </div>
                <div class="card-body">
                  <div class="row">
                    @foreach ($soal as $item)
                    <form action="{{route('pelajar.cat.soal', [$item->tema_id])}}" method="get">
                      <button type="submit" class="btn btn-outline-warning"><input hidden name="nomor" value="{{$item->nomor_soal}}"><input hidden name="soal" value="{{$item->id}}">{{$item->nomor_soal}}</button>
                    </form>
                    @endforeach                                  
                  </div>          
                  <a href="" class="btn btn-primary mt-3" data-toggle="modal" data-target="#selesaiModal">Selesai</a> 
      @if ($tenggat->jenis == "matematika")
                @foreach ($tampil_soal as $item)
                <div class="row mt-4">
                    <h6>{{$item->nomor_soal}}.</h6> <h4>${{$item->soal}}$</h4> 
                </div>
                <form action="{{route('pelajar.cat.jawaban')}}" method="GET">
                  @csrf
                  <input hidden name="soal" value={{$item->id}}>
                  <input hidden name="nomorsoal" value={{$item->nomor_soal}}>
                  <div class="form-group">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_a}}">
                      <label class="form-check-label" for="exampleRadios1">
                        <h4>${{$item->opsi_a}}$</h4>
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_b}}">
                      <label class="form-check-label" for="exampleRadios2">
                        <h4>${{$item->opsi_b}}$</h4>
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_c}}">
                      <label class="form-check-label" for="exampleRadios2">
                        <h4>${{$item->opsi_c}}$</h4>
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_d}}">
                      <label class="form-check-label" for="exampleRadios2">
                        <h4>${{$item->opsi_d}}$ </h4>
                      </label>
                    </div>
                    @if ($item->opsi_e == null)

                    @else
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_e}}">
                      <label class="form-check-label" for="exampleRadios2">
                        <h4>${{$item->opsi_e}}$</h4>
                      </label>
                    </div>
                    @endif
                    <button type="submit" class="btn btn-success mt-4" id="simpan">Simpan Jawaban</button>
                  </div>
                  
                </form>
                {{-- <div class="row float-right">
                  <form action="{{route('pelajar.cat.soal', [$item->tema_id])}}" method="get">
                    @csrf
                    <div class="text-center mt-5 mr-3"><button class="btn btn-danger" type="submit"><input hidden name="nomor" value="{{$item->nomor_soal-1 }}"><input hidden name="soal" value="{{$item->id}}">Sebelumnya</button></div>                 
                  </form>
                  <form action="{{route('pelajar.cat.soal', [$item->tema_id])}}" method="get">
                    <div class="text-center mt-5 mr-3"><button class="btn btn-primary" type="submit"><input hidden name="nomor" value="{{$item->nomor_soal+1 }}"><input hidden name="soal" value="{{$item->id}}">Selanjutnya</button></div>               
                  </form>
                </div> --}}
            @endforeach
            @foreach($sudah_jawab as $item)
            @if ($item->jawaban == null)
             <div class="h6">Jawaban yang dipilih : Belum Ada</div>
            @else
              <div class="h6">Jawaban yang dipilih : <h4>${{$item->jawaban}}$</h4></div>
            @endif
            @endforeach
    @else
            @foreach ($tampil_soal as $item)
            <div class="row mt-4">
                <h6>{{$item->nomor_soal}}. {{$item->soal}}</h6>
            </div>
            <form action="{{route('pelajar.cat.jawaban')}}" method="GET">
              @csrf
              <input hidden name="soal" value={{$item->id}}>
              <input hidden name="nomorsoal" value={{$item->nomor_soal}}>
              <div class="form-group">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_a}}">
                  <label class="form-check-label" for="exampleRadios1">
                    {{$item->opsi_a}}
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_b}}">
                  <label class="form-check-label" for="exampleRadios2">
                    {{$item->opsi_b}}
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_c}}">
                  <label class="form-check-label" for="exampleRadios2">
                    {{$item->opsi_c}}
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_d}}">
                  <label class="form-check-label" for="exampleRadios2">
                    {{$item->opsi_d}}
                  </label>
                </div>
                @if ($item->opsi_e == null)

                @else
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_e}}">
                  <label class="form-check-label" for="exampleRadios2">
                    {{$item->opsi_e}}
                  </label>
                </div>
                @endif
                <button type="submit" class="btn btn-success mt-4" id="simpan">Simpan Jawaban</button>
              </div>
              
            </form>
            {{-- <div class="row float-right">
              <form action="{{route('pelajar.cat.soal', [$item->tema_id])}}" method="get">
                @csrf
                <div class="text-center mt-5 mr-3"><button class="btn btn-danger" type="submit"><input hidden name="nomor" value="{{$item->nomor_soal-1 }}"><input hidden name="soal" value="{{$item->id}}">Sebelumnya</button></div>                 
              </form>
              <form action="{{route('pelajar.cat.soal', [$item->tema_id])}}" method="get">
                <div class="text-center mt-5 mr-3"><button class="btn btn-primary" type="submit"><input hidden name="nomor" value="{{$item->nomor_soal+1 }}"><input hidden name="soal" value="{{$item->id}}">Selanjutnya</button></div>               
              </form>
            </div> --}}
        @endforeach
        @foreach($sudah_jawab as $item)
        @if ($item->jawaban == null)
        <div class="h6">Jawaban yang dipilih : Belum Ada</div>
        @else
          <div class="h6">Jawaban yang dipilih : {{$item->jawaban}}</div>
        @endif
        @endforeach      


    @endif  

                </div>
                
             </div>

        </section>
      </div>


              </div>
              <!-- /.container-fluid -->

          <!-- End of Main Content -->

          </div>
          <!-- End of Main Content -->

          <!-- Footer -->
          <footer class="sticky-footer bg-white">
              <div class="container my-auto">
                  <div class="copyright text-center my-auto">
                      <span>Developed With Love by IT Staff Cakra Krisna Manggala 2021</span>
                  </div>
              </div>
          </footer>
          <!-- End of Footer -->

      </div>
      <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                  <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                  </button>
              </div>
              <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
              <div class="modal-footer">
                  <button class="btn btn-primary" type="button" data-dismiss="modal">Cancel</button>
                  <a class="btn btn-danger" href="{{route('logout')}}">Logout</a>
              </div>
          </div>
      </div>
  </div>

  <div class="modal fade" id="selesaiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Apakah Anda Yakin Untuk Selesai?</h5>
                  <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                  </button>
              </div>
              <div class="modal-body">Periksa ulang jawaban agar tidak ada yang belum dikerjakan</div>
              <div class="modal-footer">
                  <button class="btn btn-danger" type="button" data-dismiss="modal">Cancel</button>
                  <a class="btn btn-primary" href="{{route('pelajar.cat.kumpulkan',[$tema_id])}}">Selesai</a>
              </div>
          </div>
      </div>
  </div>

    
@endsection

@section('js')
    <script>
    var date = {!!json_encode($tenggat->tenggat)!!}
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
          var id = {!!json_encode($tema_id)!!}
          window.location = ('http://localhost:8000/pelajar/cat/kumpulkan/'+id);
        }
      }, 1000);
          </script>
          <script type="text/x-mathjax-config">
            MathJax.Hub.Config({
              tex2jax: {inlineMath: [['$','$'], ['\\(','\\)']]}
            });
            </script>
            <script type="text/javascript" async
              src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-MML-AM_CHTML">
            </script>
@endsection




      
