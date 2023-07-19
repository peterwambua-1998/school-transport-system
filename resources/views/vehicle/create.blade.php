@extends('layouts.app')
@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
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
        margin-top: 5px;
        text-overflow: ellipsis;
        width: 300px;
        padding: 0.5%;
        height: 28px;
        border: none;
        margin-right: 1%;
        filter: drop-shadow(0 4px 3px rgb(0 0 0 / 0.07)) drop-shadow(0 2px 2px rgb(0 0 0 / 0.06));
    }

    .issues {
        color: #ff3366;
    }
</style>
@endpush
@section('content')


<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('vehicles.index')}}">Vehicle</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('vehicles.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon>Cancel</a>
    </div>
</nav>

<div id="altres"></div>

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


<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Vehicle</h4>
                <hr>

                <form action="{{ route('vehicles.store') }}" method="POST" id="myForm" enctype="multipart/form-data">
                    @csrf 

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="title">Vehicle Identifier</label>
                            <input type="text" name="title" class="form-control" id="veh_identifier" placeholder="Vehicle Identifier" required>
                            <span class="issues" id="vehicle_id"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">Registration Number</label>
                            <input type="text" name="platenum" class="form-control" id="reg_num" placeholder="Registration Number" required>
                            <span class="issues" id="reg_error"></span>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="title">Number Of Seats</label>
                            <input type="text" name="num_of_seats" class="form-control" id="num_of_seats" placeholder="Number Of Seats" required>
                            <span class="issues" id="num_seats_error"></span>
                        
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="inputState">Driver</label> <span class="text-success" style="margin-left: 10px">(Driver should have license)</span>
                            @if (count($drivers) <= 0)
                            <p class="text-danger">Please add Driver</p>
                            @else
                            <select id="driver" class="form-select" name="driver" required>
                                <option>select...</option>
                                @foreach ($drivers as $driver)
                                    @if ($driver->license)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @endif
                            <span class="issues" id="driver_error"></span>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="inputState"> Attendant</label>
                            @if (count($attendants) <= 0)
                            <p class="text-danger">Please add attendant</p>
                            @else
                            <select id="attendant" class="form-select" name="attendant" required>
                                <option>select...</option>
                                @foreach ($attendants as $attendant)
                                <option value="{{ $attendant->id }}">{{ $attendant->name }}</option>
                                @endforeach
                            </select>
                            @endif
                            <span class="issues" id="attendant_error"></span>

                        </div>
        
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="inputState">Route</label>
                            @if (count($routes) <= 0)
                                <p class="text-danger">First add route for this bus</p>
                            @else 
                            <select  id="routes" class="form-select js-example-basic-multiple" multiple name="routes[]" required>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->title }}</option>
                                @endforeach
                            </select>
                            @endif
                            <span class="issues" id="routes_error"></span>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="mileage">Mileage (KM)</label>
                            <input type="number" name="mileage" class="form-control" id="mileage" placeholder="1000" required>
                            <span class="issues" id="mileage_error"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mileage">Last Service (KM)</label>
                            <input type="number" name="last_service" id="last_service" class="form-control" placeholder="1000">
                            <span class="issues" id="last_service_error"></span>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="service_interval">Service Interval (KM)</label>
                            <input type="number" name="service_interval" class="form-control" id="service_interval" placeholder="1000" required>
                            <span class="issues" id="service_interval_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="image">Vehicle Photo</label>
                            <input class="form-control-file" type="file" name="image" id="myDropify">
                        
                        </div>
                    </div>

                    <div class="row mb-3 mt-3">
                        <label for="" class="form-label">Bus GeoFecing</label>
                        <span class="issues" id="map_error"></span>
                        
                        <div class="col-md-12 map-div">
                            <input
                                id="pac-input"
                                class="controls"
                                type="text"
                                placeholder="Search Box"
                                />
                            <div id="map"></div>
                        </div>
                    </div>

                <div id="append-inputs" style="display: none"></div>
                <div class="text-center mt-3">
                    <button type="button" id="save-fence" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Add Vehicle</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@push('plugin-scripts')
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js" ></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&callback=initMap&v=weekly&libraries=places,drawing" defer></script>
<script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/dropify.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script defer>

