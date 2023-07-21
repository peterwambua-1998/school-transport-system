@extends('layouts.app')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
    <style>
        .my-nav {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
    </style>
@endpush

@section('content')

<nav class="page-breadcrumb my-nav">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('teachertrips')}}">School Trips</a></li>
      <li class="breadcrumb-item active" aria-current="page">Management</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse; gap: 2%;">
        <a href="{{route('schooltrips.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="arrow-back-circle-outline"></ion-icon>Back</a>
        <a class="btn btn-primary" href="{{route('add_std_to_schooltrip', Crypt::encrypt($schooltrip->id))}}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px" name="add-circle-outline"></ion-icon> Add Students</a>
        {{-- <a href="{{ route('teachertrips_markattendance', $schooltrip->id) }}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px" name="clipboard-outline"></ion-icon> Depature</a> --}}
        {{-- <a href="{{ route('teachertrips_markattendancereturn', $schooltrip->id) }}" class="btn btn-info"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px" name="clipboard-outline"></ion-icon> Return</a> --}}
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

    <div class="row">

        <div class="col-lg-8 col-md-8 col-sm-12">

            <nav>
                <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
                    <li class="nav-item text-center" style="width: 50%;">
                        <a class="nav-item nav-link active" class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" role="tab" aria-controls="home" aria-selected="false">Depature Checklist</a>
                    </li>
                    <li class="nav-item text-center" style="width: 50%;">
                        <a class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" role="tab" aria-controls="contact" aria-selected="true">Return Checklist</a>
                    </li>
                </ul>
            </nav>

            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="dataTableExample2" data-ordering="false">
                                    <thead>
                                        <tr>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Attendance</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($depatureChecklists as $depatureChecklist)                                    
                                        <tr>
                                            
                                            <td>{{ $depatureChecklist->student->first_name }}</td>
                                            <td>{{ $depatureChecklist->student->last_name }}</td>
                                            <td>{{ $depatureChecklist->attendance }}</td>
                                            <td>{{ date('d-M-Y', strtotime($depatureChecklist->date) ) }}</td>
                                            
                                        </tr>
                                        @endforeach
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>    
                    </div> 
                </div>
                <div  class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                
                                <table class="table table-striped"  id="dataTableExample" data-ordering="false">
                                    <thead >
                                        <tr>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Attendance</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($returnChecklists as $depatureChecklist)                                    
                                        <tr>
                                            
                                            <td>{{ $depatureChecklist->student->first_name }}</td>
                                            <td>{{ $depatureChecklist->student->last_name }}</td>
                                            <td>{{ $depatureChecklist->attendance }}</td>
                                            <td>{{  date('d-M-Y', strtotime($depatureChecklist->created_at) ) }}</td>
                                            
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
        

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item active">{{ $schooltrip->trip_name }}</li>
                        @if ($schooltrip->has_more_destinations)
                            @php
                                $destinations = DB::table('school_trips_destinations')->where('school_trip_id','=',$schooltrip->id)->get();
                            @endphp
                            <?php $num = 1 ?>
                            @foreach ($destinations as $item)
                                <li class="list-group-item">
                                    <span class="ml-5 text-muted">Destination {{$num}}:</span> <span>{{$item->destination}}</span> <br>
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
                        @php
                        $trip_vehicles = DB::table('schooltrip_vehicle')->where('schooltrip_id','=', $schooltrip->id)->get();
                        @endphp
                        <li class="list-group-item"><span  class="ml-5 text-muted">Vehicle</span> <br> @foreach ($trip_vehicles as $trip_vehicle)
                            <?php $vehicle = App\Models\Vehicle::where('id','=', $trip_vehicle->vehicle_id)->first(); ?>
                            {{ $vehicle->title }} ({{ $vehicle->plate_num }})<br>
                            @endforeach  
                        </li>
                        @if ($schooltrip->status == 'paid')
                        <li class="list-group-item"><span  class="ml-5 text-muted">Price</span> :{{ $settings->currency ?? 'USD' }} {{ $schooltrip->price }}</li>
                        @endif
                        
                        <li class="list-group-item">
                            <span class="text-muted mr-4">{{ucfirst($tr->plural) ?? 'Grades'}}: </span>
                            @foreach ($grades as $grade)
                                {{$grade->name}},
                            @endforeach
                        </li>
                      </ul>
                </div>
            </div>
        </div>

        {{--
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <p style="font-weight: bold; font-size: 16px">{{ $schooltrip->trip_name }}</p>  
                    <p><span style="font-weight: bold; font-size: 16px">Destination</span> : {{ $schooltrip->destination }}</p>
                    @php
                        $trip_vehicles = DB::table('schooltrip_vehicle')->where('schooltrip_id','=', $schooltrip->id)->get();
                    @endphp
                    <p><span style="font-weight: bold; font-size: 16px">Vehicle</span> : @foreach ($trip_vehicles as $trip_vehicle)
                        <?php $vehicle = App\Models\Vehicle::where('id','=', $trip_vehicle->vehicle_id)->first(); ?>
                        {{ $vehicle->title }} ({{ $vehicle->plate_num }})
                    @endforeach </p>
                    @if ($schooltrip->price)
                    <p><span style="font-weight: bold; font-size: 16px">{{ $settings->currency ?? 'USD' }}</span> : {{ $schooltrip->price }} </p>  
                    @endif
                    <p><span style="font-weight: bold; font-size: 16px">For: </span>
                    @if ($schooltrip->grade == 'general')
                        All students
        
                    @else
                    Grade {{ $schooltrip->grade }}
        
                    @endif
                    </p>
                    
                </div>    
            </div>   
        </div>
        --}}
    </div>

@endsection


@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/data-table.js') }}"></script>
@endpush