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
                              <label for="nama">Role</label>
                              <select name="role_id" id="" class="form-control" required>
                                <option value="4">Pelajar</option>
                                <option value="3">Pengajar</option>
                              </select>
                            </div>
                              <div class="form-group">
                                <label for="nama">Nama Lengkap</label>
                                <input id="nama" type="text" class="form-control" name="nama" autofocus required>
                              </div> 
                              <div class="form-group">
                                <label for="nama">Kelas</label>
                                <select name="kelas_id" id="" class="form-control">
                                  <option value="" selected></option>
                                  <option value="1">Kelas A - Genteng</option>
                                  <option value="2">Kelas B - Genteng</option>
                                  <option value="4">Kelas Banyuwangi</option>
                                  <option value="3">Umum</option>
                                </select>
                                <span class="text-muted">Apabila pengajar, harap dikosongkan</span>
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
                                <input id="nomor_registrasi" type="text" class="form-control" name="nomor_registrasi">
                                <span>Apabila pengajar, harap dikosongkan</span>
                                <div class="invalid-feedback">
                                </div>
                              </div>
                           </div>
                            
                            <div class="row">
                              <div class="form-group col-6">
                                <label for="password" class="d-block">Password</label>
                                <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator" name="password" required>
                                <div id="pwindicator" class="pwindicator" >
                                  <div class="bar"></div>
                                  <div class="label"></div>
                                </div>
                              </div>
                              <div class="form-group col-6">
                                <label for="password2" class="d-block">Password Confirmation</label>
                                <input id="confirm_password" type="password" class="form-control" required>
                                <span class="h6" id='message'></span>
                              </div>
                            </div>
          
                            <div class="form-group">
                              <label for="whatsapp">Whatsapp</label>
                              <input id="whatsapp" type="number" class="form-control" name="whatsapp" required>
                              <div class="invalid-feedback">
                              </div>
                            </div>
          
                            <div class="form-group">
                              <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="agree" class="custom-control-input" id="agree">
                                <label class="custom-control-label" for="agree">I agree with the terms and conditions</label>
                              </div>
                            </div>
          
                            <div class="form-group">
                              <button type="submit" class="btn btn-primary btn-lg btn-block">
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
        } else {
          $('#message').html('Password Tidak Cocok').css('color', 'red');
        }

      });
</script>
@endsection
                
             