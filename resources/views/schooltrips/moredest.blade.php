@extends('layouts.app')
@push('plugin-styles')
<style>
    .page-content {
        padding: 0px !important;
    }

    #map {
        width: 100%;
        height: 87%;
    }
</style>
@endpush
@section('content')


<div class="p-2" style="border-bottom: 2px solid #f3f4f6; display: grid; grid-template-columns:67% 1fr; gap: 2%; background:#f3f4f6">
    <div class="input-group" style="width: 100%;">
        <div class="input-group-text">
          <i data-feather="search"></i>
        </div>
        <input type="text" class="form-control" id="search-place" placeholder="Search here...">
    </div>
    <div style="width: 100%">
        <a href="{{url('driver-myschooltrips/schooltrips/schooltripshow', $schooltrip->id)}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="arrow-back-circle-outline"></ion-icon> Back</a>
        <button class="btn btn-primary" id="get-directions">
            
            Get Path</button>
        <button class="btn btn-danger" id="clear-path">
            
            clear</button>
        <button class="btn btn-success" id="save-btn">
            
            Save Path</button>
        
    </div>
</div>


<!-- map div -->
<div id="map"></div>

<!-- form to store coordinates -->
<form action="{{ route('saveroutepath') }}" method="post" id="my-form">
    @csrf
    <input type="hidden" name="schooltrip_id" value="{{ $schooltrip->id }}">
    <div style="display: none">

        <input type="text" name="origin" autocomplete="off" class="form-control mb-2 origin" placeholder="Origin Lat,Lng" style="font-size: 11px" id="orgin-input" required>
        <label for="" style="font-size: 11px">Waypoint 1</label>
        <input type="text" name="waypoint_1" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Origin Lat,Lng" style="font-size: 11px">



        <label for="" style="font-size: 11px">Waypoint 2</label>
        <input type="text" name="waypoint_2" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Waypoint Lat,Lng" style="font-size: 11px">


        <label for="" style="font-size: 11px">Waypoint 3</label>
        <input type="text" name="waypoint_3" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Waypoint Lat,Lng" style="font-size: 11px">

        <label for="" style="font-size: 11px">Waypoint 4</label>
        <input type="text" name="waypoint_4" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Waypoint Lat,Lng" style="font-size: 11px">

        <label for="" style="font-size: 11px">Waypoint 5</label>
        <input type="text" name="waypoint_5" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Waypoint Lat,Lng" style="font-size: 11px">

        <label for="" style="font-size: 11px">Waypoint 6</label>
        <input type="text" name="waypoint_6" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Waypoint Lat,Lng" style="font-size: 11px">

        <label for="" style="font-size: 11px">Waypoint 7</label>
        <input type="text" name="waypoint_7" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Waypoint Lat,Lng" style="font-size: 11px">

        <label for="" style="font-size: 11px">Waypoint 8</label>
        <input type="text" name="waypoint_8" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Waypoint Lat,Lng" style="font-size: 11px">

        <label for="" style="font-size: 11px">Destination</label>
        <input type="text" name="destination" id="destination" autocomplete="off" class="form-control mb-2 destination" placeholder="Destination Lat,Lng" style="font-size: 11px" required>
    </div>

    
</form>

 <!-- Modal -->
 <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog ">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle"><ion-icon name="bulb" style="font-size: 25px;position:absolute;" class="text-warning"></ion-icon> <span style="margin-left: 25px;">How to use</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body" style="line-height: 30px;">
            <p>1. Click the map to pinpoint detinations.</p>
            <p>2. Click up to eight markers.</p>
        </div>
        <div class="modal-footer">

          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>
@endsection

