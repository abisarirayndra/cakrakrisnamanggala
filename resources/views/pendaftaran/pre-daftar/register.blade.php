@extends('master.admin1')

@section('title')
    <title>Register Email - Cakra Krisna Manggala</title>
@endsection

@section('content')
<body class="bg-gradient-warning">

  <div class="container">

      <!-- Outer Row -->
      <div class="row justify-content-center">

          <div class="col-xl-10 col-lg-12 col-md-9">

              <div class="card o-hidden border-0 shadow-lg my-5">
                  <div class="card-body p-0">
                      <!-- Nested Row within Card Body -->
                      <div class="row">
                          <div class="col-lg-6 d-none d-lg-block text-center mt-4"><img src="{{asset('img/krisna.png')}}" width="300" height="300" alt=""></div>
                          <div class="col-lg-6">
                              <div class="p-5">
                                  <div class="text-center">
                                      <h1 class="h4 text-gray-900 mb-4">Daftarkan diri anda</h1>
                                  </div>
                                  @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                  <form class="user" action="{{ route('up-register-email') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-user" placeholder="Nama Lengkap" name="nama" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-user" placeholder="E-Mail" name="email" required>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input type="password" class="form-control form-control-user" id="password" placeholder="Password" name="password" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="password" class="form-control form-control-user" id="confirm_password" placeholder="Repeat Password" required>
                                            <small id='message'></small>
                                        </div>
                                    </div>
                                    <a href="{{route('petunjuk')}}" class="btn btn-danger">Kembali</a>
                                    <button id="button" class="btn btn-warning" type="submit" >Register</button>
                                  </form>
                                  <hr>

                                  <div class="text-center">
                                    <div class="row">
                                         <small>Email sudah terdaftar ?</small><a class="btn btn-sm btn-success ml-2" href="{{route('login')}}"> login</a>
                                    </div>
                                </div>
                              </div>
                          </div>
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

