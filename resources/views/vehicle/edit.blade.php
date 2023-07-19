@extends('layouts.app')

@push('plugin-styles')
    <script src="{{ asset('js/intlTelInput.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}"></script>
    <link href="{{ asset('css/intlTelInput.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <style>
        .my-nav {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .issue {
        color: #ff3366;
    }
    </style>
@endpush

@section('content')

<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('vehicles.index')}}">Vehicle</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('vehicles.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon>Cancel</a>
    </div>
</nav>

@if (Session::has('success'))
<div class="alert alert-success" role="alert" id="success">
    {{Session::get('success')}}
</div>
@endif

@if (Session::has('unsuccess'))
<div class="alert alert-danger" role="alert" id="danger">
    {{Session::get('unsuccess')}}
</div> 
@endif

@if ($errors->any())
<div class="alert alert-danger">
<ul>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>
</div>
@endif
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
              <h4 class="card-title">Edit Vehicle</h4>
              <hr>
              <form action="{{ route('vehicles.update', $vehicle->id) }}" method="POST" id="my-form">
                @csrf 
                @method('PATCH')
                <div class="row">
                  <div class="mb-3 col-md-6">
                    <label class="form-label" for="title">Vehicle Identifier</label>
                    <input type="text" name="title" class="form-control" id="veh_identifier" placeholder="Vehicle Identifier" value="{{ old('title', $vehicle->title) }}" required>
                    <span class="issue" id="vehicle_id"></span>
                  </div>
                  <div class="mb-3 col-md-6">
                    <label class="form-label" for="platenum">Registration Number</label>
                    <input type="text" name="platenum" class="form-control" id="reg_num" placeholder="Plate Number" value="{{ old('plate_num', $vehicle->plate_num) }}" required>
                    <span class="issue" id="reg_error"></span>
                  </div>
                  
                </div>

                <div class="row">
                  <div class="mb-3 col-md-6">
                      
                    <label class="form-label" for="inputState">Select Driver</label> <span class="text-success" style="margin-left: 10px">(Driver should have license)</span>
                    <select id="driver" class="form-select" name="driver" required> 
                      <option>select...</option>
                      @foreach ($drivers as $driver)
                        @if ($driver->license)
                          <option value="{{ $driver->id }}" {{ $driver->id == $vehicle->driver_id ? 'selected' : '' }}>{{ $driver->name }}</option>
                        @endif
                      @endforeach
                      
                    </select>
                    <span class="issue" id="driver_error"></span>
                  </div>

                  <div class="mb-3 col-md-6">
                    <label class="form-label" for="title">Number Of Seats</label>
                    <input type="text" name="num_of_seats" class="form-control" id="num_of_seats" placeholder="Number Of Seats" value="{{ old('num_of_seats', $vehicle->num_of_seats) }}" required>
                    <span class="issue" id="num_seats_error"></span>
                  </div>
                </div>


                <div class="row">
                    
                      <div class="mb-3 col-md-6">
                        <label class="form-label" for="inputState">Select Attendant</label>
                        <select id="attendant" class="form-select" name="attendant" required>
                            <option>select...</option>
                          @foreach ($attendants as $attendant)
                            <option value="{{ $attendant->id }}" {{ $attendant->id == $vehicle->attendant_id ? 'selected' : '' }}>{{ $attendant->name }}</option>
                          @endforeach
                        </select>
                        <span class="issue" id="attendant_error"></span>
                    </div>
                    
                    <div class="mb-3 col-md-6">
                        
                      <label class="form-label" for="inputState">Select Routes</label>
                      <select id="routes" class="js-example-basic form-select" name="routes[]" multiple data-width="100%" required>
                        @foreach ($routes as $route)
                          @php
                            $vehicle_routes = DB::table('vehicle_routes')->where('vehicle_id','=', $vehicle->id)->where('route_id','=', $route->id)->first();
                            var_dump($vehicle_routes)
                         @endphp
                          <option @if($vehicle_routes) @if($route->id == $vehicle_routes->route_id) selected @endif @endif value="{{ $route->id }}" >{{ $route->title }}</option>
                        @endforeach
                      
                      </select>
                      <span class="issue" id="routes_error"></span>
                    </div>
                </div>

                <div class="row">
                  

                  <div class="mb-3 col-md-6">
                    <label class="form-label" for="mileage">Mileage (KM)</label>
                    <input type="number" name="mileage" class="form-control" value="{{$vehicle->mileage}}" id="mileage" placeholder="5000" required>
                    <span class="issue" id="mileage_error"></span>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label" for="mileage">Last Service (KM)</label>
                    <input type="number" name="last_service" id="last_service" class="form-control" placeholder="1000" value="{{$vehicle->last_service}}">
                    <span class="issue" id="last_service_error"></span>
                  </div>


                  
                </div>

                <div class="row">
                  
                  <div class="mb-3 col-md-6">
                    <label class="form-label" for="service_interval">Service Interval (KM)</label>
                    <input type="number" name="service_interval" class="form-control" id="service_interval" placeholder="5000" required value="{{$vehicle->service_interval}}">
                    <span class="issue" id="service_interval_error"></span>
                  </div>
                </div>

                <div class="text-center">
                  <button id="submit-btn" type="button" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Save Changes</button>
                </div>
              </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush
@push('custom-scripts')
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script defer>
    $(function () {
        $('#submit-btn').on('click',(e) => {
          if (!$('#veh_identifier').val()) {
            $('#vehicle_id').text('field required');
            $('#veh_identifier').focus();
            return;
          } else {
              $('#vehicle_id').text('');
          }

          if (!$('#reg_num').val()) {
            $('#reg_error').text('field required');
            $('#reg_num').focus();
            return;
          } else {
            $('#reg_error').text('');
          }


          if ($('#driver').val() == 'select...') {
              $('#driver_error').text('field required');
              $('#driver').focus();
             
              return;
          } else {
              $('#driver_error').text('');
          }

          if (!$('#num_of_seats').val()) {
              $('#num_seats_error').text('field required');
              $('#num_of_seats').focus();
              e.preventDefault();
              return;
          } else {
              $('#num_seats_error').text('');
          }

          if ($('#attendant').val() == 'select...') {
              $('#attendant').focus();
              $('#attendant_error').text('field required');
              e.preventDefault();

              return;
          } else {
              $('#attendant_error').text('');
          }

          if ($('#routes').val().length <= 0) {
              $('#routes_error').text('field required');
              $('#routes').focus();
              e.preventDefault();

              return;
          } else {
              $('#routes_error').text('');
          }

          if (!$('#mileage').val()) {
              $('#mileage_error').text('field required');
              $('#mileage').focus();
              e.preventDefault();

              return;
          } else {
              $('#mileage_error').text('');
          }

          if (!$('#last_service').val()) {
              $('#last_service').focus();
              $('#last_service_error').text('field required');
              return;
          } else {
              $('#last_service_error').text('');
          }

          if (!$('#service_interval').val()) {
              $('#service_interval_error').text('field required');
              $('#service_interval').focus();
              e.preventDefault();

              return;
          } else {
              $('#service_interval_error').text('');
          }

          $('#my-form').submit();

        })
    })
</script>
@endpush