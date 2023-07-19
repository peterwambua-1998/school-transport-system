@extends('layouts.app')

@push('plugin-styles')
<style>
    .submit {
     background: #0071f3;
     color: #fff;
    }
 
    .submit:hover {
     background: #014fa8;
    }
 
    .table-responsive {
     overflow: hidden;
    }
    .map-wrapper {
        height: 60vh;
        margin-bottom: 30px;
    }
 
    #map {
        height: 100%;
    }

    
    .issue {
        color: #ff3366;
    }
 
    #pac-input {
        background-color: #fff;
        font-family: "Roboto", Helvetica, sans-serif;
        font-size: 15px;
        font-weight: 400;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 400px;
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
        margin-left: 17px;
        
        outline: none;
        padding: 0 11px 0 13px;
        z-index: 10;
        width: 400px;
        
    }
 
    .controls:focus {
        border-color: #4d90fe;
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
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">  
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">System Settings</h6>
                <hr>

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

                <br>

                <div class="mt-0">
                    <ul  class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
                    <li class="nav-item " style="width: 25%">
                        <a class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" role="tab" aria-controls="home" aria-selected="true">Company Settings</a>
                    </li>
                    <li class="nav-item" style="width: 25%">
                        <a class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" role="tab" aria-controls="profile" aria-selected="false">WhatsApp Settings</a>
                    </li>
                    
                    <li class="nav-item" style="width: 25%">
                        <a class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#emails" role="tab" aria-controls="email" aria-selected="false">Email Settings</a>
                    </li>
                    <li class="nav-item" style="width: 25%">
                        <a class="nav-link" id="center-tab" data-bs-toggle="tab" data-bs-target="#center" role="tab" aria-controls="center" aria-selected="false">Center Map Settings</a>
                    </li>
                    
                    </ul>
                    <div class="tab-content mt-3" id="lineTabContent">
                    <div class="tab-pane fade show active " id="home" role="tabpanel" aria-labelledby="home-tab">
                        @include('settings.systemsettings')
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        @include('settings.whatsapp')
                    </div>
                    
                    <div class="tab-pane fade" id="emails" role="tabpanel" aria-labelledby="email-tab">
                        @include('settings.mail')
                    </div>
                    <div class="tab-pane fade" id="center" role="tabpanel" aria-labelledby="center-tab">
                        @include('settings.centermap')
                    </div>
                    
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


@endsection


@push('custom-scripts')
<script
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&libraries=places&v=weekly"

></script>
<script defer src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer>
    $(document).ready( function () {

        if (! navigator.geolocation) {
            console.log()
        } else {
            navigator.geolocation.getCurrentPosition(succ, error);
        }


        function succ(position) {
            let latitude = position.coords.latitude;
            let longitude = position.coords.longitude;
            let timestamp = Date.now();

            getTimeZone(latitude, longitude, timestamp);
        }

        function error(e) {
            console.log(e);
        }

        function getTimeZone(lat, lng, timestamp) {
        
            $.ajax({
                method: "get",
                url: `https://maps.googleapis.com/maps/api/timezone/json?location=${lat}%2C${lng}&timestamp=1331161200&key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU`,
                cache: false,
                error: function(err) {
                    console.log(err);
                },

                success: function(res) {
                    console.log(res);
                    $('#time-zone').val(res.timeZoneId)
                }
                
            });    
        }
        
        
    } );

    function initMap() {
        var myLatlng;

        var sett = "{{ $settings }}";

        myLatlng = { lat: '{{ $settings->lat }}' - 0, lng: '{{ $settings->lng }}' - 0 };

        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 12,
            center: myLatlng,
        });
        // Create the initial InfoWindow.
        let infoWindow = new google.maps.InfoWindow();

        if (! "{{ $settings }}") {
            infoWindow.content = "Click the map to get Lat/Lng!",
            infoWindow.position = myLatlng,
            

            infoWindow.open(map);
        }



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
                    <div class="iw-title">School location marked</div>
                </div>
            `;
            infoWindow.setContent(contentInfo);

            var j = mapsMouseEvent.latLng.toJSON();

            $('.lat').val(j.lat);
            $('.lng').val(j.lng);

            infoWindow.open(map);
        });


        const input = document.getElementById("pac-input");
        const searchBox = new google.maps.places.SearchBox(input);


        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        // Bias the SearchBox results towards current map's viewport.
        map.addListener("bounds_changed", () => {
            searchBox.setBounds(map.getBounds());
        });


        let markers = [];

        if ("{{ $settings }}") {
            const image = "https://cdn-icons-png.flaticon.com/512/1183/1183390.png";


            const ceneter = new google.maps.Marker({
                position: { lat: '{{ $settings->lat }}' - 0, lng: '{{ $settings->lng }}' - 0 },
                label: {text: "School location", color: "#1e293b", fontSize: "15px", className: "label-marker"},
                icon: {
                    url: image,
                    scaledSize: new google.maps.Size(50, 50), // scaled size
                    
                },
                map: map
            });

            map.panTo({ lat: '{{ $settings->lat }}' - 0, lng: '{{ $settings->lng }}' - 0 });
            
        }

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


    

</script>
@endpush