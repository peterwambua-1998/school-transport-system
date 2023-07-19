@extends('layouts.app')
@push('plugin-styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    .page-content {
        padding: 0px !important;
    }

    .my-grid {
        display: grid;
        grid-template-columns: 1fr 30%;
        height: 100%;
    }

    #map {
        width: 100%;
        height: 100%;
    }
 
    
</style>
@endpush

@section('content')
    <div class="my-grid p-2">
        <div>
            <div id="map"></div>
        </div>
        <div>
            <div class="search-area">
                <div style="padding: 10px">
                    <h5>{{ $schoolTrip->trip_name }} Trip</h5>
                </div>
                <div class="flex-container">
                       
    
                        @if (Auth::user()->user_type == 'driver')
                        @if (!$schoolTrip->route_changed && !$schoolTrip->approved)
                        <div>
                            <button onclick="initMaptwo()" class="">Best Route</button>
                        </div>
                        <div>
                            <button onclick="sendApproval()" class="" style="width: 120%">Send Approval</button>
                        </div>
                        @endif
                        @endif
                        
                        <input type="hidden" name="source" class="form-control" placeholder="Source" id="source" value="{{ $schoolTrip->trip_route }}">
                        <input type="hidden" name="destination" class="form-control" placeholder="Destination" id="destination" value="{{ $schoolTrip->destination }}">
                </div>
                  
                <div style="padding: 10px">
                    <ul class="list-group">
                        @php
                        $dest = DB::table('school_trips_destinations')->where('school_trip_id','=',$schoolTrip->id)->first();
                        @endphp
                        <li class="list-group-item">Detination : <span style="font-weight: 600">{{$dest->destination}}</span></li>
                        <li class="list-group-item">{{ucfirst($tr->plural) ?? 'Grades'}} : 
                            @foreach ($schoolTrip->school_trip_grades as $grade)
                                <?php $gr =  DB::table('student_classes')->where('id','=', $grade->grade_id)->first(); ?>
                                <span style="font-weight: 600">{{$gr->name}}</span>
                                ,
                            @endforeach
                        </li>
                        @if ($schoolTrip->status == 'paid')
                        <li class="list-group-item">Price : <span style="font-weight: 600">{{ $settings->currency ?? 'KSH' }}  {{ $schoolTrip->price }}</span></li>
                        @endif
                        @if ($schoolTrip->route_changed && !$schoolTrip->approved)
                        <li class="list-group-item">Approval Status : <span style="font-weight: 600">Not Yet Approved</span></li> 
                        
                        @endif
    
                        @if (Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'admin' )
                            @if ($schoolTrip->route_changed && !$schoolTrip->approved)
                                <li class="list-group-item"><button onclick="approverouteHead()">Approve Route</button></li>
                            @endif
                        @endif
    
                        @if($schoolTrip->route_changed && $schoolTrip->approved)
                        <li  class="list-group-item">Approval Status : <span style="font-weight: 600">Approved</span></li> 
                        @endif
    
                        @if (Auth::user()->user_type == 'driver')
                            <li class="list-group-item"><button onclick="reachedDest()">Reached Destination</button> <button onclick="goingBack()">Going Back</button></li>
                            <li class="list-group-item"><button onclick="reachedSchool()">Reached School safely</button></li>  
                        @endif
    
                        
                        
                    </ul>
                    
                    <div class="mt-3">
                        <a href="{{route('editpage_no_wayponts', $schoolTrip->id)}}" class="btn bg-success text-white"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px" name="create-outline"></ion-icon> Change Route</a>
                        <a href="{{route('schooltrips.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="arrow-back-circle-outline"></ion-icon> Back</a>
                    </div>
                </div>
                
            </div>
                
                
    
            </div>
    </div>
    
@endsection

