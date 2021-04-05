@extends('master.admin1')

@section('title')
<title>Edit Gambar</title>
@endsection

@section('content')
<body class="bg-gradient-warning">

  <div class="container">

      <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
              <!-- Nested Row within Card Body -->
              <div class="row">
                  <div class="col-lg-12">
                      <div class="p-5">
                          <div class="text-center">
                              <h1 class="h4 text-gray-900 mb-4">Edit Gambar</h1>
                          </div>
                          <form method="POST" action="{{route('pengajar.cat.updategambar', [$soal->id])}}" enctype="multipart/form-data">
                            @csrf
                            <img src="{{asset('storage/soal/'.$soal->foto)}}" alt="">
                            <div class="form-group">
                              <label for="soal">Ubah Gambar</label>
                                <input type="file" class="form-control" name="foto" autofocus>
                            </div>
                            <div class="form-group">
                              <button type="submit" class="btn btn-primary btn-lg btn-block">
                                Update
                              </button>
                            </div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
@endsection

                
             