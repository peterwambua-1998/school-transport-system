@extends('layouts.app')
@push('plugin-styles')
<link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
<style>
  .page-content{
      padding: 0px !important;
  }
  .col-md-9, .row {
    margin-left: 0px !important;
    margin-right: 0px !important;
    padding-left: 0px !important;
    padding-right: 0px !important;
  }
  #map {
    width: 100%;
    height: 78vh;
    border-radius: 0.25rem;
  }

  #map-right {
    width: 100%;
    height: 78vh;

  }
</style>
@endpush
@section('content')

<div class="p-1">
  <div class="card">
    <div class="card-body" style="padding: 10px">
      <div class="row">
        <div class="col-md-9">
          <div id="map"></div>
        </div>
        <div class="col-md-3 detailsall">
          <div id="map-right" style="overflow-x:hidden;overflow-y:scroll">
            <h5 class="mt-3">Driver Details</h5>
            <hr style="margin-top: 5px;">
            <ul class="list-unstyled chat-list px-1">
              
              <li class="chat-item pe-1 mb-3">
                <a href="javascript:;" class="d-flex align-items-center">
                  <figure class="mb-0 me-2">
                    @if ($veh->image)
                    <img src="{{ asset('store/'.$veh->image) }}" class="img-xs" alt="vehicle">
                    @else
                    <img src="{{ asset('images/bus2.jpg') }}" class="img-xs " alt="vehicle">
                    @endif
                    
                    <div class="status online"></div>
                  </figure>
                  <div class="d-flex justify-content-between flex-grow-1 border-bottom">
                    <div>
                      <p class="text-body fw-bolder mb-1">{{$driver->name}}</p>
                      <p class="text-muted tx-13">{{$driver->phone_num}}</p>
                    </div>
                  </div>
                </a>
              </li>
              
            </ul>
          </div>
      
        </div>
      
        <div class="col-md-3 details">
          @if ($veh->image)
                    <img src="{{ asset('store/'.$veh->image) }}" class="img-fluid " alt="vehicle" style="border-radius: 5px;">
                    @else
                    <img src="{{ asset('images/bus2.jpg') }}" class="img-fluid " alt="vehicle" style="border-radius: 5px;">
                    @endif
          <hr style="margin-top: 5px;">
          <div class="row" >
            <div class="col">
              <h5 class="mb-2" style="font-weight: 600">Speed</h5>
              <p class="speed"></p>
            </div>
            <div class="col">
              <h5 class="mb-2" style="font-weight: 600">Heading</h5>
              <p class="head"></p>
            </div>
          </div>
          <hr>
          <div class="row mb-2">
            <div class="col">
              <p class="mb-2" style="font-weight: 600">Driver Details</p>
              <div id="dr-image"></div>
              <p id="driver"></p>
              <p id="driver-number"></p>
            </div>
            <div class="col">
              <p class="mb-2" style="font-weight: 600">Vehicle Details</p>
              <p class="vhl-title"></p>
              <p class="vhl-plate"></p>
            </div>
            
          </div>
          <div class="mt-3">
            <button class="btn btn-success students-modal" data-bs-toggle="modal" data-bs-target="#exampleModal"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px" name="people-outline"></ion-icon> Students List</button>
            <a href="{{route('vehicles.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="arrow-back-circle-outline"></ion-icon> Back</a>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
      </div>
      <div class="modal-body p-3">
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
              <thead>
                  <tr>
                      <th>#</th>
                      <th>Student</th>
                      <th>{{$tr->grade_class ?? 'Grade'}}</th>
                      <th>Stream</th>
                      <th>Parent</th>
                      <th>Parent Phone</th>
                  </tr>
              </thead>
              <tbody id="students-table">

              </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush
@push('custom-scripts')
<script src="https://js.pusher.com/beams/1.0/push-notifications-cdn.js"></script>
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js" ></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&libraries=drawing,geometry,places&v=weekly" defer></script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="{{ asset('assets/js/data-table.js') }}"></script>
<script defer>
// Note: This example requires that you consent to location sharing when
// prompted by your browser. If you see the error "The Geolocation service
// failed.", it means you probably did not give permission for the browser to
// locate you.

myMap();

$('.details').show();
$('.detailsall').hide();
$('.students-modal').hide();
$('.show-all').on('click', function () {
  $('.details').show();
});

var directionsService;
let map, infoWindow;

function convertDegreesToCompass(degrees) {
    if (degrees >= 0 && degrees <= 360) {
      const compassDirections = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
      const index = Math.round((degrees % 360) / 22.5);
      return compassDirections[index];
    } else {
      return 'Invalid input';
    }
}

