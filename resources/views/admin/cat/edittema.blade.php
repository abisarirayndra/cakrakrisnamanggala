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
                          <form method="POST" action="{{route('admin.cat.updatetema', [$tema->id])}}">
                            @csrf
                              <div class="form-group">
                                <label for="tema">Mata Pelajaran</label>
                                <select name="mapel_id" class="form-control" id="">
                                    @foreach ($mapel as $item)
                                        <option value="{{$item->id}}" @if($item->mapel == $selected_mapel->mapel->mapel) {{'selected="selected"'}} @endif>{{$item->mapel}}</option>
                                    @endforeach
                                </select>
                              </div>
                              <div class="form-group">
                                <label for="judul tes">Judul Tes</label>
                                  <input type="text" class="form-control" name="judul_tes" value="{{$tema->judul_tes}}" required>
                              </div>
                              <div class="form-group">
                                <label for="pengajar_id">Pendidik</label>
                                <select name="pengajar_id" class="form-control" id="">
                                    @foreach ($pengajar_id as $item)
                                        <option value="{{$item->id}}" @if($item->mapel == $selected_pengajar->user->nama) {{'selected="selected"'}} @endif>{{$item->nama}}</option>
                                    @endforeach
                                </select>
                              </div>
                            <div class="form-group">
                              <label for="mulai">Mulai</label>
                                <input type="datetime-local" class="form-control" name="mulai" required>
                            </div>
                            <div class="form-group">
                              <label for="tenggat">Tenggat</label>
                                <input type="datetime-local" class="form-control" name="tenggat" required>
                            </div>
                            <div class="form-group">
                              <label for="Status">Status</label>
                                <select name="status" class="form-control" id="">
                                    <option value="0">Tidak Aktif</option>
                                    <option selected value="1">Aktif</option>
                                </select>
                            </div>
                            <div class="form-group">
                              <button type="submit" class="btn btn-primary text-light">Update</button>
                            </div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
      </div>

  </div>
@endsection
                
             