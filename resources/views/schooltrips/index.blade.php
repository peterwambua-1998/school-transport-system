@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')


<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">School Trips</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
  
    <div style="width: 15%">
        <a class="btn btn-primary" style="float: right;border-radius:5px" href="{{ route('schooltrips.create') }}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff;font-size:16px;" name="add-circle-outline"></ion-icon> Add School Trip</a>
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
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">School Trips</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Depature</th>
                                <th>Return</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($schooltrips as $schooltrip)                                    
                            <tr>
                                <td>{{ $number }}</td>
                                <?php 
                                    $number++; 
                                    $dep_time = new \Carbon\Carbon($schooltrip->departure_time);
                                    $ret_time = new \Carbon\Carbon($schooltrip->return_time);
                                ?>
                                <td>{{ $schooltrip->trip_name }}</td>
                                <td>{{ date('d-M-Y', strtotime($schooltrip->trip_date) )}}</td>
                                <td>{{$dep_time->format('g:i A')}}</td>
                                <td>{{$ret_time->format('g:i A')}}</td>
                                <td>{{ $schooltrip->status }}</td>
                               
                                <td style="display: flex; gap: 20px;">
                                    @if ($schooltrip->active)
                                        
                                        <a href="#" class="span-delete mr-4" title="" data-bs-toggle="modal" data-bs-target="#schooltrip{{$schooltrip->id}}">
                                            <span class=""><i class="fa-solid fa-eye text-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View more details"></i></span>
                                        </a>

                                        @if ($schooltrip->route_changed)
                                        <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Route has changed" ><i class="fa-solid fa-circle-check" style="color:#818cf8"></i></a>
                                        @else
                                        <a href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Route not changed" ><i class="fa-solid fa-circle-xmark" style="color:#1e1b4b"></i></a>
                                        @endif

                                        @if ($schooltrip->status == 'paid')
                                        <a href="#" class="span-delete mr-4"  data-bs-toggle="modal" data-bs-target="#payment{{$schooltrip->id}}">
                                            <span class=""><i class="fa-solid fa-list" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Students paid for trip"></i></span>
                                        </a>
                                        @endif
                                        
                                        @if ($schooltrip->trip_route != NULL)

                                        <a href="{{ route('showroutepath', $schooltrip->id) }}" class="span-delete">
                                            <span><i class="fa fa-map text-warning" aria-hidden="true"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Shows school trip route path"></i></span>
                                        </a>
                                        @else
                                            @if ($schooltrip->has_more_destinations == 1)
                                                <a href="{{ route('schoolTripRouteMoreDests', $schooltrip->id) }}" class="span-delete">
                                                    <span><i class="fa fa-map" aria-hidden="true" style="color:#0071f3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add route"></i></span>
                                                </a>
                                            @else
                                                <a href="{{ route('schooltriproute', $schooltrip->id) }}" class="span-delete">
                                                    <span><i class="fa fa-map" aria-hidden="true" style="color:#0071f3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add route"></i></span>
                                                </a>
                                                
                                            @endif
                                        @endif

                                    @endif

                                    <a href="{{ route('schooltrips.edit',Crypt::encrypt($schooltrip->id)) }}" class="span-delete toola"  title="">
                                        <span><i class="fa fa-pencil" aria-hidden="true" style="color: rgb(2, 167, 2);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit school trip details"></i></span>
                                    </a>

                                    @if (Auth::user()->user_type != 'office staff')
                                        @if ($schooltrip->active)
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#del{{$schooltrip->id}}">
                                                <span><i class="fa-solid fa-toggle-on text-success" aria-hidden="true" style="font-size: 20px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title=" school trip"></i></span>
                                            </a>
                                        @else
                                            <a href="" data-bs-toggle="modal" data-bs-target="#activate{{$schooltrip->id}}">
                                                <i class="fa-solid fa-toggle-off text-danger" style="font-size: 20px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate school trip" title="Activate school trip"></i>
                                            </a>
                                        @endif
                                    @endif
                                   
                                    {{--
                                    <a href="{{ route('edit_fence', $vehicle->id) }}" class="span-delete" title="Edit Vehicle GeoFence">
                                        <span><i class="fa fa-map" aria-hidden="true" style="color:#0071f3"></i></span>
                                    </a>
                                    --}}

                                    
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

