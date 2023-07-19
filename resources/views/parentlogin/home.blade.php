@extends('layouts.app')

@section('css')
<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css">
<style>
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

    .my-card {
        height: 100vh;
        width: 100%;
    }
    .card-block {
        height: 100%;
        width: 100%;
    }
    #map {
        height: 100%;
        width: 100%;
    }

    .panel {
      margin-bottom: 19px;
      background-color: #fff;
      border: 1px solid transparent;
      border-radius: 4px;
      -webkit-box-shadow:  0 2px 5px 0 rgba(0,0,0,.26);
      box-shadow: 0 2px 5px 0 rgba(0,0,0,.26);
    }

    .text-thin {
      font-weight: 100!important;
    }

    .thumb24 {
  width: 24px!important;
  height: 24px!important;
  line-height: 24px!important;
}

.label-marker {
        position: absolute;
        top: 0;
        left: -40px;
        background: #FEDB00;
        padding: 3px;
        border-radius: 0.125rem;
      }

      .panel-container {
  background-color: #12192c;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
  border-radius: 15px;
  font-size: 90%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  padding: 30px;
  max-width: 400px;
  color:#4e54c8;
  margin:0 auto 2% auto;
}

.panel-container strong {
  line-height: 20px;
}

.ratings-container {
  display: flex;
  margin: 20px 0;
}

.rating {
  flex: 1;
  cursor: pointer;
  padding: 20px;
  margin: 10px 5px;
}

.rating:hover,
.rating.active {
  border-radius: 4px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.rating img {
  width: 40px;
}

.rating small {
  color: #fff;
  display: inline-block;
  margin: 10px 0 0;
}

.rating:hover small,
.rating.active small {
  color: #8f94fb;
}

.btn {
  background-color: #8f94fb;
  color: #000;
  border: 0;
  border-radius: 4px;
  padding: 12px 30px;
  cursor: pointer;
}

.btn:focus {
  outline: 0;
}

.btn:active {
  transform: scale(0.98);
}

.fa-heart {
  color: red;
  font-size: 30px;
  margin-bottom: 10px;
}


.credit a{
    text-decoration: none;
    color: #fff;
  }

  .credit {
      text-align: center;
  }

  .review-input {
    margin-bottom: 10px;
  }


    @media only screen and (max-width: 500px) and (orientation: portrait) {
        .my-card .card-block {
          padding: 0 !important;
        }
        #map {
          height: 100%;
          width: 100%;
        }

        .my-row {
          display: none;
        }

    }

    
   
</style>
@endsection

@section('content')


