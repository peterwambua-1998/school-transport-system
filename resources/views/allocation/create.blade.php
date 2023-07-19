@extends('layouts.app')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <style>
        .my-nav {
        display: grid;
        grid-template-columns: 1fr 1fr;
        }

        #map {
            width: 100%;
            height: 60vh;
        }
        
    </style>
@endpush

@section('content')

<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('students.index')}}">Student</a></li>
      <li class="breadcrumb-item active" aria-current="page">Vehicle Allocation</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('students.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon>Cancel</a>
    </div>
</nav>

<div class="alert alert-danger" role="alert" id="danger-two">
    
</div> 

@if (!$student->lat)
<div class="alert alert-danger" role="alert" id="dangers">
    <p>Parent has not provided location</p>
</div> 
@endif
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



<form action="{{route('allocation_save')}}" method="post">
    @csrf
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Vehicle allocation - pickup point.</h6>
                <hr class="mb-4 mt-4">
                <!-- student parent zone -->
                <div style="display: flex; gap: 10px; width: 100%" class="mb-3">
                    <div style="width: 100%">
                        <label class="form-label" for="">Student Name</label>
                        <input type="hidden" name="student" value="{{$student->id}}" id="student-select">
                        @php
                            $name = $student->name . ' ' . $student->last_name;
                        @endphp
                        <input class="form-control"  style="width: 100%" type="text"  value="{{$name}}">
                        
                        <input type="hidden" id="student_grade" value="{{$student->grade}}">
                    </div>
                    <div style="width: 100%">
                        <label class="form-label" for="">Parent Name</label>
                        <input type="hidden" name="parent" id="parent_id">
                        <input class="form-control"  style="width: 100%" type="text" id="parent_name">
                    </div>
                    <div style="width: 100%">
                        <label class="form-label" for="">Zone</label>
                        <input type="hidden" name="zone" id="zone_id">
                        <input class="form-control"  style="width: 100%" type="text" id="zone_name">
                    </div>
                </div>

                <!-- map -->
                <label class="mb-2 mt-4 form-label">Zones</label>
                <div class="mb-3" id="map"></div>

                <!-- to show color code for routes -->
                <div id="route-color-codes-div">
                    <div class="mt-5 mb-2">
                        <h6>Route Color Code</h6>
                    </div>
                    <div class="mb-5 " id="color-codes" style="display: flex; gap: 5%; ">
                        
                    </div>
                </div>
                

                <!-- for selecting route and vehicle -->
                <div id="route-vehicle-trips-div" style="display: flex; gap: 10px; width: 100%" class="mb-3">
                    <div style="width: 100%">
                        <label class="form-label" for="">Select Route</label>
                        <select  name="route" class="form-select" data-width="100%" id="routes">
                            <option>select route</option>
                            
                        </select>
                    </div>
                    <div style="width: 100%">
                        <label class="form-label" for="">Vehicles</label>
                        <select  name="vehicle"  id="vehicle_id" class="form-select" data-width="100%">
                            <option>select vehicle</option>
                        </select>
                    </div>

                    <div class="mb-3" style="width: 100%" id="trips-append"></div>
                </div>

                <div class="text-center">
                    <button type="submit" id="submit-allocation" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Save Allocation</button>
                </div>
                    
            </div>
        </div>
    </div>
</div>


</form>

<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Alert</h5>
        </div>
        <div class="modal-body">
            <h6>Student already has dropoff please select pickup.</h6>
            <br>
            <p>If you want to change please click change or continue to allocate dropoff vehicle.</p>
            <br>
            <p class="text-danger">Change will remove all data to allow for new allocation for both pickup and dropoff</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-inverse-success" data-bs-dismiss="modal">Change</button>
            <button id="change-location" type="button" class="btn btn-inverse-primary">Continue</button>
        </div>
      </div>
    </div>
</div>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
  <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js" ></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&v=weekly&libraries=drawing,geometry" ></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script defer>


let new_coordinates, coordinates, zones_array = [];
let map, infoWindow, lastElement, mapOptions, zones, zone_id, positions, studentInput, positionTwo;

let student_ids = $('#student-select').val();

$('#danger-two').hide();


$("#change-location").on("click", function() {
    location.href = `/allocation-dropoff/${student_ids}`;
});


var directionsService = new google.maps.DirectionsService();

let lats = '{{ $settings->lat }}' - 0;
let lngs = '{{ $settings->lng }}' - 0;

let loc = { lat: lats, lng: lngs };
let locTwo = { lat: -1.2914542344517588, lng: 36.88023857527228 };

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: loc,
        zoom: 10,
    });

    //check if student has am route
    $.ajax({
        type: "GET",
        url: `/check-allocation/${student_ids}` ,
        processData: false,
        contentType: false,
        cache: false,       
        error: function (err) {
            console.log(err);
        },
        success: function (response) {
            
            if (response == 1) {
                $('#myModal').modal('show');
                //location.href = '/allocation-dropoff';
            }
            
        }   
    });
    
    $.ajax({
        type: "GET",
        url: `/allocation/find-student/${student_ids}` ,
        processData: false,
        contentType: false,
        cache: false,       
        error: function (err) {
            console.log(err);
        },
        success: function (response) {
            //get student pickup and drop coordinates and create markers
            console.log(response);
            positions = {lat: response.student.lat - 0, lng: response.student.lng - 0};
            
            new google.maps.Marker({
                map,
                position: positions,
                label: "pickup"
            });
            

            //give the two parent input values ie id and name
            $('#parent_id').val(response.parent.id);
            $('#parent_name').val(response.parent.name);

            //then run get zone funtion to determine the correct zone
            getZoneAndGeoFence();
        }   
    });


    function getZoneAndGeoFence() {
        $.ajax({
            type: "GET",
            url: "{{ route('get_zone_geofences') }}",
            processData: false,
            contentType: false,
            cache: false,       
            error: function (err) {
                console.log(err);
            },
            success: function (response) {
                
                for (let i = 0; i < response.length; i++) { //outer array
                    x = 1;
                    var final_arr = [];
                    for (let t = 0; t < response[i].coordinates.length; t+=2) { //inner array
                        
                        const triangleCoords =  { 
                            lat: response[i].coordinates[t].corrdinates - 0, 
                            lng: response[i].coordinates[x].corrdinates - 0 
                        };
                        final_arr.push(triangleCoords);
                        x+=2;
                    }
                    
                    const bermudaTriangle = new google.maps.Polygon({
                        paths: final_arr,
                        strokeColor: "#FF0000",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: "#ADFF2F",
                        fillOpacity: 0.5,
                    });

                    var status = google.maps.geometry.poly.containsLocation(positions, bermudaTriangle);

                    if (status) {
                        bermudaTriangle.setMap(map);
                        zone_id = response[i].zone.id
                        $('#zone_id').val(zone_id);
                        $('#zone_name').val( response[i].zone.name);
                        zones_array.push(1);
                    }
                }
                if (zones_array.length == 0) {
                    console.log($('#zone_id').val());
                    $('#danger-two').text('No zone found with the supplied location.');
                    $('#danger-two').show('slow');
                    $('#route-vehicle-trips-div').hide('slow');
                    $('#route-color-codes-div').hide('slow');
                    $('#submit-allocation').hide('slow');
                }

                getZoneRoutes(zone_id);
            }   
        });
    }

    function getZoneRoutes(zone_id) {
        let destination, waypoint;
        
        let requests = [];

        $.ajax({
            type: "GET",
            url: `/get-zone-routes/${zone_id}`,
            processData: false,
            contentType: false,
            cache: false,       
            error: function (err) {
                console.log(err);

            },
            success: function (response) {

                for (let i = 0; i < response.length; i++) {
                    let waypoints = [];
                    let origin;
                    origin = response[i].polyline.origin;
                    var endorigin = origin.length - 1;
                    origin = origin.substr(1,  endorigin - 1);
                    origin = origin.split(" ");
                    origin = { lat: origin[0].replace(',','') - 0, lng: origin[1] - 0 };

                    var way_point_1 = response[i].polyline.way_point_1;
                    var ends = way_point_1.length - 1;
                    way_point_1 = way_point_1.substr(1,  ends - 1);
                    way_point_1 = way_point_1.split(" ");
                    way_point_1 = { lat: way_point_1[0].replace(',','') - 0, lng: way_point_1[1] - 0 };
                    waypoints.push({location: way_point_1, stopover: true});

                    var way_point_2 = response[i].polyline.way_point_2;
                    ends = way_point_2.length - 1;
                    way_point_2 = way_point_2.substr(1,  ends - 1);
                    way_point_2 = way_point_2.split(" ");
                    way_point_2 = { lat: way_point_2[0].replace(',','') - 0, lng: way_point_2[1] - 0 };
                    waypoints.push({location: way_point_2, stopover: true})

                    if (response[i].polyline.way_point_3) {
                        var way_point_3 = response[i].polyline.way_point_3;
                        ends = way_point_3.length - 1;
                        way_point_3 = way_point_3.substr(1,  ends - 1);
                        way_point_3 = way_point_3.split(" ");
                        way_point_3 = { lat: way_point_3[0].replace(',','') - 0, lng: way_point_3[1] - 0 };
                        waypoints.push({location: way_point_3, stopover: true});
                    }

                    if (response[i].polyline.way_point_4) {
                        var way_point_4 = response[i].polyline.way_point_4;
                        ends = way_point_4.length - 1;
                        way_point_4 = way_point_4.substr(1,  ends - 1);
                        way_point_4 = way_point_4.split(" ");
                        way_point_4 = { lat: way_point_4[0].replace(',','') - 0, lng: way_point_4[1] - 0 };
                        waypoints.push({location: way_point_4, stopover: true});
                    }

                    if (response[i].polyline.way_point_5) {
                        var way_point_5 = response[i].polyline.way_point_5;
                        ends = way_point_5.length - 1;
                        way_point_5 = way_point_5.substr(1,  ends - 1);
                        way_point_5 = way_point_5.split(" ");
                        way_point_5 = { lat: way_point_5[0].replace(',','') - 0, lng: way_point_5[1] - 0 };
                        waypoints.push({location: way_point_5, stopover: true});
                    }

                    if (response[i].polyline.way_point_6) {
                        var way_point_6 = response[i].polyline.way_point_6;
                        ends = way_point_6.length - 1;
                        way_point_6 = way_point_6.substr(1,  ends - 1);
                        way_point_6 = way_point_6.split(" ");
                        way_point_6 = { lat: way_point_6[0].replace(',','') - 0, lng: way_point_6[1] - 0 };
                        waypoints.push({location: way_point_6, stopover: true});
                    }
                    

                    if (response[i].polyline.way_point_7) {
                        var way_point_7 = response[i].polyline.way_point_7;
                        ends = way_point_7.length - 1;
                        way_point_7 = way_point_7.substr(1,  ends - 1);
                        way_point_7 = way_point_7.split(" ");
                        way_point_7 = { lat: way_point_7[0].replace(',','') - 0, lng: way_point_7[1] - 0 };
                        waypoints.push({location: way_point_7, stopover: true});
                    }

                    if (response[i].polyline.way_point_8) {
                        var way_point_8 = response[i].polyline.way_point_8;
                        ends = way_point_8.length - 1;
                        way_point_8 = way_point_8.substr(1,  ends - 1);
                        way_point_8 = way_point_8.split(" ");
                        way_point_8 = { lat: way_point_8[0].replace(',','') - 0, lng: way_point_8[1] - 0 };
                        waypoints.push({location: way_point_8, stopover: true});
                    }
                    
                    let my_colors = ["#f97316", "#ef4444", '#84cc16', '#10b981', '#0ea5e9', '#6366f1', '#d946ef', '#4c0519', '#451a03', '#fda4af', '#6d28d9', '#1d4ed8'];
                    calculateRoute(origin, waypoints, response[i].route, my_colors[i]);

                }

            
            }
        })
    }

    
    /*
    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 5; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
    */
    function calculateRoute(origin, waypoints, route, color) {
        var request = {
            origin: origin,
            destination: origin,
            waypoints: waypoints,
            optimizeWaypoints: true,
            travelMode: google.maps.DirectionsTravelMode.DRIVING
        };
        console.log(color);
        directionsService.route(request, function(result, status) {
            if (status == "OK") {
                var directionsDisplay = new google.maps.DirectionsRenderer({map:map,suppressMarkers:true, polylineOptions: {strokeColor: color,strokeOpacity: 0.7,strokeWeight: 5}});
                directionsDisplay.setDirections(result);
            }
        });  

        let templateColorCodes = 
        `
            <div style="width: 15%; border:1px solid #e2e8f0; padding: 5px; border-radius: 1px;">
                <span>${route.title}</span>
                <span style="float: right">
                    <span style="background: ${color}" class="badge">route</span>
                </span>
            </div>
        `;

        //append route to form
        let templateRoutes = ` 
            <option  value="${route.id}">${route.title}</span></option>
        `;

        $('#routes').append(templateRoutes);

        $('#color-codes').append(templateColorCodes);
    }

    
}