@push('plugin-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&callback=initMap&libraries=places,geometry,drawing&v=weekly"></script>
@endpush

@push('custom-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
<script defer>

$(window).on('load', function() {
    $('#myModal').modal('show');

});


let map;

let mylat = '{{ $settings->lat }}';
let mylng = '{{ $settings->lng }}';

var directionsDisplay;

$('#save-btn').on('click',() => {
    $('#my-form').submit();
})

function initMap() {
    let markers = [];
    let waypts = [];
    const options = {
        fields: ["place_id", "geometry", "name"] 
    };       
    
    var directionsService = new google.maps.DirectionsService();
    directionsDisplay = new google.maps.DirectionsRenderer({
        //suppressMarkers: true
    });
    // The location of Uluru
    const uluru = { lat: -1.295180, lng: 36.842510 };
    // The map, centered at Uluru
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 8,
        center: uluru,
        mapTypeControl: false,
        mapTypeId: "roadmap",
    });

    map.addListener("click", function(e) {
        addMarkers(e.latLng);
    });

    directionsDisplay.setMap(map);
    directionsDisplay.setPanel(document.getElementById("content"));
    
    addMarkers({ lat: mylat - 0, lng: mylng - 0 });

    const destination = document.getElementById('search-place');

    const autocomplete = new google.maps.places.SearchBox(destination, options);

    map.addListener("bounds_changed", () => {
        autocomplete.setBounds(map.getBounds());
    });

    autocomplete.addListener("places_changed", () => {
        const places = autocomplete.getPlaces();

        if (places.length == 0) {
        return;
        }
            
        const bounds = new google.maps.LatLngBounds();

        places.forEach((place) => {
            if (!place.geometry || !place.geometry.location) {
                console.log("Returned place contains no geometry");
                return;
            }
            if (place.geometry.viewport) {
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }
        });

        map.fitBounds(bounds);
    });


    function addMarkers(latLng) {
        var ll = `${markers.length}` ;
        var marker = new google.maps.Marker({
            position: latLng,
            map: map,
            label: ll
        });

        map.panTo(latLng);

        if (markers.length > 9) {
            marker.setMap(null);
            Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: 'max number of points is ten',
                showConfirmButton: false,
                timer: 2500
            });
            return 
        }

        markers.push(marker);
    }
    

    document.getElementById('get-directions').addEventListener('click', getDirection);

    function getDirection() {
        //console.log(markers);
        var start = markers[0].getPosition();
        var end = markers[markers.length - 1].getPosition();

        for (let i = 1; i < markers.length -1; i++) {                    
            waypts.push({
                location: markers[i].getPosition(),
                stopover: true
            });
        }

        console.log(waypts)

        var request = {
            origin: start,
            destination: end,
            waypoints: waypts,
            optimizeWaypoints: true,
            travelMode: google.maps.DirectionsTravelMode.DRIVING
        };

        directionsService.route(request, function (response, status) {
            console.log(response);
            if (status == google.maps.DirectionsStatus.OK) {
                //display route
                directionsDisplay.setDirections(response);

                var route = response.routes[0];
                for (let i = 0; i < markers.length; i++) {
                    markers[i].setMap(null);
                }
                $('#orgin-input').val(start);
                $('#destination').val(end);
                var waypoints_inputs = document.querySelectorAll('.waypoint');
                for (let i = 0; i < waypts.length; i++) {     
                    waypoints_inputs[i].value = waypts[i].location;
                }

            }
        });

    }

    

    $('#clear-path').on('click', () => {
        for (let i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
        markers.splice(0, markers.length);
        waypts = [];
        addMarkers({ lat: mylat - 0, lng: mylng - 0 });
        directionsDisplay.set('directions', null);
    });
    
}

window.initMap = initMap;
</script>
@endpush

