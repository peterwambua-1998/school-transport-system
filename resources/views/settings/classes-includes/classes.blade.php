<form action="{{ route('classes.store') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="exampleFormControlSelect1" class="form-label">Select Group</label>
                <select name="group" class="form-select" id="exampleFormControlSelect1">
                  <option selected disabled>select...</option>
                  @foreach ($groups as $group)
                    <option value="{{$group->id}}">{{$group->name}}</option>
                  @endforeach
                </select>
            </div>
            </div>
        <div class="col-md-6">
        <div class="mb-3">
            <label for="exampleFormControlSelect1" class="form-label">Select Class/Grade</label>
            <select name="name" class="form-select" id="exampleFormControlSelect1">
              <option selected disabled>select...</option>
              <option value="1">Grade 1</option>
              <option value="2">Grade 2</option>
              <option value="3">Grade 3</option>
              <option value="4">Grade 4</option>
              <option value="5">Grade 5</option>
              <option value="6">Grade 6</option>
              <option value="7">Class 7</option>
              <option value="8">Class 8</option>
            </select>
        </div>
        </div>
    </div>

    <div class="text-center">
      <button class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> save</button>
    </div>
    
</form>

