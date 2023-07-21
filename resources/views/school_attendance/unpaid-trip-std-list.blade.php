@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <style>
    .container {
      margin-top: 30px;
    }

    .title {
      text-align: center;
      margin-bottom: 20px;
    }

    .student-list {
      border: 1px solid #ddd;
      border-radius: 4px;
      overflow: hidden;
    }

    .student-list ul {
      list-style-type: none;
      padding: 0;
      margin: 0;
    }

    .student-list li {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 15px;
      border-bottom: 1px solid #ddd;
    }

    .student-list li:last-child {
      border-bottom: none;
    }

    .checkbox {
      margin-right: 10px;
    }

    .std-name {
        width: 15%;
    }
    .headers {
      color: #ef8e00;
    }

    form {
      padding: 0;
      margin: 0;
    }
    .delete-student:hover {
      cursor: pointer;
    }
  </style>
  @endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 75%">
      <li class="breadcrumb-item"><a href="#">School Trips</a></li>
      <li class="breadcrumb-item active" aria-current="page">Student List</li>
    </ol>

    <div style="width: 25%;display: flex; flex-direction: row-reverse; gap: 2%;">
      <a id="save" style="width: 50%" class="btn btn-success"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon>Save</a>
      <a style="width: 50%" href="{{route('teachertrips_show', Crypt::encrypt($schooltrip->id))}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon>Cancel</a>
    </div>
</nav>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
{{--  

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
          <div class="card-body">
              <h6 class="card-title">{{$schooltrip->trip_name}} Student List</h6>
              <hr>
              <div class="row" >
                <div id="test-list">
                  <div class="col-lg-12 mb-3">
                    <div class="input-group">
                      <input class="form-control search" type="text" placeholder="Search students...">
                      <button class="btn btn-light btn-icon" type="button" id="button-search-addon"><i data-feather="search"></i></button>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="student-list">
                      <ul class="list">
                        <li class="list-heading">
                          <span class="headers">Student</span>
                          <span class="headers">Checkbox</span>
                          <span class="headers">Vehicle</span>
                          <span class="headers">Remove</span>
                        </li>
                        <form action="{{route('teachertrips_addmystudents')}}" method="post" id="my-form">
                          @csrf
                          @if($students->isNotEmpty())
                            <input type="hidden" name="trip" value="{{$schooltrip->id}}">
                            @foreach ($students as $student)
                              <li>
                                <span class="std-name">{{ $student->first_name }} {{$student->last_name }}</span>
                                <div class="form-check">
                                  @php 
                                  $marked = '';
                                  $depature = App\Models\DepatureChecklist::where('schooltrip_id','=', $schooltrip->id)->where('student_id','=',$student->id)->first() ?? null; 
                                  if ($depature) {
                                    $marked = true;
                                  }
                                  @endphp
                                  <input class="form-check-input checkbox" name="students[]" @if($marked) checked  @endif type="checkbox" value="{{$student->id}}">
                                </div>
                                <div>
                                  <select class="form-select" name="vehicle[]">
                                    <option>select...</option>
                                    @foreach ($vehicles as $vehicle)
                                    <?php $veh = App\Models\Vehicle::where('id','=', $vehicle->vehicle_id)->first(); ?>
                                    @if ($depature)
                                    <option value="{{$veh->id}}" @if ($depature->vehicle_id == $veh->id) selected @endif>{{$veh->title}}</option>
                                    @else
                                    <option value="{{$veh->id}}">{{$veh->title}}</option>
                                    @endif
                                    @endforeach
                                  </select>
                                </div>

                                <div>
                                  <div>
                                    <input type="hidden"  class="student_id" value="{{$student->id}}">
                                    <input type="hidden" class="trip_id" value="{{$schooltrip->id}}">
                                    <i class="fa-solid fa-trash-can delete-student text-danger"></i>
                                  </div>
                                </div>
                              </li>
                            @endforeach
                          @else
                            <div class="text-center mt-3 mb-3">
                              <h6 class="card-title">Add Students</h6>
                            </div>
                          @endif
                        </form>
                      </ul>
                    </div>

                  </div>
                </div>
              </div>
          </div>
      </div>
  </div>
</div>
--}}

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h6 class="card-title">{{$schooltrip->trip_name}} Student List</h6>
            <hr>
            <div class="row" >
                <div class="col-lg-12 mb-3">
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Checkbox</th>
                                <th>Bus</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php $number = 1; ?>
                          <form action="{{route('teachertrips_addmystudents')}}" method="post" id="my-form">
                            @csrf
                            <input type="hidden" name="trip" value="{{$schooltrip->id}}">
                            @foreach ($students as $student)
                            <tr>
                              <td>{{$number}}</td>
                              <?php $number++; ?>
                              <td>{{ $student->first_name }} {{$student->last_name }}</td>
                              <td>
                                @php 
                                  $marked = '';
                                  $depature = App\Models\DepatureChecklist::where('schooltrip_id','=', $schooltrip->id)->where('student_id','=',$student->id)->first() ?? null; 
                                  if ($depature) {
                                    $marked = true;
                                  }
                                @endphp
                                <input class="form-check-input checkbox" name="students[]" @if($marked) checked  @endif type="checkbox" value="{{$student->id}}">
                              </td>
                              <td>
                                <select class="form-select" name="vehicle[]">
                                  <option>select...</option>
                                  @foreach ($vehicles as $vehicle)
                                    <?php $veh = App\Models\Vehicle::where('id','=', $vehicle->vehicle_id)->first(); ?>
                                    @if ($depature)
                                      <option value="{{$veh->id}}" @if ($depature->vehicle_id == $veh->id) selected @endif>{{$veh->title}}</option>
                                    @else
                                      <option value="{{$veh->id}}">{{$veh->title}}</option>
                                    @endif
                                  @endforeach
                                </select>
                              </td>
                              <td>
                                <div>
                                  <input type="hidden"  class="student_id" value="{{$student->id}}">
                                  <input type="hidden" class="trip_id" value="{{$schooltrip->id}}">
                                  <i class="fa-solid fa-trash-can delete-student text-danger"></i>
                                </div>
                              </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>                  
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

@endsection

@push('plugin-scripts')
  <script src="//cdnjs.cloudflare.com/ajax/libs/list.js/1.5.0/list.min.js"></script>
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script defer>
      
      $(function() {
        $('#save').on('click', () => {
          $('#my-form').submit();

        });

        $('.delete-student').each((i, e) => {
          $(e).on('click', () => {
            let e_parent = $(e).parent();
            let student = e_parent.find('.student_id').val(); 
            let trip = e_parent.find('.trip_id').val(); 
          
            let data = new FormData;
            data.append('_token','{{csrf_token()}}')
            data.append('student_id', student);
            data.append('trip_id', trip);
            $.ajax({
              type: 'post',
              url: "{{route('schooltrip_remove_student')}}",
              contentType: false,
              cache: false,
              processData: false,
              data: data,
              error: function(err){
                console.log(err);
              },
              success: function(response) {
                console.log(response);
                if (response == 1) {
                  location.reload();
                }
              }
            })
          
          })
        });

        console.log($('#test-list'));

        var monkeyList = new List('test-list', {
          valueNames: ['std-name'],
          page: 10,
          pagination: true
        });
      });
    </script>
@endpush