{{--
@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<style>
     body {
            margin: 0;
            padding: 0;
        }
        .outer-grid {
            display: grid;
            grid-template-columns: 1fr 30%;
        }

        .my-form {
            box-shadow: 0 1px 2px rgb(60 64 67 / 30%), 0 2px 6px 2px rgb(60 64 67 / 15%);
            opacity: 1;
            background-color: #fff;
            border: 0;
            position: relative;
            z-index: 1;
            top: 0;
            left: 0;
            bottom: 0;
            white-space: normal;
            /*padding: 20px;*/
        }
        #map {
        height: 100vh; /* The height is 400 pixels */
        width: 100%; /* The width is the width of the web page */
        }

        .flex-container {
            display: flex;
            flex-wrap: nowrap;
            margin-bottom: 10px;
            text-align: center;
            padding: 10px;
            gap: 10px;
        }


        .car-selected {
            color: #3490dcf8;
            
        }

        .search-area {
            box-shadow: 0 1px 2px rgb(60 64 67 / 30%), 0 1px 3px 1px rgb(60 64 67 / 15%);
            width: 100%;
           
        }

        .search-area-inputs {
            padding: 20px;
            margin-top: -20px;
        }

        #content {
            padding: 20px;
            height: 60vh;
            overflow: scroll;
        }
    .card-header {
        border-top: 1px solid rgba(0,0,0,.125);
        border-radius: 0.25rem;
        
        border-left: 1px solid rgba(0,0,0,.125);
        border-right: 1px solid rgba(0,0,0,.125);
        
    }

    .pcoded-inner-content {
        padding: 0;
    }

    .my-btn {
       
        border: 1px solid rgba(0,0,0,.125);
        border-radius: 0.25rem;
        width: 100%;
        padding: 5px;
    }
    .save {
        background: #0071f3;
        font-weight: 600;
        color: #fff;
    }

    .save:hover {
        background: #005fcb;
        cursor: pointer;
    }

    form {
        margin: 0;
        padding: 0;
    }

    .get-dir {
        background: #facc15;
        font-weight: 600;
    }

    .clear {
        background: #dc2626;
        font-weight: 600;
        color: #fff;
    }
</style>
@endsection
@section('content')

<div class="">
<!--The div element for the map -->
<div class="outer-grid">
    <div id="map"></div>
    <div class="my-form">

        <div class="search-area">
            <form action="{{ route('saveroutepath') }}" method="post">
                @csrf
                <input type="hidden" name="schooltrip_id" value="{{ $schooltrip->id }}">
                <div class="flex-container">
                   
                    
                        <button type="button" id="clear-path" class="clear my-btn">Clear</button>
                    
                        <button type="button" id="get-directions" class="get-dir my-btn text-dark">Get Direction</button>
                   
                        <button type="submit" class="save my-btn">Save</button>
                    
                    
                </div>
                
                    <div class="search-area-inputs">
                        
                        <div class="form-group">
                            <label for="">Search Places</label>
                            <input type="text" name="search-places" class="form-control" placeholder="Search Places" id="search-place">
                        </div>
                    </div>

                    <div style="display: none">

                        <input type="text" name="origin" autocomplete="off" class="form-control mb-2 origin" placeholder="Origin Lat,Lng" style="font-size: 11px" id="orgin-input" required>
                        <label for="" style="font-size: 11px">Waypoint 1</label>
                        <input type="text" name="waypoint_1" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Origin Lat,Lng" style="font-size: 11px">



                        <label for="" style="font-size: 11px">Waypoint 2</label>
                        <input type="text" name="waypoint_2" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Waypoint Lat,Lng" style="font-size: 11px">


                        <label for="" style="font-size: 11px">Waypoint 3</label>
                        <input type="text" name="waypoint_3" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Waypoint Lat,Lng" style="font-size: 11px">

                        <label for="" style="font-size: 11px">Waypoint 4</label>
                        <input type="text" name="waypoint_4" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Waypoint Lat,Lng" style="font-size: 11px">

                        <label for="" style="font-size: 11px">Waypoint 5</label>
                        <input type="text" name="waypoint_5" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Waypoint Lat,Lng" style="font-size: 11px">

                        <label for="" style="font-size: 11px">Waypoint 6</label>
                        <input type="text" name="waypoint_6" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Waypoint Lat,Lng" style="font-size: 11px">

                        <label for="" style="font-size: 11px">Waypoint 7</label>
                        <input type="text" name="waypoint_7" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Waypoint Lat,Lng" style="font-size: 11px">

                        <label for="" style="font-size: 11px">Waypoint 8</label>
                        <input type="text" name="waypoint_8" autocomplete="off" class="form-control mb-2 waypoint" placeholder="Waypoint Lat,Lng" style="font-size: 11px">

                        <label for="" style="font-size: 11px">Destination</label>
                        <input type="text" name="destination" id="destination" autocomplete="off" class="form-control mb-2 destination" placeholder="Destination Lat,Lng" style="font-size: 11px" required>
                    </div>
                </form>
            


        </div>
        

        <div id="content">
            
        </div>

    </div>
    