var mapOptions;
var map;
var coordinates = [];
let new_coordinates = [];
let lastElement;


function initMap() {
    var lats = '{{ $settings->lat }}' - 0;
    var lngs = '{{ $settings->lng }}' - 0;

    var location = new google.maps.LatLng(lats, lngs);

    mapOptions = {
        zoom: 14,
        center: location,
        mapTypeId: google.maps.MapTypeId.RoadMap
    }
    map = new google.maps.Map(document.getElementById('map'), mapOptions);

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

    var all_overlays = [];
    var selectedShape;
    var drawingManager = new google.maps.drawing.DrawingManager({
        //drawingMode: google.maps.drawing.OverlayType.MARKER,
        //drawingControl: true,
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [
                //google.maps.drawing.OverlayType.MARKER,
                //google.maps.drawing.OverlayType.CIRCLE,
                google.maps.drawing.OverlayType.POLYGON,
                //google.maps.drawing.OverlayType.RECTANGLE
            ]
        },
        markerOptions: {
            //icon: 'images/beachflag.png'
        },
        circleOptions: {
            fillColor: '#ffff00',
            fillOpacity: 0.2,
            strokeWeight: 3,
            clickable: false,
            editable: true,
            zIndex: 1
        },
        polygonOptions: {
            clickable: true,
            draggable: false,
            editable: true,
            // fillColor: '#ffff00',
            fillColor: '#ADFF2F',
            fillOpacity: 0.5,
            
        },
        rectangleOptions: {
            clickable: true,
            draggable: true,
            editable: true,
            fillColor: '#ffff00',
            fillOpacity: 0.5,
        }
    });

    function clearSelection() {
        if (selectedShape) {
            selectedShape.setEditable(false);
            selectedShape = null;
        }
    }
    //to disable drawing tools
    function stopDrawing() {
        drawingManager.setMap(null);
    }

    function setSelection(shape) {
        clearSelection();
        stopDrawing()
        selectedShape = shape;
        shape.setEditable(true);
    }

    function deleteSelectedShape() {
        if (selectedShape) {
            selectedShape.setMap(null);
            drawingManager.setMap(map);
            coordinates.splice(0, coordinates.length) 
            new_coordinates.splice(0, new_coordinates.length)
        }
    }

    function CenterControl(controlDiv, map) {

        // Set CSS for the control border.
        var controlUI = document.createElement('div');
        controlUI.style.backgroundColor = '#fff';
        controlUI.style.border = '2px solid #fff';
        controlUI.style.borderRadius = '3px';
        controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
        controlUI.style.cursor = 'pointer';
        controlUI.style.marginBottom = '22px';
        controlUI.style.textAlign = 'center';
        controlUI.title = 'Select to delete the shape';
        controlDiv.appendChild(controlUI);

        // Set CSS for the control interior.
        var controlText = document.createElement('div');
        controlText.style.color = 'rgb(25,25,25)';
        controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
        controlText.style.fontSize = '16px';
        controlText.style.lineHeight = '38px';
        controlText.style.paddingLeft = '5px';
        controlText.style.paddingRight = '5px';
        controlText.innerHTML = 'Delete Selected Area';
        controlUI.appendChild(controlText);

        //to delete the polygon
        controlUI.addEventListener('click', function () {
            deleteSelectedShape();
        });
    }

    drawingManager.setMap(map);

    var getPolygonCoords = function (newShape) {
        coordinates.splice(0, coordinates.length)
        var len = newShape.getPath().getLength();

        for (var i = 0; i < len; i++) {
            var arr = [];
            var myvalues = newShape.getPath().getAt(i).toUrlValue(6);
            var newArr = myvalues.split(",");
            new_coordinates.push(newArr);
            //console.log(newShape.getPath().getAt(i).toUrlValue(6));
        }
        getCoordinates();
    }

    google.maps.event.addListener(drawingManager, 'polygoncomplete', function (event) {
        event.getPath().getLength();
        google.maps.event.addListener(event, "dragend", getPolygonCoords(event));

        google.maps.event.addListener(event.getPath(), 'insert_at', function () {
            getPolygonCoords(event)
            
        });
        google.maps.event.addListener(event.getPath(), 'set_at', function () {
            getPolygonCoords(event)
        })
    })

    google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
        all_overlays.push(event);
        if (event.type !== google.maps.drawing.OverlayType.MARKER) {
            drawingManager.setDrawingMode(null);

            var newShape = event.overlay;
            newShape.type = event.type;
            google.maps.event.addListener(newShape, 'click', function () {
                setSelection(newShape);
            });
            setSelection(newShape);
        }
    })

    var centerControlDiv = document.createElement('div');
    var centerControl = new CenterControl(centerControlDiv, map);
    
    centerControlDiv.index = 1;
    map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(centerControlDiv);   
}


