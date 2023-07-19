@extends('layouts.master2')
@push('plugin-styles')
<link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />

  <style>
    .my-nav {
      display: grid;
      grid-template-columns: 1fr 1fr;
    }

    .map-div {
        width: 100%;
    }

    #map {
      width: 100%;
      height: 100%;
    }
    .issue {
        color: #ff3366;
    }
    #iw-container {
	margin-bottom: 10px;
    }
    #iw-container .iw-title {
        font-size: 16px;
        font-weight: 400;
        padding: 10px;
        background-color: #ffc107;
        color: green;
        margin: 0;
        border-radius: 2px 2px 0 0;
    }
  </style>
@endpush
@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="alters"></div>
        </div>
    </div>
  
    <div class="row mt-2">
      <div class="col-lg-12 grid-margin stretch-card">
          <div class="card">
              <div class="card-body">
                <h6 class="card-title text-warning text-center">Institution Information {{Auth::user()->user_type}}</h6>
                <hr>
                <form action="{{ route('settings.store') }}" method="post" enctype="multipart/form-data" id="system-form">
                    <div class="row" >
                     
                      <div class="col-md-6">
                        
                        
                          @csrf
                      
                          <div class="row">
                              <div class="col-md-12 col-sm-12">
                                  <div class="mb-3">
                                      <label class="form-label ">Institution Name</label>
                                      <input type="text" name="company_name" class="form-control " id="company_name" placeholder="Name" required>
                                      <span class="issue" id="company_name_error"></span>
                                  </div>
                              </div>
                          </div>
                      
                          <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label " for="title">Institution Address</label>
                                    <input type="text" name="company_address" class="form-control" id="pac-input" placeholder="Address"  required>
                                    <span class="issue" id="pac-input-error"></span>
                                </div>
                            </div>
                          </div>
                      
                          <div class="row">
                              <div class="col-md-6 col-sm-12">
                                    <div class="mb-3">
                                        <label class="form-label " for="title">Institution Contact</label>
                                        <input type="text" name="company_pnum" id="company_pnum" class="form-control"  placeholder="+254700000000" required>
                                        <span class="issue" id="company_pnum_error"></span>
                                    </div>
                              </div>

                              <div class="form-group col-md-6 col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label " for="title">Institution Email</label>
                                    <input type="email" id="company_email" name="company_email" class="form-control"  placeholder="institution@mail.com" required>
                                    <span class="issue" id="company_email_error"></span>
                                
                                </div>
                            </div>
                          </div>
                      
                          <div class="row">
                              <div class="col-md-6 col-sm-12">
                                    <div class="mb-3">
                                      <label class="form-label " for="title">Time Zone</label>
                                      <input type="text" name="time_zone" class="form-control"  placeholder="Time zone" required id="time-zone">
                                      <span class="issue" id="time-zone-error"></span>
                                    </div>
                              </div>
                      
                              <div class="form-group col-md-6 col-sm-12">
                                  <div class="mb-3">
                                      <label class="form-label " for="title">Currency</label>
                                      <input type="text" name="currency" class="form-control" id="currency"  placeholder="Ksh" >
                                      <span class="issue" id="currency_error"></span>

                                  </div>
                              </div>
                          </div>
                      
                          <div class="row">
                              

                              <div class="col-md-12 col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label " for="title">Institution Logo</label>
                                    <input type="file" name="image" class="form-control" >
                                </div>
                            </div>
                          </div>
                      
                          
                          <input type="hidden" class="form-control lat" name="lat" id="lat">
                          <input type="hidden" class="form-control lng" name="lng" id="lng">
                          
                          
                        

                      </div>
                      <div class="col-md-6 map-div">
                        <div id="map">map</div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="text-center">
                        <button id="submit-btn" type="button" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> save</button>
                      </div>
                    </div>
                </form>
              </div>
          </div>
      </div>
  </div>
</div> 

@endsection

@push('plugin-scripts')
<script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
<script src="{{ asset('assets/js/dropify.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&libraries=places&v=weekly"></script>

