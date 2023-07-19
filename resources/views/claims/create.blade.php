@extends('layouts.app')
@push('plugin-styles')
<link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
  <style>
    .my-nav {
      display: grid;
      grid-template-columns: 1fr 1fr;
    }

    .map-div {
        height: 40vh;
    }

    #map {
        width: 100%;
        height: 100%;
    }

    .label-marker {
        position: absolute;
        top: 0;
        left: -40px;
        background: #FEDB00;
        padding: 3px;
        border-radius: 0.125rem;
    }

    .issue {
        color: #ff3366;
    }

    #pac-input {
        background-color: #fff;
        font-family: "Roboto", Helvetica, sans-serif;
        font-size: 15px;
        font-weight: 400;
        margin-top: 10px;
        text-overflow: ellipsis;
        width: 300px;
        padding: 0.5%;
        height: 40px;
        border: none;
        margin-right: 1%;
        filter: drop-shadow(0 4px 3px rgb(0 0 0 / 0.07)) drop-shadow(0 2px 2px rgb(0 0 0 / 0.06));
    }

    #pac-input:focus {
        border-color: #4d90fe;
    }

    .controls {
            position: absolute;
            margin-top: 10px;
            left: 35vw;
            background-color: #fff;
            border-radius: 2px;
            border: 1px solid transparent;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            box-sizing: border-box;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            height: 40px;
            margin-left: 10px;
            
            outline: none;
            padding: 0 11px 0 13px;
            z-index: 10;
            width: 400px;
            
        }

        .controls:focus {
            border-color: #4d90fe;
        }
  </style>
@endpush
@section('content')


