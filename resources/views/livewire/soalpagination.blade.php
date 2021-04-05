                 <div>
                  @foreach ($soal as $item)
                  @if (isset($item->foto))
                  <div class="row mt-4">
                      <img src="{{asset('storage/soal/'.$item->foto)}}" alt="" width="500">
                  </div>
                  <div class="row mt-4">
                      <h6>{{$item->nomor_soal}}. {!!($item->soal)!!}</h6>
                  </div>
                  @else
                  <div class="row mt-4">
                      <h6>{{$item->nomor_soal}}. </h6>{!!($item->soal)!!}
                  </div>
                  @endif
              <form action="{{route('pelajar.cat.jawaban')}}" method="GET">
                  @csrf
                  <input hidden name="soal" value={{$item->id}}>
                  <input hidden name="nomorsoal" value={{$item->nomor_soal}}>
                  <div class="form-group">
                      <div class="form-check">
                      <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_a}}">
                      <label class="form-check-label" for="exampleRadios1">
                          {!!$item->opsi_a!!}
                      </label>
                      </div>
                      <div class="form-check">
                      <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_b}}">
                      <label class="form-check-label" for="exampleRadios2">
                          {!!$item->opsi_b!!}
                      </label>
                      </div>
                      <div class="form-check">
                      <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_c}}">
                      <label class="form-check-label" for="exampleRadios2">
                          {!!$item->opsi_c!!}
                      </label>
                      </div>
                      <div class="form-check">
                      <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_d}}">
                      <label class="form-check-label" for="exampleRadios2">
                          {!!$item->opsi_d!!}
                      </label>
                      </div>
                      @if ($item->opsi_e == null)

                      @else
                      <div class="form-check">
                      <input class="form-check-input" type="radio" name="jawaban" value="{{$item->opsi_e}}">
                      <label class="form-check-label" for="exampleRadios2">
                          {!!$item->opsi_e!!}
                      </label>
                      </div>
                      @endif
                      <button type="submit" class="btn btn-success mt-4" id="simpan">Simpan Jawaban</button>
                  </div>
                  
                  </form>
                  @endforeach
                  {{$soal->links()}}
                 </div>
       