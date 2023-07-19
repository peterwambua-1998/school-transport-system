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
                <h5>{{ $route->trip_name }}</h5>
            </div>
            <div class="flex-container">
                   
                    @if (Auth::user()->user_type == 'driver')
                    @if (!$route->route_changed && !$route->approved)
                    <div>
                        <button onclick="initMaptwo()" class="">Best Route</button>
                    </div>
                    <div>
                        <button onclick="sendApproval()" class="" style="width: 120%">Send Approval</button>
                    </div>
                    @endif
                    @endif
                    
                    <input type="hidden" name="source" class="form-control" placeholder="Source" id="source" value="{{ $route->trip_route }}">
                    <input type="hidden" name="destination" class="form-control" placeholder="Destination" id="destination" value="{{ $route->destination }}">
            </div>
                
            <div style="padding: 10px">
                <ul class="list-group">
                    @php
                        $dest_num = 1;
                        $dests = DB::table('school_trips_destinations')->where('school_trip_id','=',$route->id)->get();

                    @endphp
                    @foreach ($dests as $dest)
                    <li class="list-group-item">Detination {{$dest_num}}: <span style="font-weight: 600">{{$dest->destination}}</span></li>
                    <?php $dest_num++; ?>
                    @endforeach
                    
                    <li class="list-group-item">{{ucfirst($tr->plural) ?? 'Grades'}} : 
                        @foreach ($route->school_trip_grades as $grade)
                        <?php $gr =  DB::table('student_classes')->where('id','=', $grade->grade_id)->first(); ?>
                        <span style="font-weight: 600">{{$gr->name}}</span>
                        ,
                    @endforeach
                    </li>
                    
                    @if ($route->status == 'paid')
                    <li class="list-group-item">Price : <span style="font-weight: 600">{{ $settings->currency ?? 'KSH' }}  {{ $route->price }}</span></li>
                    @endif
                    @if ($route->route_changed && !$route->approved)
                    <li class="list-group-item">Approval Status : <span style="font-weight: 600">Not Yet Approved</span></li> 
                    
                    @endif
    
                    @if (Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'admin' )
                        @if ($route->route_changed && !$route->approved)
                            <li class="list-group-item"><button  onclick="approverouteHead()">Approve Route</button></li>
                        @endif
                    @endif
    
                    @if($route->route_changed && $route->approved)
                    <li class="list-group-item">Approval Status : <span style="font-weight: 600">Approved</span></li> 
                    @endif
    
                    @if (Auth::user()->user_type == 'driver')
                        <li class="list-group-item"><button onclick="reachedDest()">Reached Destination</button> <button onclick="goingBack()">Going Back</button></li>
                        <li class="list-group-item"><button onclick="reachedSchool()">Reached School safely</button></li>  
                    @endif
    
                    
                    
                </ul>
                
                <div class="mt-3">
                    <a href="{{route('editpage_wayponts', $route->id)}}" class="btn bg-success text-white"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px" name="create-outline"></ion-icon> Change Route</a>
                    <a href="{{route('schooltrips.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="arrow-back-circle-outline"></ion-icon> Back</a>
                </div>
            </div>
            
        </div>
    </div>