<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('claims.show', Crypt::encrypt($insurance->id))}}">Claim</a></li>
      <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('claims.show', Crypt::encrypt($insurance->id))}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
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
                <h4 class="card-title">Add claim for vehicle {{$vehicle->plate_num}}</h4>
                <hr>
                <form action="{{ route('claims.store') }}"  method="POST" enctype="multipart/form-data" id="my-form">
                    @csrf
                    <input type="hidden" name="insurance_id" value="{{$insurance->id}}">
                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="title">Claim Number</label>
                            <input type="text" name="claim_number" class="form-control" id="claim_number" placeholder="Number" required>
                            <span class="issue" id="claim_number_error"></span>
                        </div>
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="claim_mileage">Claim Mileage</label>
                            <input type="text" name="claim_mileage" class="form-control" id="claim_mileage" placeholder="Mileage" required>
                            <span class="issue" id="claim_mileage_error"></span>
                        
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="platenum">Claim Date</label>
                            <input type="date" placeholder="contact" name="claim_date" class="form-control" id="claim_date" required>
                            <span class="issue" id="claim_date_error"></span>
                        </div>
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="platenum">Claim Approve Date</label>
                            <input type="date" placeholder="contact" name="claim_approve_date" class="form-control" id="claim_approve_date" required>
                            <span class="issue" id="claim_approve_date_error"></span>
                        
                        </div>
                    </div> 
                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="title">Garage</label>
                            <input type="text" id="claim_garage" name="claim_garage"  class="form-control"  placeholder="Garage" required>
                            <span class="issue" id="claim_garage_error"></span>
                        
                        </div>
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="title">Comment</label>
                            <input type="text" id="comment" name="comment"  class="form-control"  placeholder="Comment">
                            <span class="issue" id="comment_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="exampleFormControlFile1">Report</label>
                            <br>
                            <input type="file" class="form-control-file" id="myDropify" name="report"><br>
                            <span class="issue" id="myDropify_error"></span>

                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="exampleFormControlFile1">Statement</label>
                            <br>
                            <input type="file" class="form-control-file" id="myDropify2" name="statement">
                            <span class="issue" id="myDropify2_error"></span>

                        </div>
                    </div>

                    <div class="row mb-3 mt-3">
                        <label class="form-label text-success">Click map to pinpoint exact garage location</label>
                        
                        <div class="col-md-12 map-div">
                            <input
                            id="pac-input"
                            class=""
                            type="text"
                            placeholder="Search Box"
                        />
                            <div id="map"></div>
                        </div>
                        <span class="issue" id="map-error"></span>
                    </div>

                    <input type="hidden" name="claim_garage_lat" id="lat">
                    <input type="hidden" name="claim_garage_lng" id="lng">

                    <div class="text-center">

                        <button type="button" id="submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Add Claim</button>
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
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&libraries=places&v=weekly"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/dropify.js') }}"></script>
    
    <script defer>
        function initMap() {
            var myLatlng;
            let markers = [];
            var sett = "{{ $settings }}";

            myLatlng = { lat: '{{ $settings->lat }}' - 0, lng: '{{ $settings->lng }}' - 0 };

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: myLatlng,
            });
            // Create the initial InfoWindow.
            let infoWindow = new google.maps.InfoWindow();

            infoWindow.content = "Click the map to get Lat/Lng!",
            infoWindow.position = myLatlng,
            infoWindow.open(map);


            // Configure the click listener.
            map.addListener("click", (mapsMouseEvent) => {
                // Close the current InfoWindow.
                infoWindow.close();
                // Create a new InfoWindow.
                infoWindow = new google.maps.InfoWindow({
                    position: mapsMouseEvent.latLng,
                });
                infoWindow.setContent(
                    JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2)
                    
                );

                var j = mapsMouseEvent.latLng.toJSON();

                $('#lat').val(j.lat);
                $('#lng').val(j.lng);

                console.log(j.lat, j.lng);
                infoWindow.open(map);
            });

            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);

         
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
            // Bias the SearchBox results towards current map's viewport.
            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });

            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

                if (places.length == 0) {
                return;
                }

                // Clear out the old markers.
                markers.forEach((marker) => {
                marker.setMap(null);
                });
                markers = [];

                // For each place, get the icon, name and location.
                const bounds = new google.maps.LatLngBounds();

                places.forEach((place) => {
                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                const icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25),
                };

                // Create a marker for each place.
                markers.push(
                    new google.maps.Marker({
                    map,
                    icon,
                    title: place.name,
                    position: place.geometry.location,
                    })
                );
                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
                });
                map.fitBounds(bounds);
            });

            
        }

        initMap();
        window.initMap = initMap;

        $('#submit-btn').on('click', function(e) {
            console.log($('claim_mileage').val());
            if(!$('#claim_number').val()){
                $('#claim_number_error').text('field required');
                $('#claim_number').focus();
                e.preventDefault();
                return;
            } else{
                $('#claim_number_error').text('');

            }

            if(!$('#claim_mileage').val()){
                $('#claim_mileage_error').text('field required');
                $('#claim_mileage').focus();
                e.preventDefault();
                return;
            } else{
                $('#claim_mileage_error').text('');

            }


            if(!$('#claim_date').val()){
                $('#claim_date_error').text('field required');
                $('#claim_date').focus();
                e.preventDefault();
                return;
            } else{
                $('#claim_date_error').text('');

            }


            if(!$('#claim_approve_date').val()){
                $('#claim_approve_date_error').text('field required');
                $('#claim_approve_date').focus();
                e.preventDefault();
                return;
            } else{
                $('#claim_approve_date_error').text('');

            }


            if(!$('#claim_garage').val()){
                $('#claim_garage_error').text('field required');
                $('#claim_garage').focus();
                e.preventDefault();
                return;
            } else{
                $('#claim_garage_error').text('');

            }


            if(!$('#comment').val()){
                $('#comment_error').text('field required');
                $('#comment').focus();
                e.preventDefault();
                return;
            } else{
                $('#comment_error').text('');

            }


            if(!$('#myDropify').val()){
                $('#myDropify_error').text('file required');
                $('#myDropify').focus();
                e.preventDefault();
                return;
            } else{
                $('#myDropify_error').text('');

            }


            if(!$('#myDropify2').val()){
                $('#myDropify2_error').text('file required');
                $('#myDropify2').focus();
                e.preventDefault();
                return;
            } else{
                $('#myDropify2_error').text('');

            }

            if(!$('#lat').val()){
                $('#map-error').text('Click map to pinpoint exact location');
                e.preventDefault();
                return;
            } else{
                $('#map-error').text('');

            }

            $('#my-form').submit();
        })
    </script>
@endpush