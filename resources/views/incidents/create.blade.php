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
      <li class="breadcrumb-item"><a href="{{route('offence.index')}}">Offence</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('offence.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
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
                <h4 class="card-title">Add Incident</h4>
                <hr>
                <form action="{{ route('incidents.store') }}" enctype="multipart/form-data"  method="POST">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="caused_by">Caused By</label>
                            <select class="form-select" name="caused_by" id="caused_by">
                                <option>select...</option>
                                <option value="student">Student</option>
                                <option value="parent">Parent</option>
                                <option value="driver">Driver</option>
                                <option value="attendant">Attendant</option>
                            </select>
                        </div>

                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label"for="assulter">Perpetrator</label>
                            <select class="form-select" name="assulter" id="user" class="form-select" required>
                                <option>select...</option>
                            </select>
                        </div>
                        
                    </div>


                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label"for="vehicle">Bus Reg No</label>
                            <select name="vehicle" id="vehicle" class="form-select" required>
                                <option>select...</option>
                                @foreach ($vehicles as $vehicle)
                                    <option value="{{$vehicle->id}}">{{$vehicle->plate_num}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label"for="trip">Trip</label>
                            <select name="trip" id="trip" class="form-select" required>
                                <option>select...</option>
                            </select>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label for="source" class="form-label">Source</label>
                            <select name="source" id="source" class="form-select" required>
                                <option>select...</option>
                                <option value="parent">Parent</option>
                                <option value="bus">Bus</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label"for="description">Date Occured</label>
                            <input type="date" name="date" id="date" class="form-control">
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label"for="description">type</label>
                            <select name="type" id="type" class="form-select" required>
                                <option>select...</option>
                                <option value="Misbehaviour">Misbehaviour</option>
                                <option value="Emergency">Emergency</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="" class="form-label">Description</label>
                            <textarea name="description" id="" cols="30" rows="10" class="form-control"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">Evidence Photo</label>
                            <input type="file" name="image" id="myDropify">
                        </div>
                    </div>

                    

                    <div class="text-center">
                        <button class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Save Incident</button>
                    </div>
        
                </form>
            </div>
        </div>
    </div>
</div>
    

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/dropify.js') }}"></script>
<script defer>
    $(function() {
        $(document).ready(function() {
            $('#user').select2();
        });

        $('#caused_by').on('change',(e) => {
            let d = new FormData;
            d.append('_token','{{csrf_token()}}');
            d.append('caused_by',e.target.value);
            $.ajax({
                type: 'POST',
                url: "/caused-by",
                processData: false,
                cache: false,
                contentType: false,
                data:d,
                error: function(err) {
                    console.log(err);
                },
                success: function(response) {
                    console.log(response);
                    $('#user').empty();
                    $('#user').append('<option>select...</option>');
                    if (e.target.value == 'student') {
                        for (let i = 0; i < response.length; i++) {
                            let template = ` 
                                <option value="${response[i].id}">${response[i].first_name}${response[i].last_name}</option>
                            `;
                        
                            $('#user').append(template);
                        }
                    } else {
                        for (let i = 0; i < response.length; i++) {
                            let template = ` 
                                <option value="${response[i].id}">${response[i].name}</option>
                            `;
                        
                            $('#user').append(template);
                        }
                    }
                    
                    
                }
            });
        });


        $('#vehicle').on('change',(e) => {
            let id = e.target.value;
            $.ajax({
                type: 'GET',
                url: `/get-trips-incident/${id}` ,
                processData: false,
                cache: false,
                contentType: false,
                error: function(err) {
                    console.log(err);
                },
                success: function(response) {
                    console.log(response);
                    $('#trip').empty();
                    $('#trip').append('<option>select...</option>');
                    for (let i = 0; i < response.length; i++) {
                        let template = ` 
                            <option value="${response[i].id}">${response[i].title}</option>
                        `;
                       
                        $('#trip').append(template);
                    }
                    
                }
            });
        })
    });
</script>
@endpush