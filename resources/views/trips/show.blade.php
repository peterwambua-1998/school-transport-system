@extends('layouts.app')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <style>
        .my-nav {
        display: grid;
        grid-template-columns: 1fr 1fr;
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
      <a href="{{route('vehicles.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="arrow-back-circle-outline"></ion-icon>Back</a>
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
                <h6 class="card-title">Trips for vehicle {{$vehicle->plate_num}}</h6>
                <hr>
                <div class="row">
                    @foreach ($trips as $trip)
                    <div class="col-md-4 mb-3">
                        <ul class="list-group">
                            <li class="list-group-item active"><h6>{{$trip->title}}</h6></li>
                            <li class="list-group-item">Time: {{ $trip->time }}</li>
                            <li class="list-group-item">From: {{ $trip->time_from }}</li>
                            <li class="list-group-item">To: {{ $trip->time_to }}</li>
                            @php
                                $stds = count(App\Models\SAndT::where('trip_id', '=', $trip->id)->get())
                            @endphp
                            <li class="list-group-item">Students: {{$stds}}</li>
                            <li class="list-group-item">Route: {{App\Models\Route::where('id','=', $trip->route_id)->first()->title}}</li>
                            <?php $grades_trip = DB::table('grade_groups')->where('trip_id','=', $trip->id)->get() ?>
                            
                            <li class="list-group-item">{{ucfirst($tr->plural) ?? 'Grades'}}: 

                                @foreach ($grades_trip as $grade_trip)
                                    {{DB::table('student_classes')->where('id','=', $grade_trip->grade_id)->first()->name}},
                                @endforeach
                            </li>
                            @if ($stds <= 0)
                            <li class="list-group-item">
                                <a href="{{route('trips.edit', Crypt::encrypt($trip->id))}}" class="btn btn-success btn-sm" >Edit</a>
                                <form action="{{ route('trips.destroy', $trip->id) }}" method="post" style="display: inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" >Delete</button>
                                </form>
                            </li>
                            @endif
                        </ul>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

