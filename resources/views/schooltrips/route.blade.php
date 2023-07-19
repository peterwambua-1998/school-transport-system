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
<div class="p-2" style="border-bottom: 2px solid #f3f4f6; display: flex; flex-direction:row-reverse; gap: 5px;background:#f3f4f6">
    
    
    <button class="btn btn-success" id="save-btn" style="width: 15%">Save Path</button>

    
    <div class="input-group" style="width: 85%;">
        <div>
            <form action="{{ route('saveroutepath') }}" method="post" id="my-form" style="padding:0;margin:0">
                @csrf
        </div>
        <div class="input-group-text">
          <i data-feather="search"></i>
        </div>
        <input type="hidden" name="destss" id="destss">
        <input type="text" name="destination" class="form-control" id="destination" placeholder="Search here...">
    </div>
</div>

<!-- map div -->
<div id="map"></div>

<!-- form to store coordinates -->

    <input type="hidden" name="schooltrip_id" value="{{ $schooltrip->id }}">
    
    
        <div class="search-area-inputs" style="display: none">
            <div class="form-group">
                <label for="">Source</label>
                <input type="text" name="origin" class="form-control" placeholder="Source" id="source">
            </div>
            
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
            <p>1. Search for your destination and the route will appear.</p>
            <p>2. Keep searching to get the needed route.</p>
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

    
    $('#save-btn').on('click',() => {
        $('#my-form').submit();
    })

    function initMap() {
        var mylat;
        var mylng;
        
        mylat  = '{{ $settings->lat }}';
        mylng = '{{ $settings->lng }}';

        $('#source').val(mylat+', '+mylng);
        
        let markers = [];

        var directionsDisplay;
        var directionsService = new google.maps.DirectionsService();
        directionsDisplay = new google.maps.DirectionsRenderer();
        // The location of Uluru
        const uluru = { lat: -1.295180, lng: 36.842510 };
        // The map, centered at Uluru
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 8,
            center: uluru,
            mapTypeControl: false,
            mapTypeId: "roadmap",
        });
        
        var trafficLayer = new google.maps.TrafficLayer();
        trafficLayer.setMap(map);

        
        directionsDisplay.setMap(map);
        directionsDisplay.setPanel(document.getElementById("content"));

        const sourceInput = document.getElementById('source');

        const options = {
            fields: ["place_id", "geometry", "name"] 
        };

        const autocomplete = new google.maps.places.SearchBox(sourceInput, options);

        map.addListener("bounds_changed", () => {
            autocomplete.setBounds(map.getBounds());
        });
                    
        const bounds = new google.maps.LatLngBounds();

        var places;

        

        autocomplete.addListener("places_changed", function () {
            places = autocomplete.getPlaces();

            if (places.length == 0) {
                return;
            }

            // Clear out the old markers.
            markers.forEach((marker) => {
                marker.setMap(null);
                });
            markers = [];

            if (!places[0].geometry || !places[0].geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
            }

            const icon = {
                    url: places[0].icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25),
            };

            markers.push(
                new google.maps.Marker({
                    position: places[0].geometry.location,
                    map: map,
                })
            )

            if (places[0].geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(places[0].geometry.viewport);
            } else {
                    bounds.extend(places[0].geometry.location);
            }
        
            map.fitBounds(bounds);
            console.log(places);
            console.log(markers);
        });
        const destination = document.getElementById('destination');
        const autocompletetwo = new google.maps.places.SearchBox(destination, options);
        map.addListener("bounds_changed", () => {
            autocompletetwo.setBounds(map.getBounds());
        });
        var placestwo;
        autocompletetwo.addListener("places_changed", function () {
            placestwo = autocompletetwo.getPlaces();

            if (placestwo.length == 0) {
                return;
            }

            if (!placestwo[0].geometry || !placestwo[0].geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
            }

            // Clear out the old markers.
            markers.forEach((marker) => {
                marker.setMap(null);
                });
            markers = [];

            const icon = {
                    url: placestwo[0].icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25),
            };

            markers.push(
                new google.maps.Marker({
                    position: placestwo[0].geometry.location,
                    map: map,
                })
            )

            if (placestwo[0].geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(placestwo[0].geometry.viewport);
            } else {
                    bounds.extend(placestwo[0].geometry.location);
            }
        
            map.fitBounds(bounds);
            calcRoute();

            function calcRoute() {
                var dest = markers[0].getPosition();

                $('#destss').val(dest);

                console.log(dest);
                
                var request = {
                    origin: { lat: mylat -0, lng: mylng -0 },
                    destination: document.getElementById('destination').value,
                    travelMode: google.maps.DirectionsTravelMode.DRIVING,
                    //provideRouteAlternatives: true,
                };

                directionsService.route(request, function (response, status) {
                    console.log(response);
                    if (status == google.maps.DirectionsStatus.OK) {
                        directionsDisplay.setDirections(response);
                    }
                });
                console.log(markers);
                
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
                
                
            }
        });
        
    }

    window.initMap = initMap;
