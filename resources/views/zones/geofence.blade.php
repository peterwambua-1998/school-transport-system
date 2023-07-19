@extends('layouts.app')
@push('plugin-styles')
<style>
    .page-content {
        padding: 0 !important;
    }
    .my-grid {
      display: grid;
      grid-template-columns: 70% 30%;
      
    }
    #map {
        width: 100%;
        height: 73vh;
        margin: 1% auto;
    }
</style>
@endpush
@section('content')

<div class="my-grid p-2">
  <div class="">
    <div id="map"></div>
  </div>
  <div class="p-1">
    <div>

        <ul class="list-group mb-3">
            <li class="list-group-item active">
                <h5 class=""><span style="font-weight: bold">{{ $zone->name }}</span></h5>
            </li>
            <li class="list-group-item">
                <span class="text-muted">Description:</span> <span style="font-weight: bold">{{ $zone->description }}</span>
            </li>
            <li class="list-group-item">
                <span class="text-muted">Two-Way Price:</span> <span style="font-weight: bold">{{ $settings->currency ?? 'USD' }} {{ $zone->price }}</span>
            </li>
            <li class="list-group-item">
                <span class="text-muted">One-Way Price:</span> <span style="font-weight: bold">{{ $settings->currency ?? 'USD' }} {{ $zone->oneway_price }}</span>
            </li>
        </ul>
          
        <ul class="list-group mb-3">
            <li class="list-group-item active">
                <h5 class=""><span style="font-weight: bold">Routes</span></h5>
            </li>
            <?php $n = 1; ?>
            @foreach ($routes as $route)
            <?php $rt = App\Models\Route::where('id','=',$route->route_id)->first(); ?>
            <li class="list-group-item">
                <span class="text-muted">Route {{$n}}:</span> <span style="font-weight: bold">{{$rt->title}}</span>
            </li>
            <?php $n++; ?>
            @endforeach
        </ul>
        <div>
            <a href="{{route('zones.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="arrow-back-circle-outline"></ion-icon> Back</a>
            <a href="{{route('zoneGeoFenceEdit', Crypt::encrypt($zone->id))}}" class="btn btn-success  add-driver text-center">
                Edit Path
            </a>
          
           
        </div>
        
    </div>

    

    {{-- {{ route('polyline_edit', Crypt::encrypt($polylines->id)) }} --}}
    
  </div>
</div>

@endsection

@push('plugin-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&v=weekly&libraries=drawing,geometry,places" ></script>
<script  src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js" ></script>
@endpush

@push('custom-scripts')
<script defer>

let lats = '{{ $settings->lat }}' - 0;
let lngs = '{{ $settings->lng }}' - 0;

let loc = { lat: lats, lng: lngs };

let map = new google.maps.Map(document.getElementById("map"), {
    center: loc,
    zoom: 10,
});

    $.ajax({
        type: "GET",
        url: '{{ route("getZoneGeoFenceCoords", $zone->id) }}',
        processData: false,
        contentType: false,
        cache: false,

        error: function (err) {
            console.log(err)
        },
        success: function (response) {
            console.log(response);
            x = 1;
            var final_arr = [];
            for (let i = 0; i < response.length; i+=2) {
                const triangleCoords =  { 
                    lat: response[i].corrdinates - 0, 
                    lng: response[x].corrdinates - 0 
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
            
            map.fitBounds(bermudaTriangle.getBounds());
            console.log(final_arr);
        }
    });
</script>
@endpush