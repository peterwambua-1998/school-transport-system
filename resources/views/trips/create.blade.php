@extends('layouts.app')

@push('plugin-styles')
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
      <li class="breadcrumb-item active" aria-current="page">Trips</li>
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
                <h4 class="card-title">Add Trips</h4>
                <hr>
                <form action="{{ route('trips.store') }}" method="POST" id="my-form">
                    @csrf 
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="title">Trip Title</label>
                            <input type="text" name="title" class="form-control" id="title" placeholder="Trip Title" required>
                            <span class="issue" id="title_error"></span>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="inputState">Select AM OR PM</label>
                            <select id="am_pm" class="form-control" name="route_time" required>
                                <option>select...</option>
                                <option value="am">am</option>
                                <option value="pm">pm</option>
                            </select>
                            <span class="issue" id="am_pm_error"></span>

                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">From</label>
                            <input type="time" name="from" class="form-control" id="from" placeholder="from" required>
                            <span class="issue" id="from_error"></span>
                        
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">To</label>
                            <input type="time" name="to" class="form-control" id="to" placeholder="to" required>
                            <span class="issue" id="to_error"></span>
                        
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ucfirst($tr->grade_class) ?? 'Grades'}}</label>
                            @if (count($grades) <= 0)
                              <p class="text-danger">Please add grades</p>  
                            @else
                            <select id="grades" class="js-example-basic-multiple form-select" name="grades[]" multiple="multiple" data-width="100%">
                                <option>select...</option>
                                @foreach ($grades as $grade)
                                    <option value="{{$grade->id}}">{{$grade->name}}</option>
                                @endforeach
                            </select>
                            @endif
                            <span class="issue" id="grades_error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="" class="form-label">Route</label>
                            @if (count($vehicle_routes) <= 0)
                              <p class="text-danger">Please add routes to this vehicle</p>  
                            @else
                            <select id="route" class="form-select" name="route" data-width="100%">
                                <option>select...</option>
                                @foreach ($vehicle_routes as $vehicle_route)
                                    <option value="{{$vehicle_route->route_id}}">{{App\Models\Route::where('id',$vehicle_route->route_id)->first()->title}}</option>
                                @endforeach
                            </select>
                            @endif
                            <span class="issue" id="route_error"></span>
                        </div>
                    </div>
                    
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}" required>

                    <div class="text-center">
                        <button type="button" id="submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Add Trip</button>
                    </div>
    
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/select2.js') }}"></script>
  <script defer>
    $(function() {
        $('#submit-btn').on('click',(e) => {
            if (!$('#title').val()) {
                $('#title_error').text('field required');
                e.preventDefault();
                $('#title').focus();
                return;
            } else {
                $('#title_error').text('');
            }

            if ($('#am_pm').find(':selected').text() == 'select...') {
                $('#am_pm_error').text('field required');
                e.preventDefault();
                $('#am_pm').focus();
                return;
            } else {
                $('#am_pm_error').text('');
            }

            if (!$('#from').val()) {
                $('#from_error').text('field required');
                e.preventDefault();
                $('#from').focus();
                return;
            } else {
                $('#from_error').text('');
            }


            if (!$('#to').val()) {
                $('#to_error').text('field required');
                e.preventDefault();
                $('#to').focus();
                return;
            } else {
                $('#to_error').text('');
            }

            if ($('#grades').val().length <= 0) {
                $('#grades_error').text('field required');
                e.preventDefault();
                $('#grades').focus();
                return;
            } else {
                $('#grades_error').text('');
            }

            if ($('#route').find(':selected').text() == 'select...') {
                $('#route_error').text('field required');
                e.preventDefault();
                $('#route').focus();
                return;
            } else {
                $('#route_error').text('');
            }

            $('#my-form').submit();

        });
    })
  </script>
@endpush