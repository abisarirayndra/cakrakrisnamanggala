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
                          <div class="col-lg-6 d-none d-lg-block text-center mt-5"><img src="{{asset('img/krisna.png')}}" width="200" height="200" alt=""></div>
                          <div class="col-lg-6">
                              <div class="p-5">
                                  <div class="text-center">
                                      <h1 class="h4 text-gray-900 mb-4">Reset Password</h1>
                                  </div>
                                  <form class="user" action="{{route('upreset')}}" method="post">
                                    @csrf
                                      <div class="form-group">
                                          <input type="text" class="form-control form-control-user"
                                              id="exampleInputEmail" aria-describedby="emailHelp"
                                              placeholder="Email" name="email">
                                      </div>
                                      <div class="form-group">
                                          <input type="password" class="form-control form-control-user"
                                              id="exampleInputPassword" placeholder="Password Baru" name="password">
                                      </div>
                                      <button type="submit" class="btn btn-primary btn-user btn-block">
                                          Reset
                                      </button>
                                  </form>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

          </div>

      </div>

  </div>
@endsection

