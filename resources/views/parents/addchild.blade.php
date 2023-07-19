@extends('layouts.app')
@section('css')

<style>
   .map-wrapper {
        height: 60vh;
        margin-bottom: 30px;
   }

   #map {
    height: 100%;
   }

   #pac-input {
    background-color: #fff;
    font-family: Roboto;
    font-size: 15px;
    font-weight: 300;
    margin-left: 12px;
    padding: 0 11px 0 13px;
    text-overflow: ellipsis;
    width: 400px;
    }

    #pac-input:focus {
    border-color: #4d90fe;
    }
</style>
@endsection
@section('content')

<div class="page-wrapper">

    <div class="page-header card">
        <div class="card-block">
            <h5 class="m-b-10">Add Student</h5>
            <p class="text-muted m-b-10">Add Student by filling the form</p>
            <ul class="breadcrumb-title b-t-default p-t-10">
                <li class="breadcrumb-item">
                    <a href="index.html"> <i class="fa fa-home"></i> </a>
                </li>
                <li class="breadcrumb-item"><a href="#!">Student</a></li>
                <li class="breadcrumb-item"><a href="#!">Add</a></li>
            </ul>
        </div>
    </div>


    <div class="page-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="card-block">
                    <form action="{{ route('addchild') }}"  method="POST" id="studentForm">
                        @csrf




                        <h5>Student Details</h5>

                        <br>

                        <input type="hidden" name="parent_id" value="{{ $parent->id }}">

                        <div class="form-row">
                          <div class="form-group col-md-6">
                            <label for="title">Student First Name</label>
                            <input type="text" name="fname" class="form-control" id="title" placeholder="Student First Name" required>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="platenum">Student Last Name</label>
                            <input type="text" name="lname" class="form-control" id="desc" placeholder="Student Last Name" required>
                          </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="platenum">Grade</label>
                                
                                <select id="inputState" class="form-control" name="grade" required>'
                                    <option selected>Choose Grade...</option>
                                    <option value="1">Grade 1</option>
                                    <option value="2">Grade 2</option>
                                    <option value="3">Grade 3</option>
                                    <option value="4">Grade 4</option>
                                    <option value="5">Grade 5</option>
                                    <option value="6">Grade 6</option>
                                    <option value="7">Grade 7</option>
                                    <option value="8">Grade 8</option>
                                    </select>
                            </div>

                            
                            <div class="form-group col-md-6">
                                <label for="platenum">Admission Number</label>
                                <input type="text" name="add_num" class="form-control" id="addNum" placeholder="Student Admission Number" required>
                            </div>

                           
                                
                            
                        </div>

                        <div class="form-row">
                            

                            <div class="form-group col-md-6">
                                <label for="inputState">Select Vehicle</label>
                                <select id="inputState" class="form-control vehicle_id" name="vehicle_id" onchange="getTrip(this.value)" required>
                                  <option selected>Choose...</option>
                                  @foreach ($vehicles as $vehicle)
                                  <option value="{{$vehicle->id}}">{{ $vehicle->title }} - ({{ $vehicle->plate_num }})</option>
                                  @endforeach
                                  
                                </select>
                            </div>


                            <div class="form-group col-md-6" id="trip_select">

                            </div>
                        </div>


                        <input type="hidden" class="form-control lat" name="lat">
                        <input type="hidden" class="form-control lng" name="lng">
                        


                        <br>

                        <div id="extraInputs">

                        </div>


                        <div class="map-wrapper">
                            <input
                            id="pac-input"
                            class="controls"
                            type="text"
                            placeholder="Search Box"
                            />
                            <div id="map"></div>
                        </div>

                        <div style="width: 100vw">
                           


                        
                            <button type="submit" class="btn" style="background:#0071f3; color: #fff" style="float: right">Submit</button>
                        </div>

                        
                      </form>
                </div>
            </div>
        </div>
    </div>

</div>
    

@endsection


@section('js')
<script
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&libraries=places&v=weekly"

></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script defer>
       $(window).keydown(function(event){
            if(event.keyCode == 13) {
            event.preventDefault();
            return false;
            }
        });
        $('#add').on('click', function() {
            var form = '<div class="children"><div class="form-row"><div class="form-group col-md-6"><label for="title">Student First Name</label><input type="text" required name="fname[]" class="form-control" id="title" placeholder="Student First Name"></div>'
            + '<div class="form-group col-md-6"><label for="platenum">Student Last Name</label><input type="text" name="lname[]" class="form-control" id="desc" required placeholder="Student Last Name"></div>' 
            +'</div>'
            +'<div class="form-row">'
            +'<div class="form-group col-md-6">'
            +'<label for="grade">Grade</label>'
            +'<select id="inputState" class="form-control" name="vehicle_id[]" required>'
            +'<option selected>Choose Grade...</option>'
            +'<option value="1">Grade 1</option>'
            +'<option value="2">Grade 2</option>'
            +'<option value="3">Grade 3</option>'
            +'<option value="4">Grade 4</option>'
            +'<option value="5">Grade 5</option>'
            +'<option value="6">Grade 6</option>'
            +'<option value="7">Grade 7</option>'
            +'<option value="8">Grade 8</option>'
            +'</select>'
            +'</div>'
            +'<div class="form-group col-md-6">'
            +'<label for="inputState">Select Vehicle</label>'
            +'<select id="inputState" class="form-control" name="vehicle_id[]">'
            +'<option selected>Choose...</option>'
            
            + '@foreach ($vehicles as $vehicle)'
            + '<option value="{{$vehicle->id}}">{{ $vehicle->title }} - ({{ $vehicle->plate_num }})</option>'
            +'@endforeach'
            +'</select>'
            + '</div>'
            +'<div class="form-group col-md-6"><label for="platenum">Admission Number</label><input type="text" name="add_num[]" class="form-control" id="addNum" placeholder="Student Admission Number"></div>'
            + '</div></div>'
            + '<br>';

            $('#extraInputs').append(form);
        });

        $('#remove').on('click', function () {
            $('#extraInputs .children:last').remove();
        });


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
                    title: '{{ Session::get("erros") }}',
                    showConfirmButton: false,
                    timer: 2500
                });
        }



            //get trip
            function getTrip(id) {
                
                $.ajax({
                    type: "GET",
                    url: '/addstd/vehicle/trips/' + id,
                    processData: false,
                    contentType: false,
                    cache: false,
                    error: function (err) {
                        console.log(err)
                    },
                    success: function (response) {
                        console.log(response);
                        $('#myedittrips').remove();

                        $('#myeditselect').remove();
                        $('#trip_select').append(response);

                    }
                });
            }
            



        
            function initMap() {
                const myLatlng = { lat: -1.4386634, lng: 36.9952405 };
                const map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 8,
                    center: myLatlng,
                });
                // Create the initial InfoWindow.
                let infoWindow = new google.maps.InfoWindow({
                    content: "Click the map to get Lat/Lng!",
                    position: myLatlng,
                });

                infoWindow.open(map);
                // Configure the click listener.
                map.addListener("click", (mapsMouseEvent) => {
                    // Close the current InfoWindow.
                    infoWindow.close();
                    // Create a new InfoWindow.
                    infoWindow = new google.maps.InfoWindow({
                        position: mapsMouseEvent.latLng,
                    });
                    infoWindow.setContent(
                        JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2)
                        
                    );

                    var jjj = mapsMouseEvent.latLng.toJSON();

                    $('.lat').val(jjj.lat);
                    $('.lng').val(jjj.lng);

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
@endsection