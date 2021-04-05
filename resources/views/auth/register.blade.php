@extends('master.admin1')

@section('title')
<title>Daftar</title>
@endsection

@section('content')
<body class="bg-gradient-warning">

  <div class="container">

      <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
              <!-- Nested Row within Card Body -->
              <div class="row">
                  <div class="col-lg-5 d-none d-lg-block text-center mt-3"><img class="mt-5" src="{{asset('img/krisna.png')}}" width="300" height="300" alt=""></div>
                  <div class="col-lg-7">
                      <div class="p-5">
                          <div class="text-center">
                              <h1 class="h4 text-gray-900 mb-4">Daftarkan Diri Anda!</h1>
                          </div>
                          <form method="POST" action="{{route('reg')}}">
                            @csrf
                            <div class="form-group">
                              <input type="text" value="4" name="role_id" hidden>
                            </div>
                              <div class="form-group">
                                <label for="nama">Nama Lengkap</label>
                                <input id="nama" type="text" class="form-control" name="nama" autofocus required>
                              </div> 
                              <div class="form-group">
                                <label for="nama">Kelas</label>
                                <select name="kelas_id" id="" class="form-control">
                                  @foreach ($kelas as $item)
                                  <option value="{{$item->id}}">{{$item->nama}}</option>
                                  @endforeach
                                </select>
                              </div>                         
                            <div class="row">
                              <div class="form-group col-6">
                                <label for="email">Email</label>
                                <input id="email" type="email" class="form-control" name="email" required>
                                <div class="invalid-feedback">
                                </div>
                              </div>
                              <div class="form-group col-6">
                                <label for="nomor_registrasi">Nomor Registrasi</label>
                                <input id="nomor_registrasi" type="number" class="form-control" name="nomor_registrasi">
                                <div class="invalid-feedback">
                                </div>
                              </div>
                           </div>
                            
                            <div class="row">
                              <div class="form-group col-6">
                                <label for="password" class="d-block">Password</label>
                                <input id="password" type="password" class="form-control" name="password" placeholder="Minimal 8 Karakter" required>
                              </div>
                              <div class="form-group col-6">
                                <label for="password2" class="d-block">Password Confirmation</label>
                                <input id="confirm_password" type="password" class="form-control" required>
                                <span class="h6" id='message'></span>
                              </div>
                            </div>
          
                            <div class="form-group">
                              <label for="whatsapp">No. Whatsapp</label>
                              <input id="whatsapp" type="number" class="form-control" name="whatsapp" required>
                              <div class="invalid-feedback">
                              </div>
                            </div>
          
                            <div class="form-group">
                              <button type="submit" id="button" class="btn btn-primary btn-lg btn-block" disabled>
                                Register
                              </button>
                            </div>
                          </form>
                          <hr>
                      </div>
                  </div>
              </div>
          </div>
      </div>

  </div>
@endsection

@section('js')
<script type="text/javascript">
  $('#password, #confirm_password').on('keyup', function () {
        if ($('#password').val() == $('#confirm_password').val()) {
          $('#message').html('Password Cocok').css('color', 'green');
          $('#button').removeAttr("disabled");
        } else {
          $('#message').html('Password Tidak Cocok').css('color', 'red');
          var element = document.getElementById('button');
          element.setAttribute('disabled','disabled');
        }

      });
</script>
@endsection
                
             