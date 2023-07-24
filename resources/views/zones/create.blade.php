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

    .issue {
        color: #ff3366;
    }
  </style>
@endpush
@section('content')


<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('zones.index')}}">Zone</a></li>
      <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('zones.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
    </div>
</nav>

<div id="alters"></div>
    
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

<form action="{{ route('zones.store') }}" method="post" id="pathForm">
    @csrf 
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Route</h4>
                <div class="row mb-3">
                    <div class="col-md-6">
                      <label class="form-label" for="title">Zone Title</label>
                      <input type="text" name="title" class="form-control" id="title" placeholder="Zone title" required>
                        
                      <span class="issue" id="title-error"></span>
                    </div>
                
                    <div class="col-md-6">
                      <label class="form-label" for="description">Zone Description</label>
                      <input type="text" name="description" class="form-control" id="description" placeholder="Zone description" required>
                      <span class="issue" id="desc-error"></span>
                    
                    </div>
                    
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="platenum">Two-Way Price {{ $settings->currency ?? "Ksh" }}</label>
                        <input type="number" name="price" class="form-control" id="two-way" placeholder="0" required>
                        <span class="issue" id="two-way-error"></span>
                    
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="oneway_price">One-Way Price {{ $settings->currency ?? "Ksh" }}</label>
                        <input type="number" name="oneway_price" class="form-control" id="oneway_price" placeholder="0" required>
                        <span class="issue" id="one-way-error"></span>
                    
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-md-12 map-div mb-3 mt-3">
                        
                        <label class="form-label mb-2" for="">Zone GeoFence</label>
                        <p class="form-label mb-3 text-muted" for="">Click on created geo fences to get more information</p>
                        <span class="issue" id="map-error"></span>
                        <br>
                        <div id="map"></div>
                    </div>
                </div>


                <div class="" id="append-inputs" style="display: none">

                </div>
                <div class="mt-5 text-center">
                    <button class="btn btn-success mt-5" type="submit" id="my-submit"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Save Zone</button>
                </div>
            </div>
        </div>
    </div>
</div>
            
   
</form>

@endsection

@push('plugin-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&callback=initMap&libraries=places,geometry,drawing&v=weekly"></script>
@endpush

@push('custom-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
<script defer>


var mapOptions;
var map;

var coordinates = []
let new_coordinates = []
let lastElement

function getFences() {
    $.ajax({
        type: 'GET',
        url: '/zone-all-fences',
        processData: false,
        cache:false,
        contentType: false,
        error: function (err) {
            console.log(err);
        },
        success: function (response) {
            console.log(response);
            window.initMap = initMap;

            for (let i = 0; i < response.length; i++) { //outer array
                x = 1;
                var final_arr = [];
                for (let t = 0; t < response[i].coordinates.length; t+=2) { //inner array
                    
                    const triangleCoords =  { 
                        lat: response[i].coordinates[t].corrdinates - 0, 
                        lng: response[i].coordinates[x].corrdinates - 0 
                    };
                    final_arr.push(triangleCoords);
                    x+=2;
                }
                
                const bermudaTriangle = new google.maps.Polygon({
                    paths: final_arr,
                    strokeColor: "#01ff4f",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "#98bee4",
                    fillOpacity: 0.5,
                });

                
                google.maps.event.addListener(bermudaTriangle, 'click', function (e) {
                    let template =  `
                        <div id="content">
                            <h1 id="firstHeading" class="card-title mb-3">${response[i].zone.name}</h1>
                            <div id="bodyContent">
                                <p>Zone price ${response[i].zone.price} {{$settings->currency}}</p>
                            </div>
                        </div>
                    `
                        const infoWindow = new google.maps.InfoWindow();
                        infoWindow.setContent(template);
                        var latLng = e.latLng;
                        infoWindow.setPosition(latLng);
                        infoWindow.open(map);
                });

                bermudaTriangle.setMap(map);
            
            }
        }
    })
}
getFences();

function initMap() {
    var lats = '{{ $settings->lat }}' - 0;
    var lngs = '{{ $settings->lng }}' - 0;
    var location = new google.maps.LatLng(lats, lngs)
    mapOptions = {
        zoom: 14,
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

        $('#append-inputs').children().remove();
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

  $('#container').children().remove();

  for (let t = 0; t < new_coordinates.length; t++) {
    var input = document.createElement("input");
    input.type = "text";
    input.setAttribute('value', new_coordinates[t][0]);
    input.setAttribute('name', 'arrone[]');
    input.setAttribute('class', 'arrone');
    container.appendChild(input);

    var inputTwo = document.createElement("input");
    inputTwo.type = "text";
    inputTwo.setAttribute('name', 'arrtwo[]');
    inputTwo.setAttribute('value', new_coordinates[t][1]);
    input.setAttribute('class', 'arrtwo');

    container.appendChild(inputTwo);
  }

}

$('#my-submit').on('click',(e) => {

    if (!$('#title').val()) {
        $('#title-error').text('field required');
        e.preventDefault();
        $('html, body').animate({
                scrollTop: "0px"
            }, 800);
            return;
    }else {
        $('#title-error').text('');
    }


    if (!$('#description').val()) {
        $('#desc-error').text('field required');
        e.preventDefault();
        $('html, body').animate({
                scrollTop: "0px"
            }, 800);
            return;
    }else {
        $('#desc-error').text('');
    }

    if (!$('#two-way').val()) {
        $('#two-way-error').text('field required');
        e.preventDefault();
        $('html, body').animate({
                scrollTop: "0px"
            }, 800);
            return;
    }else {
        $('#two-way-error').text('');
    }

    if (!$('#oneway_price').val()) {
        $('#one-way-error').text('field required');
        e.preventDefault();
        $('html, body').animate({
                scrollTop: "0px"
            }, 800);
            return;
    }else {
        $('#one-way-error').text('');
    }

    $('.arrone').each((i,e)=> {
        if (!$(e).val()) {
            $('#alters').children().remove();
            let tem = ` 
            <div class="alert alert-danger" role="alert" id="danger">
                Create zone geo fence.
            </div>
            `;
            $('#alters').append(tem);
            e.preventDefault();
            $('html, body').animate({
                    scrollTop: "0px"
                }, 800);
            return;
        } else {
            $('#map-error').text('');
        }
    })
   

    if ($('#append-inputs').children().length <= 0) {
        $('#alters').children().remove();
        let tem = ` 
        <div class="alert alert-danger" role="alert" id="danger">
            Create zone geo fence.
        </div>
        `;
        $('#alters').append(tem);
        e.preventDefault();
        $('html, body').animate({
                scrollTop: "0px"
            }, 800);
        return;
    } else {
        $('#map-error').text('');
    }

    $('#pathForm').submit();
})
    
</script>
@endpush