@push('custom-scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.js"></script>

    @if ($schoolTrip->route_changed && !$schoolTrip->approved)
    <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&callback=initMaptwo&libraries=places,geometry,drawing&v=weekly"
    defer
    ></script>
    @elseif ($schoolTrip->route_changed && $schoolTrip->approved)
    <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&callback=initMaptwo&libraries=places,geometry,drawing&v=weekly"
    defer
    ></script>

    @else
    <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&callback=initMap&libraries=places,geometry,drawing&v=weekly"
    defer
    ></script>
    @endif
    

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

        if ("{{ $schoolTrip->route_changed }}" == 0) {

            function initMap() {
            


                

                var directionDisplay;
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


                directionsDisplay.setMap(map);
                directionsDisplay.setPanel(document.getElementById("content"));

            
                

                
                const bounds = new google.maps.LatLngBounds();

                

                var placestwo;

                calcRoute();


                function calcRoute() {

                    
                    var request = {
                        origin: document.getElementById('source').value,
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


                    
                
            }
        } else {
            function initMaptwo() {
        
                var directionDisplay;
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


                directionsDisplay.setMap(map);
                directionsDisplay.setPanel(document.getElementById("content"));

            
                

                
                const bounds = new google.maps.LatLngBounds();

                

                var placestwo;

                calcRoute();


                function calcRoute() {

                    
                    var request = {
                        origin: document.getElementById('source').value,
                        destination: document.getElementById('destination').value,
                        travelMode: google.maps.DirectionsTravelMode.DRIVING,
                        drivingOptions: {
                            departureTime: new Date(Date.now()),  // for the time N milliseconds from now.
                            trafficModel: 'optimistic'
                        }
                        //provideRouteAlternatives: true,
                    };

                    directionsService.route(request, function (response, status) {
                        console.log(response);
                        if (status == google.maps.DirectionsStatus.OK) {
                            directionsDisplay.setDirections(response);
                            for (let i = 0; i < markers.length; i++) {
                                markers[i].setMap(null);
                            }
                            console.log('pre');
                        }
                    });
                    }


                    
                
            }
        }

        function initMaptwo() {
        
            var directionDisplay;
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


            directionsDisplay.setMap(map);
            directionsDisplay.setPanel(document.getElementById("content"));

        
            

            
            const bounds = new google.maps.LatLngBounds();

            

            var placestwo;

            calcRoute();


            function calcRoute() {

                
                var request = {
                    origin: document.getElementById('source').value,
                    destination: document.getElementById('destination').value,
                    travelMode: google.maps.DirectionsTravelMode.DRIVING,
                    drivingOptions: {
                        departureTime: new Date(Date.now()),  // for the time N milliseconds from now.
                        trafficModel: 'optimistic'
                    }
                    //provideRouteAlternatives: true,
                };

                directionsService.route(request, function (response, status) {
                    console.log(response);
                    if (status == google.maps.DirectionsStatus.OK) {
                        directionsDisplay.setDirections(response);
                        for (let i = 0; i < markers.length; i++) {
                            markers[i].setMap(null);
                        }
                    }
                });
                }


                
        
        }

        

        if ("{{ $schoolTrip->route_changed && !$schoolTrip->approved }}") {
            
            window.initMaptwo = initMaptwo;

        } else if ("{{ $schoolTrip->route_changed && $schoolTrip->approved }}") {
            console.log('peter');
            window.initMaptwo = initMaptwo;
            

        } else {
            window.initMap = initMap;
        }

        


        function sendApproval() {
            var dataTwo = new FormData;
            dataTwo.append('_token', '{{ csrf_token() }}');
            dataTwo.append('schooltrip_id', '{{ $schoolTrip->id }}');


            $.ajax({
                type: "POST",
                url: "{{ route('save_approval') }}",
                processData: false,
                contentType: false,
                cache: false,
                data: dataTwo,
                error: function (err) {
                    console.log(err)
                },
                success: function (response) {
                    console.log(response);
                    
                   
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(() => {
                        reloadPage();
                    }, 2000);
                    
                    
                }
            });
        }


        function approverouteHead() {
            var dataTwo = new FormData;
            dataTwo.append('_token', '{{ csrf_token() }}');
            dataTwo.append('schooltrip_id', '{{ $schoolTrip->id }}');


            $.ajax({
                type: "POST",
                url: "{{ route('schooltrip_approve') }}",
                processData: false,
                contentType: false,
                cache: false,
                data: dataTwo,
                error: function (err) {
                    console.log(err)
                },
                success: function (response) {
                    console.log(response);
                    
                   
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(() => {
                        reloadPage();
                    }, 2000);
                    
                    
                }
            });
        }

        function reachedDest() {
            $.ajax({
                type: "GET",
                url: "{{ route('sendReachedDestination', $schoolTrip->id) }}",
                processData: false,
                contentType: false,
                cache: false,
                
                error: function (err) {
                    console.log(err)
                },
                success: function (response) {
                    
                    
                   
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    
                    
                }
            });
        }

        function goingBack() {
            $.ajax({
                type: "GET",
                url: "{{ route('sendGoindBack', $schoolTrip->id) }}",
                processData: false,
                contentType: false,
                cache: false,
                
                error: function (err) {
                    console.log(err)
                },
                success: function (response) {
                    
                    
                   
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    
                    
                }
            });
        }

        function reachedSchool() {
            $.ajax({
                type: "GET",
                url: "{{ route('sendReachedSchool', $schoolTrip->id) }}",
                processData: false,
                contentType: false,
                cache: false,
                error: function (err) {
                    console.log(err)
                },
                success: function (response) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response,
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        }

        function reloadPage() {
            location.reload();
        }

    </script>
@endpush

{{--
@extends('layouts.app')
@push('plugin-styles')
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
            
        }

        #content {
            
            height: 40vh;
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

    li {
        margin-bottom: 10px;
    }

@media only screen and (max-width: 500px)  {
    .outer-grid {
        display: flex !important;
        flex-direction: column;
    }
}
</style>
@endpush
@section('content')

<div class="row">
    <div class="col-md-9">
        <div id="map"></div>
    </div>
    <div class="col-md-3">
        <div class="search-area">
            <div style="padding: 10px">
                <h5>{{ $schoolTrip->trip_name }}</h5>
            </div>
            <div class="flex-container">
                    @if (Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'admin' )
                        
                    
                    <div class="">
                        <i class="fa-solid fa-bus car-selected"></i>
                        
                    </div>
                    <div>
                        <i class="fa-solid fa-train"></i>
                    </div>
                    <div>
                        <i class="fa-solid fa-person-walking"></i>
                    </div>
                    @endif

                    @if (Auth::user()->user_type == 'driver')
                    @if (!$schoolTrip->route_changed && !$schoolTrip->approved)
                    <div>
                        <button onclick="initMaptwo()" class="">Best Route</button>
                    </div>
                    <div>
                        <button onclick="sendApproval()" class="" style="width: 120%">Send Approval</button>
                    </div>
                    @endif
                    @endif
                    

                    
                    
                    <input type="hidden" name="source" class="form-control" placeholder="Source" id="source" value="{{ $schoolTrip->trip_route }}">
                    <input type="hidden" name="destination" class="form-control" placeholder="Destination" id="destination" value="{{ $schoolTrip->destination }}">
            </div>
              
            <div style="padding: 10px">
                <ul>
                    <li>Detination : <span style="font-weight: 600">{{ $schoolTrip->destination_name }}</span></li>
                    <li>Grade : <span style="font-weight: 600">{{ $schoolTrip->grade }}</span></li>
                    <li>Price : <span style="font-weight: 600">{{ $settings->currency ?? 'KSH' }}  {{ $schoolTrip->price }}</span></li>
                    @if ($schoolTrip->route_changed && !$schoolTrip->approved)
                    <li>Approval Status : <span style="font-weight: 600">Not Yet Approved</span></li> 
                    
                    @endif

                    @if (Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'admin' )
                        @if ($schoolTrip->route_changed && !$schoolTrip->approved)
                            <li><button onclick="approverouteHead()">Approve Route</button></li>
                        @endif
                    @endif

                    @if($schoolTrip->route_changed && $schoolTrip->approved)
                    <li>Approval Status : <span style="font-weight: 600">Approved</span></li> 
                    @endif

                    @if (Auth::user()->user_type == 'driver')
                        <li><button onclick="reachedDest()">Reached Destination</button> <button onclick="goingBack()">Going Back</button></li>
                        <li><button onclick="reachedSchool()">Reached School safely</button></li>  
                    @endif

                    
                    
                </ul>
                
                <div>
                    <a href="{{route('editpage_no_wayponts', $schoolTrip->id)}}" class="btn bg-success">Change Route</a>
                </div>
            </div>
            
        </div>
            
            <div id="content">
                
            </div>

        </div>
    </div>
</div>

@endsection



---}}