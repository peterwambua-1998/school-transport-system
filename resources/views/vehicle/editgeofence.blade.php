@extends('layouts.app')
@push('plugin-styles')
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
  </style>
@endpush
@section('content')


<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('vehicles.index')}}">Zone GeoFence</a></li>
      <li class="breadcrumb-item active" aria-current="page">Update</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('vehicles.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
    </div>
</nav>

<div id="altres"></div>

@if (Session::has('unsuccess'))
<div class="alert alert-danger" role="alert" id="danger">
    {{Session::get('unsuccess')}}
</div>
@endif
    

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                    <form action="{{ route('update_fence') }}" method="POST" id="myForm">
                        @csrf 
                        @method('PATCH')
                        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}" required>
                        <input type="hidden" name="arrayone_first" id="arrayone_first" required>
                        <input type="hidden" name="arrayone_second" id="arrayone_second" required>
                        <input type="hidden" name="arraytwo_first" id="arraytwo_first" required>
                        <input type="hidden" name="arraytwo_second" id="arraytwo_second" required>
                        <input type="hidden" name="arraythree_first" id="arraythree_first" required>
                        <input type="hidden" name="arraythree_second" id="arraythree_second" required>
                        <input type="hidden" name="arrayfour_first" id="arrayfour_first" required>
                        <input type="hidden" name="arrayfour_second" id="arrayfour_second" required>
                       
                        <h5 class="mt-3 card-title">Update GeoFence for Vehicle {{$vehicle->plate_num}}</h5>
                        <hr>
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12">
                                <div class="map-div p-2">
                                    <div id="map"></div>
                                   
                                </div>
                            </div>
                        </div>
                        <div id="append-inputs" style="display: none"></div>
                       
                    </form>
                    
                    <div class="text-center" >
                        <button type="button" id="save-fence" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Update Vehicle GeoFence</button>
                    </div>
            </div>
        </div>
    </div>

</div>



@endsection


@push('custom-scripts')
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js" ></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&callback=initMap&v=weekly&libraries=drawing" defer></script>

<script defer>



var mapOptions;
var map;

var coordinates = []
let new_coordinates = []
let lastElement

function initMap() {
    var lats = '{{ $settings->lat }}' - 0;
    var lngs = '{{ $settings->lng }}' - 0;
    var location = new google.maps.LatLng(lats, lngs)
    mapOptions = {
        zoom: 10,
        center: location,
        mapTypeId: google.maps.MapTypeId.RoadMap
    }
    map = new google.maps.Map(document.getElementById('map'), mapOptions)
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


$('#save-fence').on('click', function() {

  if ($('#append-inputs').children().length <= 0) {
        let tem = `
        <div class="alert alert-danger" role="alert" id="danger">
            Please create vehicle geo fence
        </div>
        `;
        $('#altres').children().remove();
        $('#altres').append(tem);
        $("html, body").animate({ scrollTop: 0 }, "slow");
        return;
    }

    $("#myForm").submit();

  
});
window.initMap = initMap;





</script>
@endpush