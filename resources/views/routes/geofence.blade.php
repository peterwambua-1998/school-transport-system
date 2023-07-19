@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css">

<style>


    .pcoded-inner-content {
        padding: 0 !important;
        height: 100vh !important;
       
    }

    .main-body {
      height: 120%;
    }
    .outer-div {
       height: 100%;
        
        background: #d3d3d3;
    }

    .outer-div .row {
      height: 100% !important;
    }
    .dataTables_wrapper .dataTables_filter {
        float: none;
        text-align: center;
    }

    .vihcleGrid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        justify-content: end;
        padding-left: 20px;
        padding-right: 3%;
    }

    a {
        color: #fff;

    }

    a:hover {
        color: #fff;
    }

    .btn-create {
        background: #0071f3
    }

    .vihcleGrid .btn-create:hover {
        background: #012549;
    }
    
    .table-title-vehicle {
        font-size:18px;
        font-weight: 500;
        margin-bottom: 25px;
        
    }

    .span-delete {
        margin-right: 2vw;
        font-size: 20px;
    }

    #map {
        height: 100%;
        width: 100%;
        border-radius: 10px;
    }

    .div-map {
        height: 80vh;
        width: 100%;
        
    }

    .vehicle-details {
      background: #fff;
      height: 100%;
      border-top  : 10px solid #0162d1;
    }

    .vehicle-details-header {
      box-shadow: 0px 3px 10px 0px rgba(0, 0, 255, .2);
      height: 7vh;
      padding-left: 2%;
      padding-top: 2%;
      align-items: center;
      font-size: 20px;
    }

    .vhl-title {
      font-weight: bold;
      font-size: 15px;
      padding-left: 5%;
    }
    .vhl-plate {
      letter-spacing: 1px;
      font-weight: 100;
      padding-left: 5%;

    }
    

    .vehicle-details-image {
      height: 40vh;
      margin: 5%;

      
      
    }

    .vehicle-details-image img {
      border-radius: 10px;
    }

    .vehicle-details-grid {
      display: grid;
      grid-template-columns: 40% 35% 1fr;
      margin: 5%;
      
      padding-top: 25px;
      line-height: 1px;
      
      border-radius: 10px;
    }

    .speed {
      font-weight: bold;
      font-size: 18px;
    }

    

    .more-details .outer {
      
      height: 20vh;
      
    }

    
      
     .outer-row {
        padding: 2%;
        margin: 2%;
        border-radius: 10px;
        
        height: 10vh;
     }

     .img-outer {
      
       
     }
     .img-outer .image-outer-img {
      height: 80%;
      width: 65%;

     }

     .image-cover img{
      height: 100%;
      width: 50%;
     }

     .outer-row-text {
      line-height: 2px !important;
      margin-left: -16%;
      margin-top: 30px;
     }
    

     .custom-map-control-button {
        background-color: #fff;
        border: 0;
        border-radius: 2px;
        box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
        margin: 10px;
        padding: 0 0.5em;
        font: 400 18px Roboto, Arial, sans-serif;
        overflow: hidden;
        height: 40px;
        cursor: pointer;
      }
      .custom-map-control-button:hover {
        background: rgb(235, 235, 235);
      }

      .details {
        display: none
      }

      .detailsall {
        display: block;
        
      }


      .my-row {
        display: grid;
        grid-template-columns: 20% 1fr;
      }

      .driver-details-text div{
        position: relative;
        left: -20px;
        line-height: 2px;
        padding-top: 5px;
        
      }

      .driver-details-text div p {
        font-size: 15px;
      }

      .add-driver {
        position: absolute;
        bottom: 10px;
        width: 90%;
        left: 50%;
        transform: translateX(-50%);
      }
      .show-all {
        border: none;
        background: #4099ff;
        color: #fff;
        font-size: 15px;
      }

      .img-outer-img {
        height: 80%;
        width: 65%;
      }

      .save-btn {
        background: #0071f3;
        color:  #fff;
        border-radius: 10px !important; 
      }
    @media only screen and (max-width: 500px) and (orientation: portrait) {

      .pcoded-inner-content {
        padding: 0 !important;
        height: fit-content !important;
       
    }
      ::-webkit-scrollbar {
        display: block;
      }
      .details {
        margin: 2%;
        font-size: 12px;
      }
      .vehicle-details-image {
        display: none
      }

      .vehicle-details {
        border-radius: 10px;
        height: fit-content;
      }

      

      .speed {
        font-size: 12px
      }
      .speed i{
        display: none;
      }

      .outer-row {
        height: max-content;
        
      }

      .vehicle-details-header {
        font-size: 15px;
      }

      .image-cover {
        width: 30%;
      }
      .outer-row .image-outer-img {
        
        width: 20vw !important;
      }
      .outer-row-text {
        margin-left: 0px;
      }
      .detailsall {
        height: max-content !important;
      }
      
      .vehicle-details {
        height: max-content !important;
        text-align: left;
      }

      .add-driver {
        display: none
      }

      
      .img-outer {
        width: 100% !important;
 
      }

      .show-all {
        font-size: 10px;
        padding: 5px;
        border-radius: 5px;
      }
    }
