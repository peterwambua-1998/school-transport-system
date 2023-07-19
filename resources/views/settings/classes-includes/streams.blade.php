<form action="{{ route('streams.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    

    <div class="row">
        
        <div class="col-md-6">
        <div class="mb-3">
            <label for="exampleFormControlSelect1" class="form-label">Select Class/Grade</label>
            <select name="class" class="form-select" id="exampleFormControlSelect1">
              <option>select...</option>
              @foreach ($classes as $class)
                  <option value="{{$class->id}}">class/grade {{$class->name}}</option>
              @endforeach
            </select>
        </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                @if (count($teachers) <= 0)
                    <p class="text-danger pt-4">Please add teachers to the system</p>
                @else
                    <label for="exampleFormControlSelect1" class="form-label">Select Class Teacher</label>
                    <select name="class_teacher" class="form-select" id="exampleFormControlSelect1">
                        <option>select...</option>
                        @foreach ($teachers as $teacher)
                        <option value="{{$teacher->id}}">{{$teacher->name}}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
        
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="mb-3">
              <label class="form-label" for="">Stream</label>
              <input type="text" name="stream" class="form-control"  placeholder="Stream">
            </div>
        </div>
        
    </div>
    <div class="text-center">
    <button class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> save</button>
    </div>
</form>