var data = new FormData;
data.append('_token','{{csrf_token()}}');
data.append('id', '{{$vehicle}}');
$.ajax({
    type: "POST",
    url: "{{ route('get_vehicle') }}",
    processData: false,
    contentType: false,
    cache: false,
    data: data,
    error: function (err) {
        console.log(err)
    },
    success: function (response) {
      console.log(response);
      $('.detailsall').hide();
      $('.details').show();
      $('.vhl-title').text(response[0].title);
      $('.vhl-plate').text(response[0].plate_num);
      $('#driver').text(response[1].name);
      $('#driver-number').text(response[1].phone_num);
      $('.speed').text(Math.round(((response[0].speed - 0) * 1.60934)) + ' Km/h');
      let img;
      if (response[1].image) {
         img = `
          <img class="wd-80 ht-80 rounded-circle" src="{{ asset('store/${response[1].image}') }}" alt="">
        `;
      } else {
        if (response[1].gender == "male") {
           img =  `<img class="wd-50 ht-50 rounded-circle" src="https://cdn-icons-png.flaticon.com/512/9875/9875255.png" >`;
        } else {
           img =  `<img class="wd-50 ht-50 rounded-circle" src="https://cdn-icons-png.flaticon.com/512/9875/9875392.png" >`;
        }
      }
      $('#dr-image').append(img);
      $('.head').text(convertDegreesToCompass(response[0].head));
    }
  });