</script>
@endpush


{{--@extends('layouts.app')
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
        }

        .flex-container div {
            width: 100px;
            text-align: center;
            margin: 10px;
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
    .save {
        background: #0071f3;
        color: #fff;
        border: 1px solid rgba(0,0,0,.125);
        border-radius: 0.25rem;
    }

    .save:hover {
        background: #005fcb;
        cursor: pointer;
    }

    form {
        margin: 0;
        padding: 0;
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


        function initMap() {
            var mylat;
            var mylng;
           
                mylat  = '{{ $settings->lat }}';
                mylng = '{{ $settings->lng }}';

                $('#source').val(mylat+','+mylng);
            
            console.log(mylat, mylng);
            
            let markers = [];

            

            var directionsDisplay;
            var directionsService = new google.maps.DirectionsService();
            directionsDisplay = new google.maps.DirectionsRenderer();
            // The location of Uluru
            const uluru = { lat: -1.295180, lng: 36.842510 };
            // The map, centered at Uluru
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 8,
                center: uluru,
                mapTypeControl: false,
                mapTypeId: "roadmap",
            });
            // The marker, positioned at Uluru
            
            var trafficLayer = new google.maps.TrafficLayer();
            trafficLayer.setMap(map);

           
            

           

            map.addListener('click', function (e) {
                console.log(e);
                new google.maps.Marker({
                    position: e.latLng,
                    map: map,
                }).addListener('dblclick', function (ev) {
                    console.log(ev);
                    //this.setMap(null);
                });
            });

            directionsDisplay.setMap(map);
            directionsDisplay.setPanel(document.getElementById("content"));

            const sourceInput = document.getElementById('source');

            const options = {
                fields: ["place_id", "geometry", "name"] 
            };

            const autocomplete = new google.maps.places.SearchBox(sourceInput, options);
                        
            const bounds = new google.maps.LatLngBounds();

            var places;

            autocomplete.addListener("places_changed", function () {
                places = autocomplete.getPlaces();

                if (places.length == 0) {
                    return;
                }

                // Clear out the old markers.
                markers.forEach((marker) => {
                    marker.setMap(null);
                    });
                markers = [];

                if (!places[0].geometry || !places[0].geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                }

                const icon = {
                        url: places[0].icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25),
                };

                markers.push(
                    new google.maps.Marker({
                        position: places[0].geometry.location,
                        map: map,
                    })
                )

                if (places[0].geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(places[0].geometry.viewport);
                } else {
                        bounds.extend(places[0].geometry.location);
                }
            
                map.fitBounds(bounds);

                
                

                console.log(places);
                console.log(markers);
            });


            const destination = document.getElementById('destination');

            const autocompletetwo = new google.maps.places.SearchBox(destination, options);

            var placestwo;

            autocompletetwo.addListener("places_changed", function () {
                
                placestwo = autocompletetwo.getPlaces();



                if (placestwo.length == 0) {
                    return;
                }

                
                

                if (!placestwo[0].geometry || !placestwo[0].geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                }

                const icon = {
                        url: placestwo[0].icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25),
                };

                markers.push(
                    new google.maps.Marker({
                        position: placestwo[0].geometry.location,
                        map: map,
                    })
                )

                if (placestwo[0].geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(placestwo[0].geometry.viewport);
                } else {
                        bounds.extend(placestwo[0].geometry.location);
                }
            
                map.fitBounds(bounds);

        
                

                calcRoute();


                function calcRoute() {

                    markers.forEach((marker) => {
                    marker.setMap(null);
                    });
                markers = [];
                
                var request = {
                    origin: { lat: mylat -0, lng: mylng -0 },
                    destination: document.getElementById('destination').value,
                    travelMode: google.maps.DirectionsTravelMode.DRIVING,
                    //provideRouteAlternatives: true,
                };

                directionsService.route(request, function (response, status) {
                    console.log(response);
                    if (status == google.maps.DirectionsStatus.OK) {
                        directionsDisplay.setDirections(response);
                        
                    }
                });
                 }
            });


            

            
        }

        window.initMap = initMap;

    </script>
@endsection
--}}