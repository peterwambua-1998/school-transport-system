@extends('layouts.app')
@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
  <style>
    .my-nav {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .map-div {
        height: 60vh;
    }

    #map {
        width: 100%;
        height: 100%;
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
 
    .label-marker {
        position: absolute;
        top: 0;
        left: -40px;
        background: #FEDB00;
        padding: 3px;
        border-radius: 0.125rem;
    }



    #get-path {
        margin-top: 10px;
        margin-right: 1%;
        filter: drop-shadow(0 4px 3px rgb(0 0 0 / 0.07)) drop-shadow(0 2px 2px rgb(0 0 0 / 0.06));
    }

    #clear-path {
        margin-top: 10px;
        margin-right: 1%;
        filter: drop-shadow(0 4px 3px rgb(0 0 0 / 0.07)) drop-shadow(0 2px 2px rgb(0 0 0 / 0.06));
    }

    .issue {
        color: #ff3366;
    }
   
  </style>
@endpush
@section('content')


<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('routes.index')}}">Route</a></li>
      <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('routes.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
    </div>
</nav>
    
<form action="{{ route('routes.store') }}" method="post" id="pathForm">
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Route</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label  class="form-label" for="title">Name</label>
                            <input type="text" name="title" class="form-control" id="title" placeholder="Name" required>
                            <span class="issue" id="name-error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label  class="form-label" for="platenum">Description</label>
                            <input type="text" name="description" class="form-control" id="desc" placeholder="Description" required>
                            <span class="issue" id="desc-error"></span>
                        </div>
                    </div>
                </div>


                <div class="row">
                    
                    <div class="mb-3">
                        <div class="col-md-6">
                            <label  class="form-label" for="inputState">Zone</label>
                            @if (count($zones) == 0)
                                <p class="text-danger">Add active zone</p>
                            @else 
                            
                            <select id="zones" class="js-example-basic form-select" multiple="mutiple" name="zone[]" required>
                               <option>select...</option> 
                                @foreach ($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                            @endif
                            <span class="issue" id="zones-error"></span>

                        </div>
                    </div>
                </div>

                <div class="col-md-3" style="display: none">
    
                    @csrf

                    <input type="hidden" name="distance_meters" id="distance_meters">

                    <label for="" style="font-size: 11px">Origin</label>
                    <input type="text" name="origin" autocomplete="off" class="form-control mb-2 origin" placeholder="Origin Lat,Lng" style="font-size: 11px" id="orgin-input" required>
                    <p style="font-size: 13px; color:red" id="orgin-val">origin is required</p>
            
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
                    <p style="font-size: 13px; color:red" id="dest-val">destination is required</p>
                </div>


                <div class="row mb-3 mt-3">
                
                    <span class="issue" id="map-error"></span>
                    <label for="">Route path</label>
                    <div class="col-md-12 map-div">
                        
                        
                        <div id="map"></div>
                    </div>

                    <div class="map-controls">
                        <button type="button" class="btn btn-success" id="get-path">Get Path</button>
                        <button type="button" class="btn btn-danger" id="clear-path">clear</button>
                        <input
                            id="pac-input"
                            class=""
                            type="text"
                            placeholder="Search Box"
                        />
                    </div>
                    
                </div>

                <div class="text-center">
                    <button type="submit" id="submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Save Route</button>
                </div>
            </div>
        </div>
    </div>
</div>

    
</form>

@endsection

@push('plugin-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&callback=initMap&libraries=places,geometry,drawing&v=weekly"></script>
<script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script defer>

        

    function initMap() {

        var lats = '{{ $settings->lat }}' - 0;
        var lngs = '{{ $settings->lng }}' - 0;

        const getPath = document.getElementById('get-path');
        const clearPath = document.getElementById('clear-path');

        //getPath.style.marginLeft = "5px";
        //clearPath.style.margin = "8px 0 22px";

        var markers = [];
        var waypts = [];

        var directionDisplay;
        var directionsService = new google.maps.DirectionsService();

        directionDisplay = new google.maps.DirectionsRenderer({
            suppressMarkers: true
        });

        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: { lat: lats, lng: lngs },
        });

        var markerStart = new google.maps.Marker({
            position: { lat: lats, lng: lngs },
            map: map,
            label: {text: "School", color: "#1e293b", fontSize: "15px", className: "label-marker"}
        });
        //addMarkers({ lat: lats, lng: lngs });

        const infoWindow = new google.maps.InfoWindow({
            content: "",
            disableAutoPan: true,
        });

       
        directionDisplay.setMap(map);

        map.addListener("click", function(e) {
            addMarkers(e.latLng);
        });

        const input = document.getElementById("pac-input");
        const searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

        searchBox.addListener("places_changed", () => {
            const places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // For each place, get the icon, name and location.
            const bounds = new google.maps.LatLngBounds();

            places.forEach((place) => {
                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }
                
                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });

            map.fitBounds(bounds);
        });

        map.controls[google.maps.ControlPosition.TOP_CENTER].push(getPath);
        map.controls[google.maps.ControlPosition.TOP_CENTER].push(clearPath);

        function addMarkers(latLng) {
            var marker = new google.maps.Marker({
                position: latLng,
                map: map
            });

            /*
            marker.addListener('click', () => {
                marker.setMap(null);
                markers.splice(marker, 1);
                console.log(markers);
            })
            */

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

        $('#get-path').on('click', () => {
            var start = markerStart.getPosition();
            var end = markerStart.getPosition();

            for (let i = 0; i < markers.length; i++) {
                waypts.push({
                    location: markers[i].getPosition(),
                    stopover: true
                });
            }

            var request = {
                origin: start,
                destination: start,
                waypoints: waypts,
                optimizeWaypoints: true,
                travelMode: google.maps.DirectionsTravelMode.DRIVING
            };

            directionsService.route(request, function (response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionDisplay.setDirections(response);
                    let totalDistance = 0;
                    var route = response.routes[0];
                    var legs = response.routes[0].legs;
                    for(var i=0; i<legs.length; ++i) {
                        totalDistance += legs[i].distance.value;
                    }
                    $('#distance_meters').val(totalDistance);
                    console.log(totalDistance);
                }
            });

            $('#orgin-input').val(start);
            $('#destination').val(start);

            var waypoints_inputs = document.querySelectorAll('.waypoint');

            for (let i = 0; i < markers.length; i++) {     
                waypoints_inputs[i].value = markers[i].getPosition();
            }
        });


        

        //clear the path to alow re draw
        $('#clear-path').on('click', () => {
            for (let i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }

            markers = [];
            waypts = [];

            //addMarkers({ lat: lats, lng: lngs });

            directionDisplay.set('directions', null);

            var inputs = document.querySelectorAll('.waypoint');

            inputs.forEach(input => {
                input.value = '';
            });
        });

        let polygon_arr = [];


        $("#zones").on("select2:select select2:unselect", function (e) {

            //this returns all the selected item
            var items= $(this).val(); 


            console.log(polygon_arr);


            polygon_arr.forEach(element => {
                console.log(element);
                element.setMap(null);
            });

            polygon_arr = [];


            for (let z = 0; z < items.length; z++) {
                $.ajax({
                type: "get",
                url: `/get-zones-for-routes/${items[z]}`,
                processData: false,
                cache: false,
                contentType: false,
                error: function(err) {
                    console.log(err);
                },
                success: function(response) {
                    //console.log(response);

                    x = 1;
                    var final_arr = [];
                    for (let i = 0; i < response[1].length; i+=2) {
                        const triangleCoords =  { 
                            lat: response[1][i].corrdinates - 0, 
                            lng: response[1][x].corrdinates - 0 
                        };
                        final_arr.push(triangleCoords);
                        x+=2;
                    }

                    //console.log(final_arr);
                    const bermudaTriangle = new google.maps.Polygon({
                        clickable: false,
                        paths: final_arr,
                        strokeColor: "#FF0000",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: "#ADFF2F",
                        fillOpacity: 0.2,
                    });

                    polygon_arr.push(bermudaTriangle);
                    

                    google.maps.Polygon.prototype.getBounds = function() {
                        var bounds = new google.maps.LatLngBounds();
                        var paths = this.getPaths();
                        var path;        
                        for (var i = 0; i < paths.getLength(); i++) {
                            path = paths.getAt(i);
                            for (var ii = 0; ii < path.getLength(); ii++) {
                                bounds.extend(path.getAt(ii));
                            }
                        }
                        return bounds;
                    }

                    bermudaTriangle.setMap(map);

                    console.log(polygon_arr);

                    
                    map.fitBounds(bermudaTriangle.getBounds());
                }
            })
                
            }

            

            console.log(items);

            //Gets the last selected item
            var lastSelectedItem = e.params.data.id;
        })
    }

    $('#submit-btn').on('click',(e) => {
        if (!$('#title').val()) {
            $('#name-error').text('field required');
            e.preventDefault();
            $('html, body').animate({
            scrollTop: "0px"
        }, 800);
            return;
        } else {
            $('#name-error').text('');
        }

        if (!$('#desc').val()) {
            $('#desc-error').text('field required');
            $('html, body').animate({
            scrollTop: "0px"
        }, 800);
            e.preventDefault();
            return;
        } else {
            $('#desc-error').text('');
        }

        if ($('#zones').val().length <= 0) {
            $('#zones-error').text('field required');
            $('html, body').animate({
            scrollTop: "0px"
        }, 800);
            e.preventDefault();
            return;
        } else {
            $('#zones-error').text('');
        }

        if (!$('#distance_meters').val()) {
            $('#map-error').text('Create route path');
                $('html, body').animate({
                scrollTop: "0px"
            }, 800);
            e.preventDefault();
            return;
        } else {
            $('#map-error').text('');
        }

        $('#pathForm').submit();
        
        
    });


    window.initMap = initMap;
</script>
@endpush