function myMap() {
  $.ajax({
    type: "GET",
    url: "{{ route('all_vehicles', $vehicle) }}",
    processData: false,
    contentType: false,
    cache: false,

    error: function (err) {
        console.log(err)
    },
    success: function (response) {
      console.log(response);
      let routes_coordinates = [];
      let locations = [];
      let waypoints = [];
      let labels = [];
      let makersObject = [];
      let geofenceObject = [];
      let locs = response[0];
      let geofence = response[1];
      let origin, destination, waypoint_1, waypoint_2, waypoint_3, waypoint_4, waypoint_5, waypoint_6, waypoint_7, waypoint_8;
      locations.push(
          { lat: locs[0] - 0, lng: locs[1] - 0, id: locs[2] }
      );
      labels.push(locs[3]);

      Pusher.logToConsole = true;

      var pusher = new Pusher('05d822d3f46eb0987d53', {
          cluster: 'ap2',
          encrypted: true
      });

      var channel = pusher.subscribe('notifications-schoolapp.'+'{{$vehicle}}');

      

      var lats = '{{ $settings->lat }}' - 0;
      var lngs = '{{ $settings->lng }}' - 0;
      directionsService = new google.maps.DirectionsService()
     
      var loc = { lat: lats, lng: lngs }
      map = new google.maps.Map(document.getElementById("map"), {
        center: loc,
        zoom: 15,
      });
      infoWindow = new google.maps.InfoWindow({
        content: "",
        disableAutoPan: true,
      });


    
      // Add some markers to the map.

      const markers = locations.map((position, i) => {
        const label = labels[i];

        //<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M256 0C390.4 0 480 35.2 480 80V96l0 32c17.7 0 32 14.3 32 32v64c0 17.7-14.3 32-32 32l0 160c0 17.7-14.3 32-32 32v32c0 17.7-14.3 32-32 32H384c-17.7 0-32-14.3-32-32V448H160v32c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32l0-32c-17.7 0-32-14.3-32-32l0-160c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h0V96h0V80C32 35.2 121.6 0 256 0zM96 160v96c0 17.7 14.3 32 32 32H240V128H128c-17.7 0-32 14.3-32 32zM272 288H384c17.7 0 32-14.3 32-32V160c0-17.7-14.3-32-32-32H272V288zM112 400a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm288 0a32 32 0 1 0 0-64 32 32 0 1 0 0 64zM352 80c0-8.8-7.2-16-16-16H176c-8.8 0-16 7.2-16 16s7.2 16 16 16H336c8.8 0 16-7.2 16-16z"/></svg>
        const image =
          "https://cdn-icons-png.flaticon.com/512/3448/3448316.png";
        
        const marker = new google.maps.Marker({
          map,
          position,
          label: {text: label, color: "#1e293b", fontSize: "15px", className: "label-marker"},
          icon: {
            url: image,
            scaledSize: new google.maps.Size(50, 50), // scaled size
            
          }
        });

        let makerObj = {};
        makerObj[locations[i].id] = marker;

        
        makersObject.push(makerObj);
        
        marker.addListener("click", () => {
          infoWindow.setContent(label);
          infoWindow.open(map, marker);
          console.log(position);
          showMore(position);
          
        });

        
        return marker;
      });

      p = 1;
      var final_arr = [];
      for (let i = 0; i < geofence.length; i+=2) {
          const triangleCoords =  { 
              lat: geofence[i].coordinates - 0, 
              lng: geofence[p].coordinates - 0 
          };
          final_arr.push(triangleCoords);
          p+=2;
      }

      

      const bermudaTriangle = new google.maps.Polygon({
        paths: final_arr,
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#ADFF2F",
        fillOpacity: 0.5,
      });

      bermudaTriangle.setMap(map);

      var geofenceObj = {};

      geofenceObj[locations[0].id] = bermudaTriangle;

      geofenceObject.push(geofenceObj);
    

      channel.bind('App\\Events\\VehicleLocation', function(data) {
          console.log(data);

          var veh_id = data.vehicle_id;


          for (let t = 0; t < makersObject.length; t++) {
            console.log( Object.keys(makersObject[t])[0]);
            
            if (veh_id == Object.keys(makersObject[t])[0]) {
              var markerToMove = makersObject[t][veh_id];
              console.log(markerToMove);
              markerToMove.setPosition({lat: data.lat - 0, lng: data.lng - 0});

              var geofence = geofenceObject[t][veh_id];
              let is_notification_sent = sessionStorage.getItem('already-sent');
              if(is_notification_sent != 'already sent'){
                checkIfInsideGeoFence(data.lat - 0, data.lng - 0, geofence, veh_id);
                sessionStorage.setItem('already-sent','already sent');
              }
            }
          }
      }); 

      function checkIfInsideGeoFence(lat, lng, bermudaTriangle, id) {
        var status = google.maps.geometry.poly.containsLocation({lat: lat, lng:lng}, bermudaTriangle);

        if (status == false) {
          notify(status, id);

          var dataTwo = new FormData;
          dataTwo.append('_token', '{{ csrf_token() }}');
          dataTwo.append('vehicle_id', id);
          console.log('sending data');
          $.ajax({
            type: "POST",
            url: "{{ route('vehicleoutofzone') }}",
            processData: false,
            contentType: false,
            cache: false,
            data: dataTwo,
            error: function (err) {
                console.log(err)
            },
            success: function (response) {
              console.log(response);
            }
          });
        }
      }

      let user_id = "{{Auth::user()->id}}";

      const beamsClient = new PusherPushNotifications.Client({
        instanceId: "c880bb01-d93f-4eb8-9fd1-0a3003477735",
      });
      beamsClient
      .start()
      .then((beamsClient) => beamsClient.getDeviceId())
      .then((deviceId) => console.log("Successfully registered with Beams. Device ID:", deviceId))
      .then(() => beamsClient.addDeviceInterest(`transport-${user_id}`))
      .then(() => beamsClient.getDeviceInterests())
      .then((interests) => console.log("Current interests:", interests))
      .catch(console.error);

      function notify(status, id) {
        /*
        console.log(id);
        if (! status) {
          if (!("Notification" in window)) {
            
            alert("This browser does not support desktop notification");
          } else if (Notification.permission === "granted") {
            
            const notification = new Notification("one of the vehilces is out of zone ");

            
            
          } else if (Notification.permission !== "denied") {
            
            Notification.requestPermission().then((permission) => {
              
              if (permission === "granted") {
                const notification = new Notification("one of the vehilces is out of zone");
              
              }
            });
          }
        }
        */
      }

      //vehicle routes
      for (let x = 0; x < response[2].length; x++) {
        let or = response[2][x].coordinates['origin'].substr(1, (response[2][x].coordinates['origin'].length - 1) -1).split(" ");
        let de = response[2][x].coordinates['destination'].substr(1, (response[2][x].coordinates['destination'].length - 1) -1).split(" ");
        
        origin = { lat: or[0].replace(',', '') -0, lng: or[1] -0 };   
        destination = { lat: de[0].replace(',', '') -0, lng: de[1] -0 };
        
        
        
        
        waypoints = [];
        if (response[2][x].coordinates['way_point_1']) {
          let p1 = response[2][x].coordinates['way_point_1'].substr(1, (response[2][x].coordinates['way_point_1'].length - 1) -1).split(" ");
          waypoint_1 = { lat: p1[0].replace(',', '') -0, lng: p1[1] -0 };
          waypoints.push({location: waypoint_1,stopover: true})
        }

        if (response[2][x].coordinates['way_point_2']) {
          let p2 = response[2][x].coordinates['way_point_2'].substr(1, (response[2][x].coordinates['way_point_2'].length - 1) -1).split(" ");
          waypoint_2 = { lat: p2[0].replace(',', '') -0, lng: p2[1] -0 };
          waypoints.push({location: waypoint_2,stopover: true})
        }

        if (response[2][x].coordinates['way_point_3']) {
          let p3 = response[2][x].coordinates['way_point_3'].substr(1, (response[2][x].coordinates['way_point_3'].length - 1) -1).split(" ");
          waypoint_3 = { lat: p3[0].replace(',', '') -0, lng: p3[1] -0 };
          waypoints.push({location: waypoint_3,stopover: true})
        }

        if (response[2][x].coordinates['way_point_4']) {
          let p4 = response[2][x].coordinates['way_point_4'].substr(1, (response[2][x].coordinates['way_point_4'].length - 1) -1).split(" ");
          waypoint_4 = { lat: p4[0].replace(',', '') -0, lng: p4[1] -0 };
          waypoints.push({location: waypoint_4,stopover: true})
        }

        if (response[2][x].coordinates['way_point_5']) {
          let p5 = response[2][x].coordinates['way_point_5'].substr(1, (response[2][x].coordinates['way_point_5'].length - 1) -1).split(" ");
          waypoint_5 = { lat: p5[0].replace(',', '') -0, lng: p5[1] -0 };
          waypoints.push({location: waypoint_5,stopover: true})
        }

        if (response[2][x].coordinates['way_point_6']) {
          let p6 = response[2][x].coordinates['way_point_6'].substr(1, (response[2][x].coordinates['way_point_6'].length - 1) -1).split(" ");
          waypoint_6 = { lat: p6[0].replace(',', '') -0, lng: p6[1] -0 };
          waypoints.push({location: waypoint_6,stopover: true})
        }

        if (response[2][x].coordinates['way_point_7']) {
          let p7 = response[2][x].coordinates['way_point_7'].substr(1, (response[2][x].coordinates['way_point_7'].length - 1) -1).split(" ");
          waypoint_7 = { lat: p7[0].replace(',', '') -0, lng: p7[1] -0 };
          waypoints.push({location: waypoint_7,stopover: true})
        }

        if (response[2][x].coordinates['way_point_8']) {
          let p8 = response[2][x].coordinates['way_point_8'].substr(1, (response[2][x].coordinates['way_point_8'].length - 1) -1).split(" ");
          waypoint_8 = { lat: p8[0].replace(',', '') -0, lng: p8[1] -0 };
          waypoints.push({location: waypoint_8,stopover: true})
        }
        
        console.log(waypoints);

        calculateRoute(origin, waypoints, response[2][x]['trip_id'])
      }


      var vehicles = [];

      window.initMap = map;
    } 

  });


  function calculateRoute(origin, waypoints, trip_id) {
    let color = 'blue';
    var request = {
        origin: origin,
        destination: origin,
        waypoints: waypoints,
        optimizeWaypoints: true,
        travelMode: google.maps.DirectionsTravelMode.DRIVING
    };
    directionsService.route(request, function(result, status) {
        if (status == "OK") {
            var directionsDisplay = new google.maps.DirectionsRenderer({map:map,suppressMarkers:true, polylineOptions: {strokeColor: color,strokeOpacity: 0.7,strokeWeight: 5}});
            directionsDisplay.setDirections(result);
        }
    }); 

    console.log('trip === '+trip_id);

    if (trip_id) {
      $.ajax({
        type: 'get',
        url: `/students-in-trip/${trip_id}`,
        processData: false,
        cache: false,
        contentType: false,
        error: function(err) {
          console.log(err);
        },
        success: function(response){
          console.log(response);
          $('#students-table').children().remove();
          $('.modal-title').children().remove();
          let num = 1;
          for (let i = 0; i < response[1].length; i++) {
            let template = `
              <tr>
                <td>${num}</td>
                <td>${response[1][i].name}</td>
                <td>${response[1][i].grade}</td>
                <td>${response[1][i].stream}</td>
                <td>${response[1][i].parent}</td>
                <td>${response[1][i].parent_phone}</td>
              </tr>
            `;
            num++;

            $('#students-table').append(template);
          }
          let title = `<span>Trip: ${response[0].title}</span><span style='margin-left: 15px'>From: ${response[0].time_from}</span><span style='margin-left: 15px'>To: ${response[0].time_to}</span>`
          $('.modal-title').append(title);
          $('.students-modal').show();
         
        }

      })
    }

    

  }
}
</script>
@endpush