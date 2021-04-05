@extends('master.admin1')

@section('title')
<title>Edit Soal</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
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
                              <h1 class="h4 text-gray-900 mb-4">Edit Soal</h1>
                          </div>
                          <form method="POST" action="{{route('pengajar.cat.updatesoal', [$soal->id])}}">
                            @csrf
                            <div class="form-group">
                              <label for="soal">Soal</label>
                                <textarea class="ckeditor" name="soal" id="ckditor" cols="30" rows="10">{{$soal->soal}}</textarea>
                            </div>
                          <div class="form-group">
                            <label for="Opsi A">Opsi A</label>
                              <textarea name="opsi_a" class="ckeditor" id="ckditor" cols="30" rows="10">{{$soal->opsi_a}}</textarea>
                          </div>
                          <div class="form-group">
                            <label for="Opsi B">Opsi B</label>
                              <textarea name="opsi_b" class="ckeditor" id="ckditor" cols="30" rows="10">{{$soal->opsi_b}}</textarea>
                          </div>
                          <div class="form-group">
                            <label for="Opsi C">Opsi C</label>
                              <textarea name="opsi_c" class="ckeditor" id="ckditor" cols="30" rows="10">{{$soal->opsi_c}}</textarea>
                          </div>
                          <div class="form-group">
                            <label for="Opsi D">Opsi D</label>
                              <textarea name="opsi_d" class="ckeditor" id="ckditor" cols="30" rows="10">{{$soal->opsi_d}}</textarea>
                          </div>
                          <div class="form-group">
                            <label for="Opsi E">Opsi E</label>
                              <textarea name="opsi_e" class="ckeditor" id="ckditor" cols="30" rows="10">{{$soal->opsi_e}}</textarea>
                          </div>
                          <div class="form-group">
                            <label for="Kunci">Kunci</label>
                              <input type="text" class="form-control" name="tenggat" value="{{$soal->kunci}}">
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

      <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalLabel">Laravel Crop Image Before Upload using Cropper JS - NiceSnippets.com</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="img-container">
                  <div class="row">
                      <div class="col-md-10">
                          <img id="image" src="https://avatars0.githubusercontent.com/u/3456749">
                      </div>
                      <div class="col-md-2">
                          <div class="preview"></div>
                      </div>
                  </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" id="crop">Crop</button>
            </div>
          </div>
        </div>
      </div>
  </div>
@endsection

@section('js')
      <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
      <script>

        var $modal = $('#modal');
        var image = document.getElementById('image');
        var cropper;
          
        $("body").on("change", ".image", function(e){
            var files = e.target.files;
            var done = function (url) {
              image.src = url;
              $modal.modal('show');
            };
            var reader;
            var file;
            var url;
        
            if (files && files.length > 0) {
              file = files[0];
        
              if (URL) {
                done(URL.createObjectURL(file));
              } else if (FileReader) {
                reader = new FileReader();
                reader.onload = function (e) {
                  done(reader.result);
                };
                reader.readAsDataURL(file);
              }
            }
        });
        
        $modal.on('shown.bs.modal', function () {
            cropper = new Cropper(image, {
          aspectRatio: 0,
          viewMode: 0,
          preview: '.preview'
            });
        }).on('hidden.bs.modal', function () {
          cropper.destroy();
          cropper = null;
        });
        
        $("#crop").click(function(){
            canvas = cropper.getCroppedCanvas({
            width: 500,
            height: 250,
              });
        
            canvas.toBlob(function(blob) {
                url = URL.createObjectURL(blob);
                var reader = new FileReader();
                reader.readAsDataURL(blob); 
                reader.onloadend = function() {
                    var base64data = reader.result; 
        
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "image-cropper/upload",
                        data: {'_token': $('meta[name="_token"]').attr('content'), 'image': base64data},
                        success: function(data){
                            $modal.modal('hide');
                            alert("success upload image");
                        }
                      });
                }
            });
        })
        
        </script>
        <script type="text/javascript" src="{{asset('vendor/ckeditor/ckeditor.js')}}"></script>
        {{-- <script>
          var konten = document.getElementById("konten");
            CKEDITOR.replace(konten,{
            language:'en-gb'
          });
          CKEDITOR.config.allowedContent = true;
       </script> --}}
    
@endsection
                
             