<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Blank Page &mdash; Stisla</title>

  <link rel="icon" type="image/x-icon" href="{{asset('assets/img/favicon.ico')}}" />

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- CSS Libraries -->

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{asset('admin/assets/css/dataTable.min.css')}}">
  <link rel="stylesheet" href="{{asset('admin/assets/css/style.css')}}">
  <link rel="stylesheet" href="{{asset('admin/assets/css/components.css')}}">
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg-pengajar"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
          </ul>
        </form>
        <ul class="navbar-nav navbar-right">
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <img alt="image" src="{{asset('admin/assets/img/avatar/avatar-1.png')}}" class="rounded-circle mr-1">
            <div class="d-sm-none d-lg-inline-block">Hai, {{$user}}</div></a>
            <div class="dropdown-menu dropdown-menu-right">
              <a href="{{route('logout')}}" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>
      <div class="main-sidebar">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand mt-2">
            <a href=""><img src="{{asset('assets/img/logos/krisna.png')}}" width="50" alt=""></a>
            <h6>Cakra Krisna Manggala</h6>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <a href=""><img src="{{asset('assets/img/favicon.ico')}}" width="50" alt=""></a>
          </div>
          <ul class="sidebar-menu mt-4">
              {{-- <li class="menu-header">Starter</li> --}}
              <li class="nav-item dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-columns"></i> <span>Layout</span></a>
                <ul class="dropdown-menu">
                  <li><a class="nav-link" href="layout-default.html">Default Layout</a></li>
                  <li><a class="nav-link" href="layout-transparent.html">Transparent Sidebar</a></li>
                  <li><a class="nav-link" href="layout-top-navigation.html">Top Navigation</a></li>
                </ul>
              </li>
              <li class="active"><a class="nav-link" href="blank.html"><i class="far fa-square"></i> <span>CAT</span></a></li>
              <li class="nav-item dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-th"></i> <span>Bootstrap</span></a>
                <ul class="dropdown-menu">
                  <li><a class="nav-link" href="bootstrap-alert.html">Alert</a></li>
                  <li><a class="nav-link" href="bootstrap-badge.html">Badge</a></li>
                  <li><a class="nav-link" href="bootstrap-breadcrumb.html">Breadcrumb</a></li>
                  <li><a class="nav-link" href="bootstrap-buttons.html">Buttons</a></li>
                  <li><a class="nav-link" href="bootstrap-card.html">Card</a></li>
                  <li><a class="nav-link" href="bootstrap-carousel.html">Carousel</a></li>
                  <li><a class="nav-link" href="bootstrap-collapse.html">Collapse</a></li>
                  <li><a class="nav-link" href="bootstrap-dropdown.html">Dropdown</a></li>
                  <li><a class="nav-link" href="bootstrap-form.html">Form</a></li>
                  <li><a class="nav-link" href="bootstrap-list-group.html">List Group</a></li>
                  <li><a class="nav-link" href="bootstrap-media-object.html">Media Object</a></li>
                  <li><a class="nav-link" href="bootstrap-modal.html">Modal</a></li>
                  <li><a class="nav-link" href="bootstrap-nav.html">Nav</a></li>
                  <li><a class="nav-link" href="bootstrap-navbar.html">Navbar</a></li>
                  <li><a class="nav-link" href="bootstrap-pagination.html">Pagination</a></li>
                  <li><a class="nav-link" href="bootstrap-popover.html">Popover</a></li>
                  <li><a class="nav-link" href="bootstrap-progress.html">Progress</a></li>
                  <li><a class="nav-link" href="bootstrap-table.html">Table</a></li>
                  <li><a class="nav-link" href="bootstrap-tooltip.html">Tooltip</a></li>
                  <li><a class="nav-link" href="bootstrap-typography.html">Typography</a></li>
                </ul>
              </li>
            </ul>
            </div>
        </aside>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="row">
                  <div class="card-header">
                    <div class="col-xl-6">
                        @foreach ($nama as $item)
                            <h4>Paket Soal {{$item->nama_paket}}</h4>
                        @endforeach
                    </div>
                    <div class="col-xl-6">
                      <button type="button" class="btn btn-sm btn-primary float-right" data-toggle="modal" data-target="#myModal">Tambah Tema</button>
                    </div>
                  </div>
                </div>
                
                {{-- <div class="card">
                  <div class="card-header">
                    <h4>Horizontal Form</h4>
                  </div>
                  <div class="card-body">
                    <div class="form-row">
                      <div class="form-group col-md-6">
                        <label for="inputEmail4">Email</label>
                        <input type="email" class="form-control" id="inputEmail4" placeholder="Email">
                      </div>
                      <div class="form-group col-md-6">
                        <label for="inputPassword4">Password</label>
                        <input type="password" class="form-control" id="inputPassword4" placeholder="Password">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="inputAddress">Address</label>
                      <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
                    </div>
                    <div class="form-group">
                      <label for="inputAddress2">Address 2</label>
                      <input type="text" class="form-control" id="inputAddress2" placeholder="Apartment, studio, or floor">
                    </div>
                    <div class="form-row">
                      <div class="form-group col-md-6">
                        <label for="inputCity">City</label>
                        <input type="text" class="form-control" id="inputCity">
                      </div>
                      <div class="form-group col-md-4">
                        <label for="inputState">State</label>
                        <select id="inputState" class="form-control">
                          <option selected>Choose...</option>
                          <option>...</option>
                        </select>
                      </div>
                      <div class="form-group col-md-2">
                        <label for="inputZip">Zip</label>
                        <input type="text" class="form-control" id="inputZip">
                      </div>
                    </div>
                    <div class="form-group mb-0">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="gridCheck">
                        <label class="form-check-label" for="gridCheck">
                          Check me out
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer">
                    <button class="btn btn-primary">Submit</button>
                  </div>
                </div> --}}

                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="table-1">
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Tema</th>
                          <th>Durasi</th>
                          <th>Dibuat Tanggal</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                          @php
                              $no = 1;
                          @endphp
                          @foreach ($tema as $item)
                          <td>{{$no++}}</td>
                          <td>{{$item->tema}}</td>
                          <td>{{$item->durasi}} menit</td>
                          <td>{{$item->created_at}}</td>
                          <td>
                            <a href="{{route('pengajar.cat.soal', [$item->id])}}"><button type="button" class="btn btn-sm btn-success">Soal</button></a>
                            <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#">hapus</button>
                          </td>
                      @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="section-body">
          </div>
        </section>
      </div>

      {{-- Modal --}}
      <div class="modal fade" tabindex="-1" role="dialog" id="myModal">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Tambah Tema Soal</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form method="POST" action="{{route('pangajar.cat.buattema')}}">
                @csrf
                <div class="row">
                  <div class="form-group col-12">
                    <label for="tema">Tema</label>
                      <select class="form-control" name="tema" required>
                        <option value="matematika">Matematika</option>
                        <option value="bahasa inggris">Bahasa Inggris</option>
                        <option value="psikologi">Psikologi</option>
                        <option value="wawasan umum">Wawasan Umum</option>
                      </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="durasi">Durasi</label>
                    <input placeholder="Dalam Satuan Menit" type="number" class="form-control" name="durasi" autofocus required>
                    <input type="number" class="form-control" name="paket_id" value="{{$paket_id}}" readonly hidden required>
                  <div class="invalid-feedback">
                  </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary text-light">Tambah</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      {{-- Footer --}}
      <footer class="main-footer">
        <div class="footer-left">
          Developed With Love by IT Staff Cakra Krisna Manggala
        </div>
      </footer>
    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src="{{asset('admin/assets/js/stisla.js')}}"></script>
  <script src="{{asset('admin/assets/js/dataTable.min.js')}}"></script>

  <!-- JS Libraies -->
  <script>
    $(document).ready( function () {
    $('#table-1').DataTable();
    });

    $('#myModal').on('shown.bs.modal', function() {
        $(document).off('focusin.modal');
    })
  </script>

  <!-- Template JS File -->
  <script src="{{asset('admin/assets/js/scripts.js')}}"></script>
  <script src="{{asset('admin/assets/js/custom.js')}} "></script>

  <!-- Page Specific JS File -->
</body>
</html>
