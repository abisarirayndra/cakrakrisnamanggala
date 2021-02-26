                 <div class="card card-primary">
                    <div class="card-header">
                      <h4>Kerjakan dengan teliti !</h4>
                      {{$soal->links()}}
                    </div>
                    @foreach ($soal as $item)
                    <div class="card-body">
                      <h6>{{$item->nomor_soal}}.  </h6> <p>{{$item->soal}}</p>
                    </div>
                    <p>Please select your gender:</p>
                      <input type="radio" id="male" name="gender" value="male">
                      <label for="male">Male</label><br>
                      <input type="radio" id="female" name="gender" value="female">
                      <label for="female">Female</label><br>
                      <input type="radio" id="other" name="gender" value="other">
                      <label for="other">Other</label>
                    @endforeach
                 </div>
       