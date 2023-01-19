@extends('master.admin1')

@section('title')
    <title>Reset Password</title>
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
                                  <form class="user" action="{{route('submit_email')}}" method="post">
                                    @csrf
                                      <div class="form-group">
                                          <input type="text" class="form-control form-control-user"
                                              id="exampleInputEmail" aria-describedby="emailHelp"
                                              placeholder="Email" name="email" required>
                                      </div>
                                      <div class="form-group">
                                        <input type="text" class="form-control form-control-user"
                                            placeholder="Token / No. ID" name="token" required>
                                    </div>
                                      <button type="submit" class="btn btn-warning btn-user btn-block">
                                          Submit
                                      </button>
                                  </form>
                                @if (session()->has('success'))
                                    <div class="alert alert-success mt-3">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                        <p>{{ session()->get('success') }}</p>
                                    </div>
                                @endif
                                @if (session()->has('error'))
                                    <div class="alert alert-danger mt-3">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                        <p>{{ session()->get('error') }}</p>
                                    </div>
                                @endif
                              </div>

                          </div>
                      </div>
                  </div>
              </div>

          </div>

      </div>

  </div>
@endsection

