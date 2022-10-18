@extends('master.pendaftar')

@section('title')
<title>Profil Pendaftar</title>

@endsection

@section('content')
<!-- Begin Page Content -->
<div class="container">

    <!-- Page Heading -->
    {{-- <h1 class="h3 mb-2 text-gray-800">Formulir Pendaftaran</h1> --}}

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="p-3 mt-3">
                @if (isset($ada))
                    <div class="text-center">
                        <h4>Biodata Sudah Ada, Silakan Tunggu Validasi</h4>
                        <h6>Hubungi Staf IT untuk validasi</h6>
                    </div>
                    <div class="text-center mt-4">
                        <a href="https://wa.link/m9o2vu" target="_blank" class="btn btn-success">
                            <i class="fab fa-whatsapp"></i>
                            Staf IT
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>

</div>
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

</div>

@endsection

@section('js')
<script type="text/javascript" src="{{asset('back/vendor/ckeditor/ckeditor.js')}}"></script>


@endsection



