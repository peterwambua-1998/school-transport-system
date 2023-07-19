@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css">
<style>
    
    
    .top-navigation {
        padding: 10px;
        border-radius: .25rem;
        border: 1px solid rgba(0,0,0,.125);
        margin-bottom: 15px;
        display: flex;
        
    }

    .top-navigation p {
        flex-grow: 8;
        position: relative;
        top: 5px;
        letter-spacing: 1px;
        font-size: 16px;
    }
    
    .map-wrapper {
        height: 80vh;
        
   }

   #map {
    height: 100%;
   }

   #pac-input {
    background-color: #fff;
    font-family: Roboto;
    font-size: 15px;
    font-weight: 300;
    margin-left: 12px;
    padding: 0 11px 0 13px;
    text-overflow: ellipsis;
    width: 400px;
    margin-top: 12px;
    height: 30px;
    border: 1px solid rgba(0,0, 0, .125);
    border-radius: .15rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    #pac-input:focus {
    border-color: #4d90fe;
    }
</style>
@endsection
@section('content')

<div class="top-navigation" style="background: #e2e8f0">
    <p><span >Select {{ $student->first_name }}</span> - <span style="font-weight:500;">Drop off</span></p>
    <form action="{{ route('changeDropOffSave') }}" method="post">
        @csrf
        <input type="hidden" name="lat" id="lat" required>
        <input type="hidden" name="lng" id="lng" required>
        <input type="hidden" name="student_id" value="{{ $student->id }}">

        <button type="submit" class="btn btn-primary" id="submit" style="float: right;border-radius:5px" >Save Drop off</button>
    </form>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="card">
            <div class="card-body">
                <p>Click on map to select drop off point</p>
                
                <div class="map-wrapper">
                    <input
                    id="pac-input"
                    class="controls"
                    type="text"
                    placeholder="Search Box"
                    />
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection


@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.js"></script>
<script defer src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js" ></script>
<script
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&callback=initMap&libraries=places&v=weekly"
defer
></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script defer>
        $( function() {
            $( document ).tooltip();
        } );
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
                    title: '{{ Session::get("erros") }}',
                    showConfirmButton: false,
                    timer: 2500
                });
        }
        } );

        function initMap() {

            let markers = [];
            let map;

            var lat = "{{ $settings->lat }}" - 0;
            var lng = "{{ $settings->lng }}" - 0;

          
            const myLatLng = { lat: lat, lng: lng };

           

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 8,
                center: myLatLng,
            });


            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);

            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
                // Bias the SearchBox results towards current map's viewport.
            map.addListener("bounds_changed", () => {
                    searchBox.setBounds(map.getBounds());
            });

            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

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
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });

               
                    
                    map.fitBounds(bounds);
            });

            

            map.addListener('click', function(e) {
                deleteMarkers();

                var latlngJson = JSON.parse(JSON.stringify(e.latLng.toJSON(), null, 2));

                $('#lat').val(latlngJson.lat);
                $('#lng').val(latlngJson.lng);


                addMarker(e.latLng);
            });


            function addMarker(position) {
                const marker = new google.maps.Marker({
                    position,
                    map,
                    title: "{{ $student->first_name }} Pickup/Drop off point",
                });

                markers.push(marker);
            }

            function setMapOnAll(map) {
                for (let i = 0; i < markers.length; i++) {
                    markers[i].setMap(map);
                }
            }

            function hideMarkers() {
                setMapOnAll(null);
            }

            function deleteMarkers() {
                hideMarkers();
                markers = [];
            }
            

            
        }

        window.initMap = initMap;

        
    </script>
@endsection