function getCoordinates() {
    var container = document.getElementById('append-inputs');
    $('#append-inputs').children().remove();

    for (let t = 0; t < new_coordinates.length; t++) {
    var input = document.createElement("input");
    input.type = "text";
    input.setAttribute('value', new_coordinates[t][0]);
    input.setAttribute('name', 'arrone[]');
    container.appendChild(input);

    var inputTwo = document.createElement("input");
    inputTwo.type = "text";
    inputTwo.setAttribute('name', 'arrtwo[]');
    inputTwo.setAttribute('value', new_coordinates[t][1]);
    container.appendChild(inputTwo);
  }

}

$('#save-fence').on('click', function(e) {

    

    if (!$('#veh_identifier').val()) {
        $('#vehicle_id').text('field required');
        $('#veh_identifier').focus();
        $('html, body').animate({
            scrollTop: "0px"
        }, 800);
        e.preventDefault();

        return;
    } else {
        $('#vehicle_id').text('');
    }

    if (!$('#reg_num').val()) {
        $('#reg_num').focus();
        $('#reg_error').text('field required');
        $('html, body').animate({
            scrollTop: "0px"
        }, 800);
        e.preventDefault();

        return;
    } else {
        $('#reg_error').text('');
    }

    if (!$('#num_of_seats').val()) {
        $('#num_of_seats').focus();
        $('#num_seats_error').text('field required');
        $('html, body').animate({
            scrollTop: "0px"
        }, 800);
        e.preventDefault();

        return;
    } else {
        $('#num_seats_error').text('');
    }

    if ($('#driver').val() == 'select...') {
        $('#driver').focus();
        $('#driver_error').text('field required');
        $('html, body').animate({
            scrollTop: "0px"
        }, 800);
    e.preventDefault();

        return;
    } else {
        $('#driver_error').text('');
    }

    if ($('#attendant').val() == 'select...') {
        $('#attendant').focus();
        $('html, body').animate({
            scrollTop: "0px"
        }, 800);
        $('#attendant_error').text('field required');
    e.preventDefault();

        return;
    } else {
        $('#attendant_error').text('');
    }


    if ($('#routes').val().length <= 0) {
        $('#routes').focus();
        $('#routes_error').text('field required');
        $('html, body').animate({
            scrollTop: "0px"
        }, 800);
    e.preventDefault();

        return;
    } else {
        $('#routes_error').text('');
    }

    if (!$('#mileage').val()) {
        $('#mileage').focus();
        $('#mileage_error').text('field required');
        $('html, body').animate({
            scrollTop: "0px"
        }, 800);
    e.preventDefault();

        return;
    } else {
        $('#mileage_error').text('');
    }

    if (!$('#last_service').val()) {
        $('#last_service').focus();
        $('#last_service_error').text('field required');
        $('html, body').animate({
            scrollTop: "0px"
        }, 800);
    e.preventDefault();

        return;
    } else {
        $('#last_service_error').text('');
    }


    if (!$('#service_interval').val()) {
        $('#service_interval_error').text('field required');
        $('html, body').animate({
            scrollTop: "0px"
        }, 800);
    e.preventDefault();

        return;
    } else {
        $('#service_interval_error').text('');
    }

    if ($('#append-inputs').children().length <= 0) {
        $('#map_error').text('Create bus geo fence.');
        $('html, body').animate({
            scrollTop: ($( document ). height() / 2) + 'px'
        }, 800);
        return;
    e.preventDefault();

    } else {
        $('#map_error').text('');
    }
   
    
    $("#myForm").submit();
});


window.initMap = initMap;

</script>
@endpush