</style>
@endsection
@section('content')
<form action="{{ route('geofence_store') }}" method="post" id="myForm">
  <div class="row" style="background: #d3d3d3">
    <div class="col-md-10"></div>
    <div class="col-md-2">
      <button type="button" id="reload-map" class="btn btn-primary mt-2">Refresh Map</button>
    </div>
    
  </div>
<div class="outer-div">

  

  <div class="row">
    <div class="col-xl-12 col-md-12 col-sm-12">
        <div class="div-map p-2">
            <div id="map"></div>
           
        </div>

        <div class="p-2">
          <button type="button"  class="btn btn-block save-btn" id="save-fence" style="background: #0071f3; color: #fff">Save Geo Fence</button>
        </div>

        
        
    </div>

    {{--
    <div class="col-xl-4 col-md-4 col-sm-12 details">
      <div class="vehicle-details">
        <div>
          <p class="vehicle-details-header">Fleet Details <small style="float: right; margin-right: 5px"><button class="show-all">Show All</button></small></p>
        </div>

        <div style="line-height: 9px;">
          <p class="vhl-title">Vehicle Title</p>
          <p class="vhl-plate">KBX 124D</p>
        </div>

        <div class="vehicle-details-image">
          <img src="{{ asset('images/bus2.jpg') }}" alt="" style="height: 100%; width: 100%">
        </div>

        <div class="vehicle-details-grid">
          <div>
            <p style="font-weight: 100">speed</p>
            <p class="speed">120 km/h </p>
          </div>
          <div>
            <p  style="font-weight: 100">Altitude</p>
            <p  class="speed">208m </p>
          </div>
          <div>
            <p style="font-weight: 100">Direction</p>
            <p class="speed">NE </p>
          </div>
        </div>


        <div class="outer-row">
          <p>Driver Details</p>
          <div class="row">
            <div class="col-md-4 col-sm-2 image-cover">
              <img src="{{ asset('images/person.png') }}" alt="" class="">
            </div>
            <div class="col-md-8 col-sm-8 outer-row-text">
              <p style="font-weight: bold" id="driver">Peter Wambua</p>
              <p id="driver-number">0715100539</p>
            </div>
          </div>
        </div>
        

      </div>
    </div>

    <div class="col-xl-4 col-md-4 col-sm-12 detailsall">
      <div class="vehicle-details">

        <div>
          <p class="vehicle-details-header">Driver Details</p>
        </div>

        
        
        <div class="outer-row">
         
          <div class="my-row">
            <div class="my-grid img-outer">
              <img src="{{ asset('images/person.png') }}" alt="" class="img-outer-img">
            </div>
            <div class="driver-details-text" >
              <div>
                <p style="font-weight: bold">123</p>
                <p> 123</p>
                
                <p>123</p>
              </div>
              
            </div>
          </div>
        </div>

        <div>
            <p id="info"></p>
        </div>
        
      </div>
      <div style="text-align: center">
        <button class="btn btn-primary btn-block add-driver">add driver</button>
      </div>
      
    </div>
    ---}}
  </div>

</div>

<div style="display: none">
  
    @csrf
    <input type="hidden" name="arrayone_first" id="arrayone_first">
    <input type="hidden" name="arrayone_second" id="arrayone_second">
    <input type="hidden" name="arraytwo_first" id="arraytwo_first">
    <input type="hidden" name="arraytwo_second" id="arraytwo_second">
    <input type="hidden" name="arraythree_first" id="arraythree_first">
    <input type="hidden" name="arraythree_second" id="arraythree_second">
    <input type="hidden" name="arrayfour_first" id="arrayfour_first">
    <input type="hidden" name="arrayfour_second" id="arrayfour_second">
    <input type="hidden" name="route_id" id="vehicle_id">
    
  
</div>
</form>
@endsection


@section('js')
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js" ></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&v=weekly&libraries=drawing" ></script>

<script defer>

$('#reload-map').on('click', InitMap);

var mapOptions;
var map;

var coordinates = []
let new_coordinates = []
let lastElement

function InitMap() {
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

    console.log('{{ $route_id }}');
    
}


function getCoordinates() {
  console.log(new_coordinates[0][0]);
}


$('#save-fence').on('click', function() {

  var arrayone_first = new_coordinates[0][0];
  var arrayone_second = new_coordinates[0][1];

  var arraytwo_first = new_coordinates[1][0];
  var arraytwo_second = new_coordinates[1][1];


  var arraythree_first = new_coordinates[2][0];
  var arraythree_second = new_coordinates[2][1];


  var arrayfour_first = new_coordinates[3][0];
  var arrayfour_second = new_coordinates[3][1];

  var route_id = '{{ $route_id }}';

  $('#arrayone_first').val(arrayone_first);
  $('#arrayone_second').val(arrayone_second);
  $('#arraytwo_first').val(arraytwo_first);
  $('#arraytwo_second').val(arraytwo_second);
  $('#arraythree_first').val(arraythree_first);
  $('#arraythree_second').val(arraythree_second);
  $('#arrayfour_first').val(arrayfour_first);
  $('#arrayfour_second').val(arrayfour_second);
  $('#arrayfour_second').val(arrayfour_second);
  $('#route_id').val(route_id);

  document.getElementById("myForm").submit();

  
});


InitMap()


</script>
@endsection