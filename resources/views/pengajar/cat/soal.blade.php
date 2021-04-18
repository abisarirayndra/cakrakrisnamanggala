@extends('master.admin1')

@section('title')
    <title>CAT - Pengajar</title>
@endsection

@section('css')
     <!-- Custom styles for this page -->
     <link href="{{asset('vendor/datatables/datatables.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

      <!-- Sidebar -->
      <ul class="navbar-nav bg-gradient-danger sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
           
              <img src="{{asset('assets/img/logos/krisna.png')}}" width="50" alt="">
           
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Interface
        </div>

        <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                aria-expanded="true" aria-controls="collapseTwo">
                <i class="fas fa-fw fa-cog"></i>
                <span>CAT</span>
            </a>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Cakra Assesement Test</h6>
                    <a class="collapse-item" href="{{route('pengajar.cat.paket')}}">CAT - Daftar Tes</a>
                </div>
            </div>
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

                  <!-- Page Heading -->
                  <div class="row">
                  </div>

                  {{-- cards import --}}
                  
                        @if(isset($ada_soal))
                        @elseif (isset($tema->jumlah_soal))
                        <div class="card">
                            <div class="card-body">
                         <form action="{{route('pengajar.cat.importsoal')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-3">
                                        <label for="inputEmail4">Jumlah Soal</label>
                                        <input type="text" class="form-control" value="{{$tema->jumlah_soal}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                            <div class="form-group col-md-6">
                              <label for="inputEmail4">Kode Tema</label>
                              <input type="text" class="form-control" id="tema" value="{{$tema_id}}" readonly>
                              <small id="passwordHelpBlock" class="form-text text-muted">
                                Pastikan anda mencantumkan kode tema pada file excel yang akan anda unggah
                              </small>
                            </div>
                            <div class="form-group col-md-6">
                              <div class="form-group">
                                <label>File</label>
                                <input type="file" class="form-control" name="file">
                                <small id="passwordHelpBlock" class="form-text text-muted">
                                  Pastikan file yang anda upload benar dengan format .xslx atau .csv
                                </small>
                              </div>
                            </div>
                          </div>
                          <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Submit Soal</button>
                          </div>
                        </form>
                    </div>
                </div>
                        @else
                        <div class="card">
                            <div class="card-body">
                        <div class="row">
                         <form action="{{route('pengajar.cat.upjumlahsoal', [$tema->id])}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Jumlah Soal</label>
                                    <input type="text" class="form-control" name="jumlah_soal">
                                    <small id="passwordHelpBlock" class="form-text text-muted">
                                      Isi jumlah soal terlebih dahulu sebelum upload soal !
                                    </small>
                                  </div>
                                  <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                  </div>
                            </div>
                         </form>
                        </div>
                    </div>
                </div>
                        @endif
                  
                  <div class="card shadow mb-4">
                      <div class="card-header py-3">
                          <div class="row">
                                <div class="col-xl-6">
                                    <h6 class="m-0 font-weight-bold text-primary">Preview Soal {{$tema->mapel->mapel}}</h6>
                                </div>
                                <div class="col-xl-6">
                                    <a href="{{route('pengajar.cat.tema', [$paket->paket_id])}}" class="btn btn-sm btn-primary float-right">Kembali ke Tes</a>
                                </div>
                          </div>
                      </div>
                      <div class="card-body">
                            @php
                             $no = 1;   
                            @endphp
                            @foreach ($soal as $item)
                                
                                <div class="row m-3 mt-5">
                                    <div class="col-xl-1">
                                        <p>{{$item->nomor_soal}}.</p>
                                    </div>
                                    <div class="col-xl-11">
                                        @if ($item->foto != null)
                                            <img src="{{asset('soal/'.$item->foto)}}" alt="" width="500">
                                            <a href="{{route('pengajar.cat.editgambar',[$item->id])}}" class="btn btn-sm btn-warning">Edit Gambar</a>
                                            <a href="{{route('pengajar.cat.hapusgambar',[$item->id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Hapus Gambar</a>
                                        @else
                                        <a href="{{route('pengajar.cat.tambahgambar',[$item->id])}}" class="btn btn-sm btn-success">Gambar</a>
                                        @endif
                                        <p class="mt-2">{!!$item->soal!!}</p>
                                    </div>
                                </div>
                                <div class="row m-3">
                                    <div class="col-xl-1">
                                    </div>
                                    <div class="col-xl-11">
                                            <h6>A. {!!$item->opsi_a!!}</h6>
                                            <h6>B. {!!$item->opsi_b!!}</h6>
                                            <h6>C. {!!$item->opsi_c!!}</h6>
                                            <h6>D. {!!$item->opsi_d!!}</h6>
                                            @if (isset($item->opsi_e))
                                            <h6>E. {!!$item->opsi_e!!}</h6>
                                            @endif
                                            <h6><b>Kunci : {!!$item->kunci!!}</b><br>
                                            <div class="mt-3">
                                                <a href="{{route('pengajar.cat.editsoal', [$item->id])}}"><button type="button" class="btn btn-sm btn-warning">Edit</button></a>
                                                <a href="{{route('pengajar.cat.hapussoal', [$item->id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Hapus</a>
                                            </div>
                                    </div>
                                    
                                </div>
                                <hr>
                            @endforeach
                      </div>
                  </div>
              </div>
              <!-- /.container-fluid -->

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
                  <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                  <a class="btn btn-primary" href="{{route('logout')}}">Logout</a>
              </div>
          </div>
      </div>
  </div>
    
@endsection

@section('js')
    <!-- Page level plugins -->
    <script src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('vendor/datatables/datatables.min.js')}}"></script>

    <!-- Page level custom scripts -->
    <script src="{{asset('js/demo/datatables-demo.js')}}"></script>
    <script type="text/x-mathjax-config">
        MathJax.Hub.Config({
          tex2jax: {inlineMath: [['$','$'], ['\\(','\\)']]}
        });
        </script>
        <script type="text/javascript" async
          src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-MML-AM_CHTML">
        </script>
@endsection
