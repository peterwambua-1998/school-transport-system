@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="{{route('vehicles.index')}}">Vehicles</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
  
    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff')
    <div style="width: 15%">
        <a href="{{route('vehicles.create')}}"><button type="button" class="btn btn-primary" style="width: 100%"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Add Vehicle</button></a>
    </div>
    @endif
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
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Vehicles Table</h6>
    
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Bus Reg. No</th>
                                <th>Driver</th>
                                <th>Students</th>
                                <th>Trips</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($vehicles as $vehicle)
                            <tr>
                                <td>{{ $number }}</td>
                                <?php $number++; ?>
                                <td>{{ $vehicle->title }}</td>
                                <td>{{ $vehicle->plate_num }}</td>
                                <td>{{ $vehicle->driver->name ?? 'no driver' }}</td>
                                
                                <td>
                                    @php
                                        $students_vehicle = DB::table('vehicle_students')->where('vehicle_id','=',$vehicle->id)->get();
                                        $final_array = [];
                                        foreach ($students_vehicle as $key => $st_vh) {
                                            $student = App\Models\Student::find($st_vh->student_id);
                                            if (count($final_array) > 1) {
                                                $ch = $final_array[$student->id] ?? null;
                                                if ($ch) {
                                                    continue;
                                                }
                                            }
                                            
                                            $obj = new stdClass;
                                            $obj->student_id = $student->id;
                                            $final_array[$student->id] = $obj;
                                        }
                                    @endphp
                                    {{count($final_array)}}
                                </td>
                                <td><a title="Vehicle trips" style="text-success" href="{{ route('trips.show', Crypt::encrypt($vehicle->id)) }}">{{ count($vehicle->trips) }} </a></td>
                                <td>
                                    @if ($vehicle->status === 1)
                                    <span class="badge bg-success">In-Service</span>
                                    @else
                                    <span class="badge bg-danger">Out-of-Service</span>
                                    @endif
                                </td>
                                <td style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr; gap: 10px">
                                    @if($vehicle->status == 1)

                                    <a href="#" class="span-delete mr-4"  data-bs-toggle="modal" data-bs-target="#vehicle{{$vehicle->id}}">
                                        <span class=""><i class="fa-solid fa-eye text-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View more details" title="View more details"></i></span>
                                    </a>

                                    <a href="#" data-bs-toggle="modal" data-bs-target="#stand-in{{$vehicle->id}}">
                                        <i class="fa-solid fa-repeat" style="font-weight: bolder; color: #e11d48" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add stand-in vehicle" title="Add stand-in vehicle"></i>
                                    </a>

                                    <a href="{{ route('trips_create', Crypt::encrypt($vehicle->id)) }}">
                                        <i class="fa-solid fa-square-plus"  style="font-size: 16px; color:#7c3aed" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add vehicle trips" title="Add vehicle trips"></i>
                                    </a>

                                    <a href="{{ route('tracker', Crypt::encrypt($vehicle->driver->id)) }}" class="span-delete">
                                        <span><i class="fa-solid fa-map-location-dot" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Vehicle tracker" title="Vehicle tracker"></i></span>
                                    </a>
                                    
                                    <a href="{{ route('edit_fence', Crypt::encrypt($vehicle->id)) }}" class="span-delete" >
                                        <span><i class="fa fa-map" aria-hidden="true" style="color:#0071f3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit vehicle geo fence" title="Edit vehicle geo fence"></i></span>
                                    </a>
                                    @endif

                                    
                                    @if (count($final_array) <= 0)
                                        <a href="{{ route('vehicles.edit', Crypt::encrypt($vehicle->id)) }}" class="" >
                                            <i class="fa fa-pencil" aria-hidden="true" style="color: rgb(2, 167, 2);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit vehicle details" title="Edit vehicle details"></i>
                                        </a>
                                        @if ($vehicle->status == 0)
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#activate{{$vehicle->id}}">
                                                <i class="fa-solid fa-toggle-off text-danger" style="font-size: 20px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate vehicle"></i>
                                            </a>
                                        @endif


                                        @if ($vehicle->status == 1)
                                            
                                            @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                            <a data-bs-toggle="modal" data-bs-target="#del{{$vehicle->id}}" type="button" class="span-delete delete" style="background: none; border: none" >
                                                <i class="fa-solid fa-toggle-on text-success" style="font-size: 20px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate vehicle" title="Deactivate vehicle" ></i>
                                            </a>
                                            @endif
                                        @endif
                                    @endif
                                    
                                </td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                        
                        
                    </table>
                </div>
                <div class="text-center">
                    
                </div>
            </div>       
        </div>
    </div>
</div>

