@extends('layouts.app')
@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
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

    .issue {
        color: #ff3366;
    }
    #iw-container {
	    margin-bottom: 10px;
    }
    #iw-container .iw-title {
        font-size: 16px;
        font-weight: 400;
        padding: 10px;
        background-color: #ffc107;
        color: green;
        margin: 0;
        border-radius: 2px 2px 0 0;
    }
</style>
@endpush
@section('content')


<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('inspection.index')}}">Vehicle Inspection</a></li>
      <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('inspection.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
    </div>
</nav>

<div id="alters"></div>

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
                <h4 class="card-title">Add Vehicle Inspection</h4>
                <hr>
                <form action="{{ route('inspection.store') }}"  method="POST" id="inspection-form">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="title">Bus Reg. No </label>
                            <input type="text" readonly class="form-control" name="vehicle" id="" value="{{$vehicle->plate_num}}" readonly>
                            <input type="hidden" name="vehicle_id" value="{{$vehicle->id}}" readonly>
                        </div>
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="last_inspection">Last Inspection</label>
                            <input type="date"  name="last_inspection" class="form-control" id="last_inspection">
                        </div>
                        
                    </div>
                    <div class="row">
                       
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="next_inspection">Next Inspection </label>
                            <input type="date" name="next_inspection" class="form-control" id="next_inspection" required>
                            <span class="issue" id="next_inspection_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">Location Name </label>
                            <input type="text" placeholder="Location Name" name="location_name" id="pac-input" class="form-control">
                            <span class="issue" id="pac_input_error"></span>
                        </div>
                    </div>

                    <div class="row mb-3 mt-3">
                        <label class="form-label text-success">Click map or to pin point inspection location</label>
                        <div class="col-md-12 map-div">
                            <div id="map"></div>
                        </div>
                    </div>

                    <input type="hidden" name="lat" id="lat">
                    <input type="hidden" name="lng" id="lng">

                    <div class="text-center">
                        <button id="submit-btn" type="button" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Add Inspection</button>
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
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&libraries=places&v=weekly"></script>
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
                let contentInfo = `
                    <div id="iw-container">
                        <div class="iw-title">Inspection location marked</div>
                    </div>
                `;
                infoWindow.setContent(contentInfo);

                var j = mapsMouseEvent.latLng.toJSON();

                $('#lat').val(j.lat);
                $('#lng').val(j.lng);

                console.log(j.lat, j.lng);
                infoWindow.open(map);
            });

            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);


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

        $(function() {
            $('#submit-btn').on('click',() => {
                if (!$('#next_inspection').val()) {
                    $('#next_inspection_error').text('field required');
                    $('#next_inspection').focus('');
                    return;
                } else {
                    $('#next_inspection_error').text('');
                }

                if (!$('#pac-input').val()) {
                    $('#pac_input_error').text('field required');
                    $('#pac-input').focus('');
                    return;
                } else {
                    $('#pac_input_error').text('');
                }

                if (!$('#lat').val()) {
                    let template = `
                    <div class="alert alert-danger" role="alert" id="danger">
                        <p>Please click on the map to pinpoint exact inspection location</p>
                    </div>
                    `;
                    $('#alters').children().remove();
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    $('#alters').append(template);
                    return;
                }

                if (!$('#lng').val()) {
                    let template = `
                    <div class="alert alert-danger" role="alert" id="danger">
                        <p>Please click on the map to pinpoint exact inspection location</p>
                    </div>
                    `;
                    $('#alters').children().remove();
                    $('#alters').append(template);
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    return;
                }
                
                $('#inspection-form').submit();
            });
        })

    </script>
@endpush