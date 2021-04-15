@extends('master.admin1')

@section('title')
    <title>CAT - Pengajar</title>
@endsection

@section('css')
     <!-- Custom styles for this page -->
     <link href="{{asset('vendor/datatables/datatables.min.css')}}" rel="stylesheet">
     {{-- <link rel="stylesheet" href="cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css"> --}}
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
                    <div class="col-xl-6">
                        <h1 class="h3 mb-4 text-gray-800">Cakra Assesment Test - Pengajar</h1>
                    </div>
                    
                  </div>
                  <!-- DataTales Example -->
                  <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Tes Pendidik {{$user}}</h6>
                    </div>
                      <div class="card-body">
                          <div class="table-responsive">
                              <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                  <thead>
                                      <tr>
                                        <th>No.</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Judul Tes</th>
                                        <th>Mulai</th>
                                        <th>Tenggat</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                              @php
                                              $no = 1;
                                          @endphp
                                          @foreach ($tema as $item)
                                          <tr>
                                          <td>{{$no++}}</td>
                                          <td>{{$item->mapel->mapel}}</td>
                                          <td>{{$item->judul_tes}}</td>
                                          <td>{{\Carbon\Carbon::parse($item->mulai)->isoFormat('dddd, D MMMM Y HH:mm')}}</td>
                                          <td>{{\Carbon\Carbon::parse($item->tenggat)->isoFormat('dddd, D MMMM Y HH:mm')}}</td>
                                          @if ($item->status == 1)
                                          <td>Aktif</td>
                                          @else
                                          <td> Tidak Aktif</td>   
                                          @endif
                                          <td>
                                            <a href="{{route('pengajar.cat.soal', [$item->id])}}"><button type="button" class="btn btn-sm btn-success">Soal</button></a>
                                            <a href="{{route('pengajar.cat.edit', [$item->id])}}"><button type="button" class="btn btn-sm btn-primary">Edit</button></a>
                                            <a href="{{route('pengajar.cat.hasil', [$item->id])}}"><button type="button" class="btn btn-sm btn-warning">Hasil</button></a>
                                            </td>
                                        </tr>
                                      @endforeach
                                  </tbody>
                              </table>
                          </div>
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
                  <button class="btn btn-primary" type="button" data-dismiss="modal">Cancel</button>
                  <a class="btn btn-danger" href="{{route('logout')}}">Logout</a>
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
    {{-- <script charset="utf8" src="cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script> --}}
    <script>
        
    </script>
@endsection