@extends('layouts.app')

@push('plugin-styles')
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

<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('students.index')}}">School Trips</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
  
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
        @foreach ($schooltrips as $trip)
        @php
            $dests = DB::table('school_trips_destinations')->where('school_trip_id','=', $trip->id)->get();
        @endphp
        <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item active" style="font-weight: bold; font-size: 16px">{{ $trip->trip_name }}</li>
                            <li class="list-group-item"><span style="font-weight: bold;">Destination</span> <br> @foreach ($dests as $dest)
                                {{$dest->destination}} <br>
                            @endforeach</p></li>
                            <li class="list-group-item">{{ $settings->currency }} {{ $trip->price }}</li>
                            <li class="list-group-item"><a class="btn btn-success" href="{{ route('teachertrips_show', $trip->id) }}" >Show</a></li>
                        </ul>
                       
                    </div>    
                </div>   
            
        </div>
        @endforeach
    </div>

@endsection