@foreach ($schooltrips as $schooltrip)
<div class="modal fade" id="schooltrip{{$schooltrip->id}}" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="exampleModalCenterTitle">{{$schooltrip->trip_name}} Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
        <div class="modal-body">
            <ul class="list-group mb-3">
                @php
                    $dep_time = new \Carbon\Carbon($schooltrip->departure_time);
                    $ret_time = new \Carbon\Carbon($schooltrip->return_time);
                @endphp
                <p class="mb-1">Trip</p>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Name:</span> <span>{{$schooltrip->trip_name}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Type:</span> <span>{{$schooltrip->status}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Date:</span> <span>{{date('d-M-Y', strtotime($schooltrip->trip_date))}}</span>
                </li>
                @if ($schooltrip->status == 'paid')
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Price:</span> <span>{{$settings->currency}} {{$schooltrip->price}}</span>
                </li>
                @endif
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Depature:</span> <span>{{$dep_time->format('g:i A')}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Return:</span> <span>{{$ret_time->format('g:i A')}}</span>
                </li>
                @if ($schooltrip->has_more_destinations)
                    @php
                        $destinations = DB::table('school_trips_destinations')->where('school_trip_id','=',$schooltrip->id)->get();
                    @endphp
                    <?php $num = 1 ?>
                    @foreach ($destinations as $item)
                        <li class="list-group-item">
                            <span class="ml-5 text-muted">Destination {{$num}}:</span> <span>{{$item->destination}}</span>
                        </li>
                        <?php $num++ ?>
                    @endforeach
                @else
                    @php
                        $dest = DB::table('school_trips_destinations')->where('school_trip_id','=',$schooltrip->id)->first();
                    @endphp
                    <li class="list-group-item">
                        <span class="ml-5 text-muted">Destination:</span> <span>{{$dest->destination}}</span>
                    </li>
                @endif
               
            </ul>

            <ul class="list-group mb-3">
                <p>{{$tr->plural}}</p>
                @if($schooltrip->school_trip_grades->isNotEmpty())
                @foreach ($schooltrip->school_trip_grades as $grade)
                    <?php $gr =  DB::table('student_classes')->where('id','=', $grade->grade_id)->first(); ?>
                    <li class="list-group-item">
                        <span class="ml-5 text-muted">{{$tr->grade_class}}:</span>
                        {{$gr->name}},
                    </li>
                @endforeach
                @else
                    <li class="list-group-item">Not provided</li>
                @endif
            </ul>

            <ul class="list-group mb-3">

                <!-- teachers -->
                <p class="mb-1">Teacher</p>
                @php
                    $trip_teachers = DB::table('schooltrip_teacher')->where('schooltrip_id','=', $schooltrip->id)->get();
                @endphp
                @if($trip_teachers->isNotEmpty()) 
                    @foreach ($trip_teachers as $trip_teacher)
                    <li class="list-group-item">
                        <span class="ml-5 text-muted">Teacher:</span> 
                        
                        <?php $teacher = App\Models\User::where('id','=', $trip_teacher->teacher_id)->first(); ?>
                        <span>
                            {{$teacher->name}},
                        </span>
                        
                    </li>
                    <li class="list-group-item">
                        <span class="ml-5 text-muted">Phone:</span> 
                        <span>{{$teacher->phone_num}}</span>
                    </li>
                    @endforeach
                @else
                    <li class="list-group-item">Not provided</li>
                @endif
                
            </ul>
            <ul class="list-group mb-3">


                <p class="mb-1">Vehicle</p>
                <!-- vehicles -->
                @php
                    $trip_vehicles = DB::table('schooltrip_vehicle')->where('schooltrip_id','=', $schooltrip->id)->get();
                @endphp
                @if($trip_vehicles->isNotEmpty())
                @foreach ($trip_vehicles as  $trip_vehicle)
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Vehicle:</span> 
                    
                    <?php $vehicle = App\Models\Vehicle::where('id','=', $trip_vehicle->vehicle_id)->first(); ?>
                        <span>
                            {{$vehicle->title}},
                        </span>
                </li>
                <!-- end vehicles -->

                @endforeach

                @else
                    <li class="list-group-item">Not provided</li>
                @endif
                
              </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
                <a href="{{route('teachertrips_show', Crypt::encrypt($schooltrip->id))}}" class="btn btn-success">Allocate Bus</a>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal -->
@foreach ($schooltrips as $schooltrip)
@php
    $payments = DB::table('school_trip_payment_tables')->where('schooltrip_id','=', $schooltrip->id)->get();
@endphp
<div class="modal fade" id="payment{{$schooltrip->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Paid Students {{count($payments)}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                @if (count($payments) <= 0)
                    <p>No student has paid for this trip.</p>
                @else
                <table id="dataTableExample" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Student</th>
                      <th>Date Paid</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $n = 1; ?>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>{{$n}}</td>
                            <?php $n++; ?>
                            <td>
                                @php
                                    $student = App\Models\Student::where('id','=', $payment->student_id)->first() ?? '';
                                @endphp
                                @if ($student)
                                    {{ $student->first_name }} {{ $student->last_name }}
                                @endif
                                </td>
                            <td>{{$payment->date}}</td>
                        </tr>
                    @endforeach
                  </tbody>
                </table>
                @endif
              </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
          <a href="{{route('add_std_to_schooltrip', Crypt::encrypt($schooltrip->id))}}" class="btn btn-success">Allocate Bus</a>
        </div>
      </div>
    </div>
</div>
@endforeach

@foreach ($schooltrips as $schooltrip)
<div class="modal fade" id="del{{$schooltrip->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       <form action="{{ route('schooltrips.destroy', $schooltrip->id) }}" method="post" style="display: inline-block">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="exampleModalLabel">
                   <i class="far fa-lightbulb text-danger" style="margin-right: 20px;"></i>
                    
                    Deactivate {{$schooltrip->trip_name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Warning record will be inactive. Are you sure?</p>
                @if ($schooltrip->status == 'paid')
                <p>Will not deactivate if record has payments against it.</p>
                @endif
                @csrf
                @method('DELETE')
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-danger">Deactivate</button>
               <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
      </div>
    </div>
</div>
@endforeach

@foreach ($schooltrips as $schooltrip)
<div class="modal fade" id="activate{{$schooltrip->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       <form action="{{ route('activate_schooltrip') }}" method="post" style="display: inline-block">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="exampleModalLabel">
                   <i class="far fa-lightbulb text-success" style="margin-right: 20px;"></i>
                    
                    Deactivate {{$schooltrip->trip_name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Warning record will be active. Are you sure?</p>
                @if ($schooltrip->status == 'paid')
                <p>Payments can be made against it.</p>
                @endif
                @csrf
                <input type="hidden" name="schooltrip_id" value="{{$schooltrip->id}}">
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-success">Activate</button>
               <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
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
@endpush