</div>


    

</div>
@endsection


@section('js')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&callback=initMap&libraries=places,geometry,drawing&v=weekly"
        defer
        ></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script defer>
       
        $(document).ready( function () {
          
    
            
            if("{{ Session::has('success') }}") {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ Session::get("success") }}',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else if ("{{ Session::has('unsuccess') }}") {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ Session::get("unsuccess") }}',
                    showConfirmButton: false,
                    timer: 2500
                });
            }else if ("{{ Session::has('errors') }}") {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ Session::get("errors") }}',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        } );

        let map;

        let mylat = '{{ $settings->lat }}';
        let mylng = '{{ $settings->lng }}';

        var directionsDisplay;

        function initMap() {
            

            let markers = [];

            let waypts = [];

            const options = {
                fields: ["place_id", "geometry", "name"] 
            };       

            
            var directionsService = new google.maps.DirectionsService();
            directionsDisplay = new google.maps.DirectionsRenderer({
                //suppressMarkers: true
            });
            // The location of Uluru
            const uluru = { lat: -1.295180, lng: 36.842510 };
            // The map, centered at Uluru
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 8,
                center: uluru,
                mapTypeControl: false,
                mapTypeId: "roadmap",
            });

            map.addListener("click", function(e) {
                addMarkers(e.latLng);
            });

            directionsDisplay.setMap(map);
            directionsDisplay.setPanel(document.getElementById("content"));
            
            addMarkers({ lat: mylat - 0, lng: mylng - 0 });

            const destination = document.getElementById('search-place');

            const autocomplete = new google.maps.places.SearchBox(destination, options);

            autocomplete.addListener("places_changed", () => {
                const places = autocomplete.getPlaces();

                if (places.length == 0) {
                return;
                }
                    
                const bounds = new google.maps.LatLngBounds();

                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    if (place.geometry.viewport) {
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });

                map.fitBounds(bounds);
            });


            function addMarkers(latLng) {
                var ll = `${markers.length}` ;
                var marker = new google.maps.Marker({
                    position: latLng,
                    map: map,
                    label: ll
                });

                marker.addListener('click', () => {
                    var index = marker.label - 0;
                    marker.setMap(null);
                    markers.splice(index, 1);
                    console.log(markers);
                });

                marker.setMap(map);

                map.panTo(latLng);

                if (markers.length > 9) {
                    marker.setMap(null);
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'max number of points is ten',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    return 
                }

                markers.push(marker);

                console.log(markers);
            }
            

            document.getElementById('get-directions').addEventListener('click', getDirection);

            function getDirection() {
                //console.log(markers);
                var start = markers[0].getPosition();

                var end = markers[markers.length - 1].getPosition();


                for (let i = 1; i < markers.length -1; i++) {                    
                    waypts.push({
                        location: markers[i].getPosition(),
                        stopover: true
                    });
                }

                console.log(waypts)

                var request = {
                    origin: start,
                    destination: end,
                    waypoints: waypts,
                    optimizeWaypoints: true,
                    travelMode: google.maps.DirectionsTravelMode.DRIVING
                };

                directionsService.route(request, function (response, status) {
                    console.log(response);
                    if (status == google.maps.DirectionsStatus.OK) {

                        directionsDisplay.setDirections(response);

                        
                        var route = response.routes[0];
                        for (let i = 0; i < markers.length; i++) {
                            markers[i].setMap(null);
                        }

                        $('#orgin-input').val(start);
                        $('#destination').val(end);

                        var waypoints_inputs = document.querySelectorAll('.waypoint');

                        for (let i = 0; i < waypts.length; i++) {     
                            waypoints_inputs[i].value = waypts[i].location;
                        }

                    }
                });

            }

            

            $('#clear-path').on('click', () => {
                    for (let i = 0; i < markers.length; i++) {
                        markers[i].setMap(null);
                    }

                    markers.splice(0, markers.length);
                    waypts = [];

                    addMarkers({ lat: mylat - 0, lng: mylng - 0 });


                    directionsDisplay.set('directions', null);
            });
            
        }

        window.initMap = initMap;

    </script>
@endsection
--}}