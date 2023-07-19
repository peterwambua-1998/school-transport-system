@extends('layouts.app')
@push('plugin-styles')
<style>
    .page-content {
        padding: 1px !important;
    }
    .my-grid {
      display: grid;
      grid-template-columns: 70% 30%;
    }
    .map-div {
      height: 80vh;

    }
    #map {
        width: 100%;
        height: 100%;
    }
</style>
@endpush
@section('content')

<div class="my-grid">
  <div class="p-2 map-div">
    <div id="map"></div>
  </div>
  <div class="p-3">
    <ul class="list-group mb-3">
      <li class="list-group-item active">
          <h5 class=""><span style="font-weight: bold">{{ $route->title }}</span></h5>
      </li>
      <li class="list-group-item">
          <span class="text-muted">Description:</span> <span style="font-weight: bold">{{ $route->description }}</span>
      </li>
      @php
          $route_zone = DB::table('route_zones')->where('route_id','=',$route->id)->first();

          $zone_price = App\Models\Zone::where('id','=', $route_zone->zone_id)->first()->price;
      @endphp
      <li class="list-group-item">
          <span class="text-muted">Price:</span> <span style="font-weight: bold">{{ $settings->currecy ?? 'ksh' }} {{ $zone_price }}</span></span>
      </li>
    </ul>
    <div>
      <a style="margin-right: 10px" href="{{ route('polyline_edit', Crypt::encrypt($polylines->id)) }}" class="btn btn-success  add-driver text-center ">
        Edit Path
      </a>

      <a href="{{route('routes.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="arrow-back-circle-outline"></ion-icon>Back</a>
    </div>
    
    </div>
</div>



<div style="display: none">
  <input type="hidden" name="origin" class="form-control mb-2 origin" value="{{ $polylines->origin }}" style="font-size: 11px">
  <input type="hidden" name="waypoint_1" class="form-control mb-2 waypoint" value="{{ $polylines->way_point_1 }}" style="font-size: 11px">
  <input type="hidden" name="waypoint_2" class="form-control mb-2 waypoint" value="{{ $polylines->way_point_2 }}" style="font-size: 11px">  
  <input type="hidden" name="waypoint_3" class="form-control mb-2 waypoint" value="{{ $polylines->way_point_3 }}" placeholder="Waypoint Lat,Lng" style="font-size: 11px">
  <input type="hidden" name="waypoint_4" class="form-control mb-2 waypoint" value="{{ $polylines->way_point_4 }}" placeholder="Waypoint Lat,Lng" style="font-size: 11px">
  <input type="hidden" name="waypoint_5" class="form-control mb-2 waypoint" value="{{ $polylines->way_point_5 }}" placeholder="Waypoint Lat,Lng" style="font-size: 11px">
  <input type="hidden" name="waypoint_6" class="form-control mb-2 waypoint" value="{{ $polylines->way_point_6 }}" placeholder="Waypoint Lat,Lng" style="font-size: 11px">
  <input type="hidden" name="waypoint_7" class="form-control mb-2 waypoint" value="{{ $polylines->way_point_7 }}" placeholder="Waypoint Lat,Lng" style="font-size: 11px">
  <input type="hidden" name="waypoint_8" class="form-control mb-2 waypoint" value="{{ $polylines->way_point_8 }}" placeholder="Waypoint Lat,Lng" style="font-size: 11px">
  <input type="hidden" name="destination" class="form-control mb-2 destination" value="{{ $polylines->destination }}" placeholder="Destination Lat,Lng" style="font-size: 11px">
  @foreach ($colors as $color)
      <p class="colors">{{ $color }}</p>
  @endforeach
</div>
@endsection

@push('plugin-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&v=weekly&libraries=drawing,geometry,places" ></script>
<script  src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js" ></script>
@endpush

@push('custom-scripts')
<script defer>

  var directionDisplay;
  var directionsService = new google.maps.DirectionsService();

  var map;

  var infowindow = new google.maps.InfoWindow();

  function initMap() {
      directionsDisplay = new google.maps.DirectionsRenderer({
          suppressMarkers: true
      });

      var lats = '{{ $settings->lat }}' - 0;
      var lngs = '{{ $settings->lng }}' - 0;
    

      map = new google.maps.Map(document.getElementById("map"), {
          center: { lat: lats, lng: lngs },
          zoom: 14,
      });

      directionsDisplay.setMap(map);

      let markers = [];
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
          }

          
      })



      

      var origin = $('.origin').val();

      var endorigin = origin.length - 1;

      origin = origin.substr(1,  endorigin - 1);

      origin = origin.split(" ")

      var myoring = { lat: origin[0].replace(',','') - 0, lng: origin[1] - 0 };


      console.log(myoring);

      var omarker = new google.maps.Marker({
              position: myoring,
              map: map
      });

      

      var destination = $('.destination').val();

    

      
      start = new google.maps.LatLng(origin);
      end = new google.maps.LatLng(destination);
      
      
      
      var request = {
          origin: myoring,
          destination: myoring,
          waypoints: waypts,
          optimizeWaypoints: true,
          travelMode: google.maps.DirectionsTravelMode.DRIVING
      };

      directionsService.route(request, function (response, status) {
          if (status == google.maps.DirectionsStatus.OK) {
              directionsDisplay.setDirections(response);
              var route = response.routes[0];
          }
      });
  }

  initMap();

  calcRoute();

</script>
@endpush