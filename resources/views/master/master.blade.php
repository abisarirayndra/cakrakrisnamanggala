<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/favicon.ico')}}" />

    @yield('title')

    <!-- Custom fonts for this template-->
    <link href="{{asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{asset('css/sb-admin-2.min.css')}}" rel="stylesheet">

</head>
<body id="page-top">

    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand-md navbar-light bg-warning topbar mb-4 static-top shadow" style="z-index: 1;">
                <div class="container">
                    <a class="navbar-brand" href="#"><img src="{{asset('assets/img/favicon.ico')}}" width="50" alt=""></a>
                    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-bars ml-1"></i>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarResponsive">
                        <ul class="navbar-nav mr-auto bg-warning">
                            <li class="nav-item"><a class="nav-link text-white" href="{{ route('pendidik.dinas.beranda') }}">Beranda</a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="{{ route('pendidik.dinas.paket') }}"">Paket Soal</a></li>
                            {{-- <li class="nav-item"><a class="nav-link text-white" href="">Kedinasan</a></li> --}}
                            <li class="nav-item"><a class="nav-link text-white" href="{{ route('pendidik.dinas.analisis') }}">Analisis</a></li>
                            <div class="topbar-divider d-none d-sm-block"></div>

                                <!-- Nav Item - User Information -->
                                <li class="nav-item dropdown no-arrow">
                                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="mr-2 d-none d-lg-inline text-gray-600 small"><div class="text-white">{{$user}}</div></span>
                                        <img class="img-profile rounded-circle"
                                            src="{{asset('img/undraw_profile.svg')}}">
                                    </a>
                                    <!-- Dropdown - User Information -->
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                        aria-labelledby="userDropdown">
                                        <a class="dropdown-item" href="" data-toggle="modal" data-target="#logoutModal">
                                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Logout
                                        </a>
                                    </div>
                                </li>
                        </ul>
                    </div>
                </div>

            </nav>
            <!-- End of Topbar -->

@yield('content')
@include('sweetalert::alert')
<!-- End of Main Content -->
<!-- /.container-fluid -->
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
             <h5 class="modal-title" id="exampleModalLabel">Yakin Untuk Meninggalkan Halaman Ini ?</h5>
             <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">×</span>
             </button>
         </div>
         <div class="modal-footer">
             <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
             <a class="btn btn-danger" href="{{route('logout')}}">Logout</a>
         </div>
     </div>
 </div>
</div>

<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Cakra Krisna Manggala {{\Carbon\Carbon::now()->isoFormat('Y')}}</span>
        </div>
    </div>
    </footer>
    <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->
    <!-- Bootstrap core JavaScript-->
    <script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{asset('vendor/jquery-easing/jquery.easing.min.js')}}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{asset('js/sb-admin-2.min.js')}}"></script>

@yield('js')

</body>


</html>
