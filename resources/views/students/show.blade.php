@extends('layouts.app')

@push('plugin-styles')
    <script src="{{ asset('js/intlTelInput.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}"></script>
    <link href="{{ asset('css/intlTelInput.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />

    <style>
        .my-nav {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
    </style>
@endpush

@section('content')

<nav class="page-breadcrumb my-nav">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('students.index')}}">Student</a></li>
      <li class="breadcrumb-item active" aria-current="page">Profile</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('students.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="arrow-back-circle-outline"></ion-icon>Back</a>
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

<div class="row inbox-wrapper">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-3 border-end-lg">
            <div class="d-flex align-items-center justify-content-between">
              <button class="navbar-toggle btn btn-icon border d-block d-lg-none" data-bs-target=".email-aside-nav" data-bs-toggle="collapse" type="button">
                <span class="icon"><i data-feather="chevron-down"></i></span>
              </button>
              <div class="text-center w-100" >
                @if ($student->image)
                        <img class="wd-50 ht-50 rounded-circle text-center" src="{{ asset('store/'.$student->image) }}" alt="photo" >
                    @else
                        @if ($student->gender == 'male')
                        <img class="wd-50 ht-50 rounded-circle" src="{{ url('https://cdn-icons-png.flaticon.com/512/3135/3135755.png') }}" alt="">
                        @else
                        <img class="wd-50 ht-50 rounded-circle" src="{{ url('https://cdn-icons-png.flaticon.com/512/9676/9676572.png') }}" alt="">
                        @endif
                    @endif
              </div>
            </div>
            <div class="d-grid my-3">
              <a class="btn btn-success">Navigation</a>
            </div>
            <div class="email-aside-nav collapse">
              <ul class="nav flex-column">
                <li class="nav-item active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" role="tab" aria-controls="home" aria-selected="false">
                  <a class="nav-link d-flex align-items-center" href="#" >
                    <i data-feather="phone-call" class="icon-lg me-2"></i>
                     Contact
                  </a>
                </li>
                <li class="nav-item" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" role="tab" aria-controls="contact" aria-selected="true">
                  <a class="nav-link d-flex align-items-center" href="#">
                    <i data-feather="truck" class="icon-lg me-2"></i>
                    Transport Details
                  </a>
                </li>
                <li class="nav-item" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" role="tab" aria-controls="profile" aria-selected="false">
                  <a class="nav-link d-flex align-items-center" href="#">
                    <i data-feather="file-text" class="icon-lg me-2"></i>
                    School Fees Structure
                  </a>
                </li>
                
                <li class="nav-item" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" role="tab" aria-controls="center" aria-selected="false">
                  <a class="nav-link d-flex align-items-center" href="#">
                    <i data-feather="layers" class="icon-lg me-2"></i>
                    Payment History
                  </a>
                </li>
              </ul>
              
            </div>
          </div>
          <div class="col-lg-9">
            <div class="p-3 border-bottom">
              <div class="row align-items-center">
                <div class="col-lg-12">
                  <div class="d-flex align-items-end mb-2 mb-md-0">
                    <h4 class="me-1">{{$student->first_name}} {{$student->last_name}}</h4>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="input-group">
                    <div class="col-md-3">
                      <div class="mt-3">
                        <label class="tx-11 fw-bolder mb-0 text-uppercase">Addmission No:</label>
                        <p class="text-muted">{{$student->add_num}}</p>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="mt-3">
                        <label class="tx-11 fw-bolder mb-0 text-uppercase">{{$tr->grade_class ?? 'Grade'}}:</label>
                        <p class="text-muted">{{DB::table('student_classes')->where('id','=',$student->grade)->first()->name}}</p>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="mt-3">
                        <label class="tx-11 fw-bolder mb-0 text-uppercase">Stream:</label>
                        <p class="text-muted">{{App\Models\Stream::where('id','=',$student->stream)->first()->name}}</p>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="mt-3">
                        <label class="tx-11 fw-bolder mb-0 text-uppercase">Term:</label>
                        <p class="text-muted">{{App\Models\SchoolTermDate::where('status','=', 1)->first()->name}}</p>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
            
            <div class="email-list tab-content" id="lineTabContent">
              
              <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                <div class="p-3">
                  <h6 class="card-title mb-0">Contact</h6>
  
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mt-3">
                          <label class="tx-11 fw-bolder mb-0 text-uppercase">Parent:</label>
                          <p class="text-muted">{{$student->parent->name}}</p>
                        </div>
                      </div>
  
                      <div class="col-md-6">
                        <div class="mt-3">
                          <label class="tx-11 fw-bolder mb-0 text-uppercase">Contact:</label>
                          <p class="text-muted"><a href="tel:{{$student->parent->phone_num}}">{{$student->parent->phone_num}}</a></p>
                        </div>
                      </div>
  
                      @if ($parent_two)
                      <div class="col-md-6">
                        <div class="mt-3">
                          <label class="tx-11 fw-bolder mb-0 text-uppercase">Parent Two:</label>
                          <p class="text-muted">{{$parent_two->name}}</p>
                        </div>
                      </div>
  
                      <div class="col-md-6">
                        <div class="mt-3">
                          <label class="tx-11 fw-bolder mb-0 text-uppercase">Contact:</label>
                          <p class="text-muted"><a href="tel:{{$parent_two->phone_num}}">{{$parent_two->phone_num}}</a></p>
                        </div>
                      </div>
                      @endif
                      
                      @if ($guardian)
                      <div class="col-md-6">
                        <div class="mt-3">
                          <label class="tx-11 fw-bolder mb-0 text-uppercase">Guardian:</label>
                          <p class="text-muted">{{$guardian->name}}</p>
                        </div>
                      </div>
  
                      <div class="col-md-6">
                        <div class="mt-3">
                          <label class="tx-11 fw-bolder mb-0 text-uppercase">Contact:</label>
                          <p class="text-muted"><a href="tel:{{$guardian->phone_num}}">{{$guardian->phone_num}}</a></p>
                        </div>
                      </div>
                      @endif
                     
  
  
                      
                    </div>
                  </div>
                  
              </div>
              
              <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                <div class=" rounded">
                  <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <h6 class="card-title mb-0">Transport Details</h6>
                      <div class="dropdown">
                        <button class="btn btn-link p-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="git-branch" class="icon-sm me-2"></i> <span class="">Update</span></a>
                          <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="eye" class="icon-sm me-2"></i> <span class="">View all</span></a>
                        </div>
                      </div>
                    </div>
                    <div class="row mt-3">
                      @if (!$student->bus_assigned)
                          <p class="text-danger">Not assigned</p>
                      @endif
                      @foreach ($vehicle_trips as $key => $vehicle_trip)
                        @if ($student->trip_type == 3)
                          <div class="col-md-6">
                            <h6>@if ($key == 0)
                                Pickup
                            @elseif($key == 1)
                            Drop off
                            @endif</h6>
                            <div class="mt-3">
                              <label class="tx-11 fw-bolder mb-0 text-uppercase">Vehicle:</label>
                              <p class="text-muted">{{$vehicle_trip->plate_num}}</p>
                            </div>
                            <div class="mt-3">
                              <label class="tx-11 fw-bolder mb-0 text-uppercase">Trip:</label>
                              @php
                                  $dep_time = new \Carbon\Carbon($vehicle_trip->trip->time_from);
                                  $ret_time = new \Carbon\Carbon($vehicle_trip->trip->time_to);
                              @endphp
                              <p class="text-muted">{{$vehicle_trip->trip->title}} ({{$dep_time->format('g:i A')}} - {{$ret_time->format('g:i A')}})</p>
                            </div>
                          </div>
                        @endif

                        @if ($student->trip_type == 1)
                        <div class="col-md-6">
                          <h6>Pickup</h6>
                          <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">Vehicle:</label>
                            <p class="text-muted">{{$vehicle_trip->plate_num}}</p>
                          </div>
                          <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">Trip:</label>
                            @php
                                $dep_time = new \Carbon\Carbon($vehicle_trip->trip->time_from);
                                $ret_time = new \Carbon\Carbon($vehicle_trip->trip->time_to);
                            @endphp
                            <p class="text-muted">{{$vehicle_trip->trip->title}} ({{$dep_time->format('g:i A')}} - {{$ret_time->format('g:i A')}})</p>
                          </div>
                        </div>
                        @endif

                        @if ($student->trip_type == 2)
                        <div class="col-md-6">
                          <h6>Drop off</h6>
                          <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">Vehicle:</label>
                            <p class="text-muted">{{$vehicle_trip->plate_num}}</p>
                          </div>
                          <div class="mt-3">
                            <label class="tx-11 fw-bolder mb-0 text-uppercase">Trip:</label>
                            @php
                                $dep_time = new \Carbon\Carbon($vehicle_trip->trip->time_from);
                                $ret_time = new \Carbon\Carbon($vehicle_trip->trip->time_to);
                            @endphp
                            <p class="text-muted">{{$vehicle_trip->trip->title}} ({{$dep_time->format('g:i A')}} - {{$ret_time->format('g:i A')}})</p>
                          </div>
                        </div>
                        @endif
                      
                      @endforeach
                        
                    </div>
                   
                  </div>
                </div>
              </div>

              <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="card mt-3">
                  <div class="card-body">
                    <h6 class="card-title">School Fees Structure</h6>
                    <hr>
                    <div class="row">
                      <div class="col-md-12">
                        @foreach ($schoolfees as $fee)
                        <ul class="list-group mb-3">
                          <?php $fee_amt = 0; $has_trasnport = false; ?>
                          @foreach ($fee->details as $detail)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                              {{ucfirst($detail->detail)}}
                              @if ($detail->detail == 'transport')
                              <?php  $has_trasnport = true; ?>
                              @endif
                              <span style="font-size: 13px;" class="badge bg-white text-dark rounded-pill">
                                {{$settings->currency}}  {{number_format($detail->detail_amount, 2)}}
                              </span>
                            </li>
                            <?php $fee_amt += $detail->detail_amount ?>
                          @endforeach
                          <li class="list-group-item d-flex justify-content-between align-items-center">
                            @if ($has_trasnport)
                            Total Amount
                            @else
                            Sub Total Amount
                            @endif 
                            <span style="font-size: 13px;" class="badge bg-white text-dark rounded-pill">
                             {{$settings->currency}} {{number_format($fee_amt, 2)}}
                            </span>
                          </li>

                          <li class="list-group-item d-flex justify-content-between align-items-center">
                            Payment
                              <a class="btn btn-primary" href="{{url('/school-fees-payment/'.Crypt::encrypt($fee->id))}}">
                                Add payment
                              </a>
                            
                          </li>
                        </ul>
                        @endforeach
                      </div>

                      </div>

                  </div>
                </div>
                  <div class="card mt-3">
                    <div class="table-responsive mt-3">

                    <div class="card-body">
                      <h6 class="card-title">School Fees Payments</h6>
                      <hr>

                      <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
                        <thead>
                            <tr>
                              <th>#</th>
                              <th>Receipt No</th>
                              <th>Amount paid</th>
                              <th>Payment method</th>
                              <th>Date paid</th>
                              <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($schoolfees as $fee)
                            @foreach ($fee->payemnts as $payment)
                            <tr>
                                <td>{{$number}}</td>
                                <?php $number++ ?>
                                <td>{{$payment->receipt_number}}</td>
                                <td>{{$settings->currency}}  {{number_format($payment->amount_paid, 2)}}</td>
                                <td>{{$payment->payment_method}}</td>
                                <td>{{date_format(date_create($payment->date_paid), 'M d, Y')}}</td>
                                <td>{{$settings->currency}} {{number_format($payment->balance,2)}}</td>
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                        
                    </table>
                    </div>
                  </div>
                  
                </div>
              </div>

              

              <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                @include('students.payment-history')
              </div>
            </div>
          </div>
        </div>
      
      </div>
    </div>
  </div>
</div>



@endsection


@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script defer>
      $(function() {
        $('#a-tag-click-submit').on('click', () => {
          $('#assign-fee-form').submit();
        })
      });
    </script>
@endpush