$('#routes').on('change', function() {
    let route_id = this.value;
    let student_id = $('#student-select').val();
    let data = new FormData;
    data.append('_token','{{csrf_token()}}');
    data.append('route_id',route_id);
    data.append('time','am');
    data.append('student_id',student_id);

    $.ajax({
        type: "POST",
        url: '/get-vehicle',
        processData: false,
        contentType: false,
        cache: false,
        data: data,
        error: function (err) {
            console.log(err)
        },
        success: function (response) {
            console.log(response);
            $('#vehicle_id').empty();
            $('#vehicle_id').append('<option>select vehicle</option>');
            for (let z = 0; z < response.length; z++) {
                let templateVehicle = ` 
                    <option  value="${response[z].id}">${response[z].title}</span></option>
                `;
                
                $('#vehicle_id').append(templateVehicle);
            }
           
        }
    });
    
});

$('#vehicle_id').on('change', (e) => {
    let vehicle_id = e.target.value;

    getTrips(vehicle_id);
});

function getTrips(id) {
    console.log($('#student_grade').val());
    let data = new FormData;
    data.append('_token','{{csrf_token()}}');
    data.append('time','am');
    data.append('vehicle', id);
    data.append('route', $('#routes').find(':selected').val());
    data.append('grade', $('#student_grade').val());
    $.ajax({
        type: "POST",
        url: '/get-vehicle-trips',
        processData: false,
        contentType: false,
        cache: false,
        data: data,
        error: function (err) {
            console.log(err)
        },
        success: function (response) {
            console.log(response);

            $('#trips-append').empty();

            $('#trips-append').append(response);

            if ($("#tripsss").length) {
                $("#tripsss").select2();
            }
        }
    });
}

initMap();



</script>
@endpush