<!-- Modal -->
@foreach ($vehicles as $vehicle)
<div class="modal fade" id="vehicle{{$vehicle->id}}" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="exampleModalCenterTitle">{{$vehicle->title}} Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
        <div class="modal-body">
            <ul class="list-group">
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Title:</span> <span>{{$vehicle->title}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Reg No:</span> <span>{{$vehicle->plate_num}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Driver:</span> <span>{{$vehicle->driver->name}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Attendant:</span> <span>{{$vehicle->attendant->name ?? 'not provided'}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Seats:</span> <span>{{$vehicle->num_of_seats}}</span>
                </li>
                
                @php
                $nums = 1;
                $routes = DB::table('vehicle_routes')->where('vehicle_id','=', $vehicle->id)->get();
                @endphp
               
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Routes:</span> 
                    @foreach ($routes as $route)
                    @php
                        $route_name = App\Models\Route::where('id','=', $route->route_id)->first();
                    @endphp
                    <span>
                        {{$route_name->title}}@if (count($routes) > 1),@endif
                    </span>
                    <?php $nums++ ?>
                    @endforeach
                </li>
                
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Am Trips:</span> 
                    @foreach ($vehicle->trips as $trip)
                    <span>
                        @if ($trip->time == "am")
                        {{date_format(date_create($trip->time_from),'h:i A' )}} -  {{date_format(date_create($trip->time_to),'h:i A' )}}@if (count($vehicle->trips) > 2),@endif
                        @endif
                    </span>
                    @endforeach
                </li>
                
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Pm Trips:</span> 
                    @foreach ($vehicle->trips as $trip)
                    <span>
                        @if ($trip->time == "pm")
                        {{date_format(date_create($trip->time_from),'h:i A' )}} - {{date_format(date_create($trip->time_to),'h:i A' )}}@if (count($vehicle->trips) > 2),@endif
                        @endif
                    </span>
                    @endforeach
                </li>

               
              </ul>
            </div>
            <div class="modal-footer">
                    
                    <a href="{{ route('trips.show', Crypt::encrypt($vehicle->id)) }}" class="btn btn-success" >Show Trips</a>
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach


 <!-- Modal -->
 @foreach ($vehicles as $vehicle)
 <div class="modal fade" id="del{{$vehicle->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
        <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="post">
             <div class="modal-header">
                 <h5 class="modal-title text-danger" id="exampleModalLabel">
                    <i class="far fa-lightbulb text-danger" style="margin-right: 10px;"></i>
                     Deactivate vehicle {{$vehicle->plate_num}}</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
             </div>
             <div class="modal-body">
                 <p>Vehicle will be inactive. Are you sure?</p>
                 
                 @csrf
                 @method('DELETE')
                 <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
             </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger">Deactivate</button>

             </div>
         </form>
       </div>
     </div>
 </div>
 @endforeach

 @foreach ($vehicles as $vehicle)
 <div class="modal fade" id="activate{{$vehicle->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
        <form action="{{ route('activate_vehicle') }}" method="post">
             <div class="modal-header">
                 <h5 class="modal-title text-success" id="exampleModalLabel">
                    <i class="far fa-lightbulb text-success" style="margin-right: 10px;"></i>
                     Activate vehicle {{$vehicle->plate_num}}</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
             </div>
             <div class="modal-body">
                 <p>Warning vehicle will be active. Are you sure?</p>
                
                 @csrf
                 <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
             </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Activate</button>

             </div>
         </form>
       </div>
     </div>
 </div>
@endforeach

@foreach ($vehicles as $key => $vehicle)
<div class="modal fade" id="stand-in{{$vehicle->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Stand-In For Vehicle {{$vehicle->plate_num}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            @php 
                $other_vehicles = App\Models\Vehicle::where('id','!=', $vehicle->id)->get();
            @endphp
            <form action="{{route('stand_in_vehicle')}}" method="post" class="stand-in-form">
                @csrf
                <input type="hidden" name="original_vehicle" value="{{$vehicle->id}}">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="vehicle_id" class="form-label">Stand-in Vehicle</label>
                        <select name="stand_in_vehicle" class="form-select vehicle_id">
                            <option>select...</option>
                            @foreach ($other_vehicles as $other_vehicle)
                                <option value="{{$other_vehicle->id}}">{{$other_vehicle->plate_num}}</option>
                            @endforeach
                        </select>
                        <span class="text-danger vehicle_id_error"></span>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control date_from">
                        <span class="text-danger date_from_error"></span>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="date_to" class="form-label">Date From</label>
                        <input type="date" name="date_to" class="form-control date_to">
                        <span class="text-danger date_to_error"></span>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="stand_in_status" class="form-label">Status</label>
                        <select name="stand_in_status" class="form-select stand_in_status">
                            <option>select...</option>
                            <option value="1">Active</option>
                            <option value="0">In-active</option>
                        </select>
                        <span class="text-danger stand_in_status_error"></span>
                    </div>
                </div>
                
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success save-stand-in">Save Changes</button>
        </div>
      </div>
    </div>
  </div>
@endforeach

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script>
        $(function() {
            $('.save-stand-in').each((i, e) => {
                $(e).on('click',(ev) => {
                    ev.preventDefault();
                    let stand_in_form = $(e).parent().prev().find('.stand-in-form');
                    let vehicle_id = $(e).parent().prev().find('.vehicle_id');
                    let date_from = $(e).parent().prev().find('.date_from');
                    let date_to = $(e).parent().prev().find('.date_to');
                    let status = $(e).parent().prev().find('.stand_in_status');
                    console.log(vehicle_id, date_from.val(), date_to);

                    if (vehicle_id.find(':selected').text() == "select...") {
                        vehicle_id.next().text('field required');
                        return false;
                    } else {
                        vehicle_id.next().text('');
                    }

                    if (! date_from.val()) {
                        date_from.next().text('field required');
                        return false;
                    } else {
                        date_from.next().text('');
                    }

                    if (! date_to.val()) {
                        date_to.next().text('field required');
                        return false;
                    } else {
                        date_to.next().text('');
                    }

                    if (status.find(':selected').text() == "select...") {
                        status.next().text('field required');
                        return false;
                    } else {
                        status.next().text('');
                    }

                    stand_in_form.submit();
                });
            })
        })
    </script>
@endpush