@extends('master.admin1')

@section('title')
    <title>Login</title>
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
                                      <h1 class="h4 text-gray-900 mb-4">Selamat Datang di Computer Assisted Test !</h1>
                                  </div>
                                  @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                <li>{{ $errors->first() }}</li>
                                            </ul>
                                        </div>
                                    @endif
                                  <form class="user" action="{{route('log')}}" method="post">
                                    @csrf
                                      <div class="form-group">
                                          <input type="email" class="form-control form-control-user" placeholder="Masukkan Email" name="email">
                                      </div>
                                      <div class="form-group">
                                          <input type="password" class="form-control form-control-user" placeholder="Password" name="password">
                                      </div>
                                      <div class="form-group">
                                          <div class="custom-control custom-checkbox small">
                                              <input type="checkbox" class="custom-control-input" name="remember">
                                              <label class="custom-control-label" for="customCheck">Ingat Saya</label>
                                          </div>
                                      </div>
                                      <button type="submit" class="btn btn-warning btn-user btn-block">
                                          Login
                                      </button>
                                  </form>
                                  <hr>

                                  <div class="text-center">
                                      <div class="row">
                                           <a class="small" href="{{route('reset')}}"> Lupa Password ?</a>
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