<div class="page-wrapper">
    

    <div class="page-body">
        <div class="row">
            <div class="col-md-6 col-xl-4">
                <div class="card  order-card" style="background: #16a34a">
                    <div class="card-block">
                        <h6 class="m-b-20">Unpaid Invoices Count</h6>
                        <h2 class="text-right"><i class="fa-solid fa-file-invoice f-left"></i><span id="unpaid_invoice">0</span></h2>
                        <p class="m-b-0">Unpaid Invoices Total<span class="f-right" id="unpaid_total">0</span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4 my-row" >
                <div class="card order-card" style="background: #16a34a">
                    <div class="card-block">
                        <h6 class="m-b-20">Number Of children</h6>
                        <h2 class="text-right"><i class="fa-solid fa-children f-left"></i><span id="num_of_children">0</span></h2>
                        <p style="visibility: hidden" class="m-b-0">This Month<span class="f-right">213</span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4 my-row" style="visibility: hidden">
                <div class="card bg-c-blue order-card">
                    <div class="card-block">
                        <h6 class="m-b-20">Revenue</h6>
                        <h2 class="text-right"><i class="ti-reload f-left"></i><span>$42,562</span></h2>
                        <p class="m-b-0">This Month<span class="f-right">$5,032</span></p>
                    </div>
                </div>
            </div>

            <div class="col-md-12 pb-4">
              <h5>Estimated arrival time : <span class="pl-2" style="color: #0071f3" id="estimatetime"></span></h5>
            </div>

            <div class="col-lg-12 col-md-12">
                
                <div class="card my-card">
                  <p style="position:relative; top: 10px; left: 20px">School Bus Current Location</p>
                    <div class="card-block">
                        

                        <div id="map"></div>
                    </div>
                </div>
            </div>
            

            <div class="col-md-12 col-lg-12 col-sm-12">
              <div class="card tabs-card">
                
                <div class="tab-content card-block">
                    <div class="tab-pane active" id="home3" role="tabpanel">
                        <p>Absent Days</p>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" style="border: 1px solid gray;" id="vehTable">
                                <thead style="background-color: #0071f3; color: #fff">
                                    <tr>
                                       
                                        <th>Name</th>
                                        <th>Grade</th>
                                        <th>Day Absent</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $student)
                                    
                                      {{--
                                      @php
                                          $attendances = App\Attendance::where('student_id', '=', $student->id)->where('present', 'LIKE', 'false')->orderBy('created_at', 'DESC')->get();
                                          
                                      @endphp
                                      ---}}
                                      @php
                                          $flags = App\Models\FlagOff::where('student_id', '=', $student->id)
                                                ->get();   
                                      @endphp 
                                      
                                      @foreach ($flags as $flag)
                                          
                                          
                                          <tr>
                                            <td>{{$flag->student->first_name}}</td>
                                            <td>{{$flag->student->last_name}}</td>
                                            
                                            <td>{{ $flag->date }}</td>
                                            <td>{{ $flag->reason ?? ''}}</td>
                                            
                                          </tr>
                                           
                                        
                                      @endforeach
                                    
                                   
                                    @endforeach
                                </tbody>
                               
                                
                            </table>
                        </div>
                        <div class="text-center">
                            
                        </div>
                    </div>
                   
                    
                </div>
            </div>
            </div>

            

            
            
        </div>

        <div class="row">
        

          <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="panel panel-default">
              <div class="panel-body bg-primary p-2">
                 <h2 class="text-thin mt">Contact Us:</h2>
                 <div class="clearfix">
                    <div class="pull-right">
                       <ul>
                          <li>{{ $settings->company_pnum ?? '' }}</li>
                          <li>{{ $settings->company_email ?? '' }}</li>
                       </ul>
                    </div>
                 </div>
              </div>
            </div>
          
          </div>

          <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div id="panel" class="panel-container">
              <strong>How satisfied are you with our <br /> transport service?</strong>
              <div class="ratings-container">
                <div class="rating">
                  <img src="https://cdn-icons-png.flaticon.com/128/4486/4486655.png" alt="">
                  <small>Unhappy</small>
                </div>
        
                <div class="rating">
                  <img src="https://cdn-icons-png.flaticon.com/128/4486/4486723.png" alt=""/>
                  <small>Neutral</small>
                </div>
        
                <div class="rating active">
                  <img src="https://cdn-icons-png.flaticon.com/128/4486/4486630.png" alt=""/>
                  <small>Satisfied</small>
                </div>
              </div>
              <div class="review-input">
                <p class="text-sm">Give us a feedback</p>
                <label for="">Select Child</label>
                <select  id="student_id" class="form-control">
                  @foreach ($students as $student)
                      <option value="{{$student->id}}">{{$student->first_name}} {{$student->last_name}}</option>
                  @endforeach
                </select>
                <br>
                <label for="">Reason</label>
                <textarea  id="feedback-input" cols="30" rows="5" class="form-control"></textarea>
              </div>
              <button class="btn" id="send">Send Review</button>
            </div>

          </div>


        </div>
    </div>

</div>



<div style="visibility: hidden">
  <i class="fa fa-location-arrow" aria-hidden="true" id="loc"></i>

 
</div>


@endsection


@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js" ></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&v=weekly" ></script>
<script defer>
// Note: This example requires that you consent to location sharing when
// prompted by your browser. If you see the error "The Geolocation service
// failed.", it means you probably did not give permission for the browser to
// locate you.
$(document).ready( function () {
            $('#vehTable').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });
});
function fireswal() {
  (async () => {

    const { value: text } = await Swal.fire({
      input: 'text',
      inputLabel: 'Reason',
      inputPlaceholder: 'Enter reason here...',
      inputAttributes: {
        'aria-label': 'Type your message here'
      },
      showCancelButton: true
    })

    if (text) {
      Swal.fire(text)
    }

  })()
}

let map, infoWindow;



var parent_id = '{{ Auth::user()->id }}' - 0;

getlatlong();