<script defer>
        let latitude, longitude;
        if (! navigator.geolocation) {
            console.log()
        } else {
            navigator.geolocation.getCurrentPosition(succ, error);
        }

      function succ(position) {
          latitude = position.coords.latitude;
          longitude = position.coords.longitude;
          let timestamp = Date.now();

          getTimeZone(latitude, longitude, timestamp);
          initMap();

      }

      function error(e) {
            console.log(e);
      }

      function getTimeZone(lat, lng, timestamp) {
        
            $.ajax({
                method: "get",
                url: `https://maps.googleapis.com/maps/api/timezone/json?location=${lat}%2C${lng}&timestamp=1331161200&key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU`,
                cache: false,
                error: function(err) {
                    console.log(err);
                },

                success: function(res) {
                    console.log(res);
                    $('#time-zone').val(res.timeZoneId)
                }
                
            });    
      }
      function initMap() {
        var myLatlng;

        myLatlng = { lat: latitude - 0, lng: longitude - 0 };

        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 12,
            center: myLatlng,
        });
        // Create the initial InfoWindow.
        let infoWindow = new google.maps.InfoWindow();

        // Configure the click listener.
        map.addListener("click", (mapsMouseEvent) => {
            // Close the current InfoWindow.
            infoWindow.close();
            // Create a new InfoWindow.
            infoWindow = new google.maps.InfoWindow({
                position: mapsMouseEvent.latLng,
            });
            let contentInfo = `
                <div id="iw-container">
                    <div class="iw-title">School location marked</div>
                </div>
            `;
            infoWindow.setContent(contentInfo);

            var j = mapsMouseEvent.latLng.toJSON();

            $('.lat').val(j.lat);
            $('.lng').val(j.lng);

            infoWindow.open(map);
        });


        const input = document.getElementById("pac-input");
        const searchBox = new google.maps.places.SearchBox(input);


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
            /*
            markers.push(
                new google.maps.Marker({
                map,
                icon,
                title: place.name,
                position: place.geometry.location,
                })
            );
            */
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

    window.initMap = initMap;


    $(function() {
        $('#submit-btn').on('click',(e) => {

            if(!$('#company_name').val()){
                $('#company_name_error').text('field required');
                e.preventDefault();
                $('#company_name').focus();
                return;
            } else {
                $('#company_name_error').text('');
            }

           

            if(!$('#pac-input').val()){
                $('#pac-input-error').text('field required');
                e.preventDefault();
                $('#pac-input').focus();
                return;
            } else {
                $('#pac-input-error').text('');
            }

            if(!$('#company_pnum').val()){
                $('#company_pnum_error').text('field required');
                e.preventDefault();
                $('#company_pnum').focus();
                return;
            } else {
                $('#company_pnum_error').text('');
            }

            if(!$('#company_email').val()){
                $('#company_email_error').text('field required');
                e.preventDefault();
                $('#company_email').focus();
                return;
            } else {
                $('#company_email_error').text('');
            }

            if(!$('#time-zone').val()){
                $('#time-zone-error').text('field required');
                e.preventDefault();
                $('#time-zone').focus();
                return;
            } else {
                $('#time-zone-error').text('');
            }

            if(!$('#currency').val()){
                $('#currency_error').text('field required');
                e.preventDefault();
                $('#currency').focus();
                return;
            } else {
                $('#currency_error').text('');
            }

            if (!$('#lat').val()) {
                let template = `
                <div class="alert alert-danger" role="alert" id="danger">
                    <p>Please click on the map to pinpoint exact institution location</p>
                </div>
                `;
                $('#alters').children().remove();
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('#alters').append(template);
                return;
            }

            if (!$('#lng').val()) {
                let template = `
                <div class="alert alert-danger" role="alert" id="danger">
                    <p>Please click on the map to pinpoint institution location</p>
                </div>
                `;
                $('#alters').children().remove();
                $('#alters').append(template);
                $("html, body").animate({ scrollTop: 0 }, "slow");
                return;
            }

            $('#system-form').submit();

        });
    });
</script>
@endpush