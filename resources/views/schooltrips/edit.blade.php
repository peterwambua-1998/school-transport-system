@extends('layouts.app')
@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.css') }}" rel="stylesheet" />
  <style>
    .my-nav {
      display: grid;
      grid-template-columns: 1fr 1fr;
    }
    .issue {
        color: #ff3366;
    }
  </style>
@endpush

@section('content')
<form action="{{route('schooltrips.update', $schooltrip->id)}}"  method="POST" id="trip-form">
    <nav class="page-breadcrumb my-nav" >
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{route('schooltrips.index')}}">School Trip</a></li>
          <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
      
        <div style="display: flex; flex-direction: row-reverse;">
          <a href="{{route('schooltrips.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
        </div>
    </nav>

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
                    <h4 class="card-title">Edit School Trip</h4>
                    <hr>
                    @csrf 
                    @method('PATCH')
                        <div class="row">
                          <div class="mb-3 col-md-6">
                            <label class="form-label" for="title">Trip Name</label>
                            <input type="text" name="trip_name" class="form-control" id="trip_name" placeholder="Trip Name" required value="{{ $schooltrip->trip_name }}">
                            <p class="text-sm text-danger trip-err"></p>
                          </div>
                          <div class="mb-3 col-md-6">
                            <label class="form-label" for="inputState">Select Vehicle</label>
                            <select id="vehicle_id" class="js-example-basic-multiple form-select vehicle_id" multiple="multiple" name="vehicle_id[]" required>
                                  @foreach ($vehicles as $vehicle)
                                  @php
                                      $vehTrip = DB::table('schooltrip_vehicle')->where('schooltrip_id','=', $schooltrip->id)
                                      ->where('vehicle_id','=',$vehicle->id)->first();
                                  @endphp
                                  <option @if($vehTrip) selected @endif value="{{$vehicle->id}}">{{ $vehicle->title }} - ({{ $vehicle->plate_num }})</option>
                                  @endforeach
                                  
                            </select>
                          </div>
                         
                          
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="inputState">Select Teacher In Charge</label>
                                <select id="teacher_id" class="js-example-basic form-select vehicle_id" multiple="multiple" name="teacher_id[]"  required>
                                    @foreach ($teachers as $teacher)
                                    @php
                                        $teacherTrip = DB::table('schooltrip_teacher')->where('schooltrip_id','=', $schooltrip->id)
                                        ->where('teacher_id','=',$teacher->id)->first();
                                    @endphp
                                    <option @if($teacherTrip) selected @endif value="{{$teacher->id}}">{{ $teacher->name }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="platenum">Trip Date</label>
                                <input type="date" name="trip_date" class="form-control" id="trip_date" required value="{{ $schooltrip->trip_date }}">
                                <p class="text-sm text-danger trip-date-err"></p>
                            
                              </div>
                        </div>


                        <div class="row">
                            
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="platenum">Departure Time</label>
                                <input type="time" name="depature_time" class="form-control" id="depature_time" required value="{{ $schooltrip->departure_time }}">
                                <p class="text-sm text-danger trip-de-time"></p>
                              </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="platenum">Return Time</label>
                                <input type="time" name="return_time" class="form-control" id="return_time" required value="{{ $schooltrip->return_time }}">
                                <p class="text-sm text-danger return-err"></p>
                            
                              </div>
                        </div>

                        <div class="row">
                           
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="platenum">Type</label>
                                <select id="paid_unpaid" class="form-select vehicle_id" name="paid_unpaid" required>
                                    <option>select...</option>
                                    <option @if ($schooltrip->status == 'paid')
                                      selected @endif value="paid">Paid Trip</option>
                                    <option @if ($schooltrip->status == 'unpaid')
                                      selected @endif value="unpaid">Unpaid Trip</option>
                              </select>
                              <p class="text-sm text-danger trip-type"></p>

                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="platenum">{{ucfirst($tr->plural) ?? 'Grades'}}</label>
                                <select id="grade" multiple="multiple" class="js-example-basic-multiple form-select vehicle_id" name="grade[]" required>
                                    <option>select...</option>
                                    @foreach ($grades as $grade)
                                        <?php $check = App\Models\SchoolTripGrade::where('grade_id','=',$grade->id)->where('schooltrip_id','=',$schooltrip->id)->first(); ?>
                                        <option @if ($check) selected @endif value="{{$grade->id}}">{{$grade->name}}</option>
                                    @endforeach
                                </select>
                                <p class="text-sm text-danger return-grade"></p>
                            </div>
                            
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Destination</label>
                                <input name="destination" id="tags" value="{{$dests}}" />
                                <p class="text-sm text-danger dest-err"></p>
                            
                              </div>
                            <div class="mb-3 col-md-6" id="price">
                                <label class="form-label" for="platenum">Trip Price ({{ $settings->currency ?? 'USD' }})</label>
                                <input type="number" id="pricessss" name="price" class="form-control"  placeholder="price" required value="{{ $schooltrip->price }}" >
                                <p class="text-sm text-danger trip-price"></p>
                            </div>
                        </div>



                        <div class="text-center">
                          <button type="button" id="save-btn" class="btn btn-success"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Submit</button>                        
                        </div>
                        
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush
@push('custom-scripts')
<script src="{{ asset('assets/js/tags-input.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script defer>
        $(document).ready( function () {
          let status = $('#paid_unpaid').find(":selected").val();
          if (status == "paid") {
            $('#price').show();
          } else {
            $('#price').hide();
          }

          // Get the current year
          var currentYear = new Date().getFullYear();

          // Set the minimum attribute of the date input
          var dateInput = document.getElementById('trip_date');
          dateInput.min = currentYear + "-01-01";


          $('#paid_unpaid').on('change', function() {
            var value = $(this).find(":selected").val();

            if (value == 'unpaid') {
                $('#price').hide();
            }

            if (value == 'paid') {
                $('#price').show();
            }
          });

          $('#save-btn').on('click', function() {
                
              if (!$('#trip_name').val()) {
                  $('.trip-err').text('field is required');
                  $('html, body').animate({
                      scrollTop: '0px'
                  }, 800);
                  $('#trip_name').focus();
                  return;
              } else {
                  $('.trip-err').text('');
              }


              if (!$('#trip_date').val()) {
                  $('.trip-date-err').text('field is required');
                  $('html, body').animate({
                      scrollTop: '0px'
                  }, 800);
                  $('#trip_date').focus();

                  return;
              }else {
                  $('.trip-date-err').text('');
              }

              if (!$('#depature_time').val()) {
                  $('.trip-de-time').text('field is required');
                  
                  return;
              }else {
                  $('.trip-de-time').text('');
              }

              if (!$('#return_time').val()) {
                  $('.return-err').text('field is required');
                  $('#return_time').focus();
                  return;
              }else {
                  $('.return-err').text('');
              }

              if ($('#paid_unpaid').find(':selected').val() == 'select...') {
                  $('.trip-type').text('field is required');
                  $('#paid_unpaid').focus();

                  return;
              }else {
                  $('.trip-type').text('');
              }

              if (!$('#grade').find(':selected').val()) {
                    $('.return-grade').text('This field is required');
                    $('#grade').focus();
                    return;
                }else {
                    $('.return-grade').text('');
                }

              

              if (!$('#tags').val()) {
                $('.dest-err').text('field is required');
                $('#tags').focus();
                return;
              } else {
                $('.dest-err').text('');
                
              }


              if ($('#paid_unpaid').find(':selected').val() !== 'select...') {
                  if ($('#paid_unpaid').find(':selected').val() == 'paid') {
                      
                      if ($('#pricessss').val() <= 0) {
                          console.log($('#pricessss').val());
                          $('.trip-price').text('field is required');
                          $('#pricessss').focus();
                          return;
                      } else {
                          $('.trip-price').text('');
                      }
                      
                  }
              }


              $('#trip-form').submit();
          })

        });
    </script>
@endpush