function getlatlong() {
  var data = new FormData;

  data.append('_token','{{csrf_token()}}');
  data.append('pid', parent_id);

  $.ajax({
    type: "POST",
    url: "{{ route('getlatlong') }}",
    processData: false,
    contentType: false,
    cache: false,
    data: data,
    error: function (err) {
        console.log(err)
    },
    success: function (response) {
      //console.log(response);

      let locations = [];

      let stdlocations = [];

      //console.log(locations);

      let labels = [];

      for (let i = 0; i < response.lat.length; i++) {
        
        locations.push(
            { lat: response.lat[i] - 0, lng: response.lng[i] - 0 }
        );
        
        labels.push(response.label[i]);
      }
      

      for (let i = 0; i < response.stdlat.length; i++) {
        
        stdlocations.push(
            { lat: response.stdlat[i] - 0, lng: response.stdlng[i] - 0 }
        );
        
        
      }
      

      console.log(stdlocations);
      
      var lats = '{{ $settings->lat }}' - 0;
      var lngs = '{{ $settings->lng }}' - 0;

      map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: lats, lng: lngs },
        zoom: 10,
      });
      infoWindow = new google.maps.InfoWindow({
        content: "",
        disableAutoPan: true,
      });


      const markers = locations.map((position, i) => {
      
        const label = labels[i];

        const image =
    "https://cdn-icons-png.flaticon.com/512/3448/3448316.png";
        
        const marker = new google.maps.Marker({
          position,
          label: {text: label, color: "#1e293b", fontSize: "15px", className: "label-marker"},
          icon: {
            url: image,
            scaledSize: new google.maps.Size(50, 50), // scaled size
            
          }
        });

        // markers can only be keyboard focusable when they have click listeners
        // open info window when marker is clicked
        marker.addListener("click", () => {
          infoWindow.setContent(label);
          infoWindow.open(map, marker);
          console.log(position);
          showMore(position);
          
        });

        return marker;
      });


      new markerClusterer.MarkerClusterer({ markers, map });

      //distance matrix
      const geocoder = new google.maps.Geocoder();
      const service = new google.maps.DistanceMatrixService();


      const origin1 = { lat: locations[0].lat, lng: locations[0].lng };
      const destinationA = { lat: stdlocations[0].lat, lng: stdlocations[0].lng };

      const request = {
      origins: [origin1],
      destinations: [destinationA],
      travelMode: google.maps.TravelMode.DRIVING,
      unitSystem: google.maps.UnitSystem.METRIC,
      avoidHighways: false,
      avoidTolls: false,
      
    };

    service.getDistanceMatrix(request).then((response) => {
      //console.log(response.rows[0].elements[0].duration.text);

      $('#estimatetime').text(response.rows[0].elements[0].duration.text);
    })


      
      window.initMap = map;

    }
  });
}


setInterval(getlatlong, 30000);

$(document).ready( function () {
  $.ajax({
    type: "GET",
    url: "{{ route('home_data') }}",
    processData: false,
    contentType: false,
    cache: false,
    error: function(error) {
      console.log(error);
    },
    success: function(response) {
      console.log(response);

      $('#unpaid_invoice').text(response.unpaidinvoice);
      $('#num_of_children').text(response.numChild);
      $('#unpaid_total').text(response.total_unpaid);
    } 
  });
});


const ratings = document.querySelectorAll('.rating')
const ratingsContainer = document.querySelector('.ratings-container')
const sendBtn = document.querySelector('#send')
const panel = document.querySelector('#panel')
let selectedRating = 'Satisfied'

ratingsContainer.addEventListener('click', (e) => {
    if(e.target.parentNode.classList.contains('rating')) {
        removeActive()
        e.target.parentNode.classList.add('active')
        selectedRating = e.target.nextElementSibling.innerHTML
    }
    if(e.target.classList.contains('rating')) {
        removeActive()
        e.target.classList.add('active')
        selectedRating = e.target.nextElementSibling.innerHTML
    }

})

sendBtn.addEventListener('click', (e) => {
    var feedback = document.getElementById('feedback-input').value;
    var std = document.getElementById('student_id');
    var student_id = std.options[std.selectedIndex].value
    var datas = new FormData;

    datas.append('_token','{{csrf_token()}}');
    datas.append('user_id', '{{Auth::user()->id}}');
    datas.append('rating', selectedRating);
    datas.append('feedback', feedback);
    datas.append('student_id', student_id);

    $.ajax({
        type: "POST",
        url: "{{route('reviews.store')}}",
        processData: false,
        contentType: false,
        cache: false,
        data: datas,
                          
        error: function(err){
          console.log(err);
            Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: 'Sytem error please try again',
                showConfirmButton: false,
                timer: 1500
            });
        },
        success: function (message) {
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: `${message}`,
                showConfirmButton: false,
                timer: 1500
            });
        }
    });

    panel.innerHTML = `
        
        Thank You!
        
        Feedback 
        We'll use your feedback to improve our service
    `
})

function removeActive() {
    for(let i = 0; i < ratings.length; i++) {
        ratings[i].classList.remove('active')
    }
}



</script>
@endsection