</div>
<div style="display: none">
    <input type="hidden" name="origin" class="form-control mb-2 origin" value="{{ $route->trip_route }}" style="font-size: 11px">
    <input type="hidden" name="waypoint_1" class="form-control mb-2 waypoint" value="{{ $route->waypont_one }}" style="font-size: 11px">
    <input type="hidden" name="waypoint_2" class="form-control mb-2 waypoint" value="{{ $route->waypont_two }}" style="font-size: 11px">
    <input type="hidden" name="waypoint_3" class="form-control mb-2 waypoint" value="{{ $route->waypont_three }}" placeholder="Waypoint Lat,Lng" style="font-size: 11px">
    <input type="hidden" name="waypoint_4" class="form-control mb-2 waypoint" value="{{ $route->waypont_four }}" placeholder="Waypoint Lat,Lng" style="font-size: 11px">
    <input type="hidden" name="waypoint_5" class="form-control mb-2 waypoint" value="{{ $route->waypont_five }}" placeholder="Waypoint Lat,Lng" style="font-size: 11px">
    <input type="hidden" name="waypoint_6" class="form-control mb-2 waypoint" value="{{ $route->waypont_six }}" placeholder="Waypoint Lat,Lng" style="font-size: 11px">
    <input type="hidden" name="waypoint_7" class="form-control mb-2 waypoint" value="{{ $route->waypont_seven }}" placeholder="Waypoint Lat,Lng" style="font-size: 11px">
    <input type="hidden" name="waypoint_8" class="form-control mb-2 waypoint" value="{{ $route->waypont_eight }}" placeholder="Waypoint Lat,Lng" style="font-size: 11px">
    <input type="hidden" name="destination" class="form-control mb-2 destination" value="{{ $route->destination }}" placeholder="Destination Lat,Lng" style="font-size: 11px">
</div>


@endsection

@push('custom-scripts')
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js" ></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&v=weekly&libraries=drawing,geometry,places" ></script>

<script defer>
    function approverouteHead() {
        var dataTwo = new FormData;
        dataTwo.append('_token', '{{ csrf_token() }}');
        dataTwo.append('schooltrip_id', '{{ $route->id }}');


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

            var directionDisplay;
            var directionsService = new google.maps.DirectionsService();

            var map;

            var infowindow = new google.maps.InfoWindow();

            let markers = [];

            function initialize() {
                directionsDisplay = new google.maps.DirectionsRenderer({
                    //suppressMarkers: true
                });

                var lats = '{{ $settings->lat }}' - 0;
                var lngs = '{{ $settings->lng }}' - 0;
              

                map = new google.maps.Map(document.getElementById("map"), {
                    center: { lat: lats, lng: lngs },
                    zoom: 10,
                });
    
                directionsDisplay.setMap(map);     

                directionsDisplay.setPanel(document.getElementById("content"));
            }

            function calcRoute() {

                
                var waypts = [];

                $('.waypoint').each(function(index, ele){
                  var wayLat = this.value;
                   
                  var ends = this.value.length - 1;

                  var subst = this.value.substr(1, ends -1);

                  var lat = subst.split(" ");

                  let myLatLng = { lat: lat[0].replace(',', '') -0, lng: lat[1] -0 };

                  var marker = new google.maps.Marker({
                        position: myLatLng,
                        map: map
                  });

                  if (wayLat) {
                       
                      stop = new google.maps.LatLng(myLatLng)
                      waypts.push({
                            location: stop,
                            stopover: true
                      });

                      markers.push(marker);
                  }

                    
                })



                

                var origin = $('.origin').val();

                var endorigin = origin.length - 1;

                origin = origin.substr(1,  endorigin - 1);

                origin = origin.split(" ")

                var myoring = { lat: origin[0].replace(',','') - 0, lng: origin[1] - 0 };


                var dest = $('.destination').val();

                var enddest = dest.length - 1;

                dest = dest.substr(1,  enddest - 1);

                dest = dest.split(" ")

                var mydest = { lat: dest[0].replace(',','') - 0, lng: dest[1] - 0 };

                console.log(mydest);

                var omarker = new google.maps.Marker({
                        position: myoring,
                        map: map
                });

                
                start = new google.maps.LatLng(origin);
                //end = new google.maps.LatLng(destination);
                
                
                
                var request = {
                    origin: myoring,
                    destination: mydest,
                    waypoints: waypts,
                    optimizeWaypoints: true,
                    travelMode: google.maps.DirectionsTravelMode.DRIVING
                };

                directionsService.route(request, function (response, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        directionsDisplay.setDirections(response);
                        var route = response.routes[0];

                        for (let i = 0; i < markers.length; i++) {
                            markers[i].setMap(null);
                        }
                        console.log(omarker);
                        omarker.setMap(null);
                    }
                });
            }

        initialize();

        calcRoute();
</script>
@endpush