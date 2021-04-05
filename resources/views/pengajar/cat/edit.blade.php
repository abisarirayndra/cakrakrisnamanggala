@extends('master.admin1')

@section('title')
<title>Edit Tes</title>
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
                              <h1 class="h4 text-gray-900 mb-4">Edit Tes</h1>
                          </div>
                          <form method="POST" action="{{route('pengajar.cat.updatetema', [$tema->id])}}">
                            @csrf
                            <div class="form-group">
                              <label for="tema">Judul Tes</label>
                                <input type="text" class="form-control" name="judul_tes" value="{{$tema->judul_tes}}" required autofocus>
                                <input name="pengajar_id" readonly hidden value="{{$id_user }}"> 
                            </div>
                          <div class="form-group">
                            <label for="durasi">Jumlah Soal</label>
                              <input type="number" class="form-control" name="jumlah_soal" value="{{$tema->jumlah_soal}}" required>
                          </div>
                          <div class="form-group">
                            <label for="mulai">Mulai</label>
                              <input type="datetime-local" class="form-control" name="mulai" value="{{$tema->mulai}}" required>
                          </div>
                          <div class="form-group">
                            <label for="tenggat">Tenggat</label>
                              <input type="datetime-local" class="form-control" name="tenggat" value="{{$tema->tenggat}}" required>
                          </div>
                          <div class="form-group">
                            <label for="Status">Status</label>
                              <select name="status" class="form-control" id="" value="{{$tema->status}}">
                                  <option value="0">Tidak Aktif</option>
                                  <option selected value="1">Aktif</option>
                              </select>
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
                
             