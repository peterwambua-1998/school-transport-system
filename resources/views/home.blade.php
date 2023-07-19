@extends('layouts.app')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
  <style>
    .modal-dialog {
      width: 750px;
      margin: auto;
    }

    .alert {
      padding: 10px !important;
      margin: 0px !important;
    }
  </style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Welcome to Dashboard</h4>
  </div>
  <div class="d-flex align-items-center flex-wrap text-nowrap">
    <div class="input-group flatpickr wd-200 me-2 mb-2 mb-md-0" id="dashboardDate">
      <span class="input-group-text input-group-addon bg-transparent border-warning" data-toggle><i data-feather="calendar" class="text-warning"></i></span>
      <input type="text" class="form-control bg-transparent border-warning" placeholder="Select date" data-input>
    </div>
    
    <button type="button" class="btn btn-warning btn-icon-text mb-2 mb-md-0">
      <i class="btn-icon-prepend" data-feather="download-cloud"></i>
      Download Report
    </button>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    @if ($msg_create_term)
    <div class="alert alert-danger" role="alert" >
        {{$msg_create_term}}
    </div> 
    @endif
  </div>
</div>
<div class="example">
  <!-- Modal -->
  <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Students-pending-bus-allocation</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table id="dataTableExample" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>student Name</th>
                  <th>Parent Name</th>
                  <th>Contact</th>
                </tr>
              </thead>
              <tbody id="parent-student-location">
                
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <a href="{{route('students.index',['unallocated'])}}" type="button" class="btn btn-success">View More Details</a>

          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-12 col-xl-12 stretch-card">
    <div class="row flex-grow-1">
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-6 col-md-12 col-xl-5">
                <div class="d-flex justify-content-between align-items-baseline">
                  <h6 class="card-title mb-2">Drivers</h6>
                </div>
                <h3 class="" id="drivers_total"></h3>
              </div>
              <div class="col-6 col-md-12 col-xl-7 text-end" >
                <i class="fa-solid fa-address-card text-warning" style="font-size: 40px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-6 col-md-12 col-xl-5">
                <div class="d-flex justify-content-between align-items-baseline">
                  <h6 class="card-title mb-2">Buses</h6>
                </div>
                <h3 class="" id="buses_total"></h3>
              </div>
              <div class="col-6 col-md-12 col-xl-7 text-end" >
                <i class="fa-solid fa-bus text-success" style="font-size: 40px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-6 col-md-12 col-xl-5">
                <div class="d-flex justify-content-between align-items-baseline">
                  <h6 class="card-title mb-2">Students</h6>
                </div>
                <h3 class="" id="students_total"></h3>
              </div>
              <div class="col-6 col-md-12 col-xl-7 text-end">
                <i class="fa-sharp fa-solid fa-graduation-cap text-warning" style="font-size: 40px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-6 col-md-12 col-xl-5">
                <div class="d-flex justify-content-between align-items-baseline">
                  <h6 class="card-title mb-2">Parents</h6>
                </div>
                <h3 class="" id="parents_total"></h3>
              </div>
              <div class="col-6 col-md-12 col-xl-7 text-end">
                <i class="fa-solid fa-user text-success" style="font-size: 40px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-6 col-md-12 col-xl-5">
                <div class="d-flex justify-content-between align-items-baseline">
                  <h6 class="card-title mb-2">Routes</h6>
                </div>
                <h3 class="" id="routes_total"></h3>
              </div>
              <div class="col-6 col-md-12 col-xl-7 text-end">
                <i class="fa-solid fa-route text-warning" style="font-size: 40px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-6 col-md-12 col-xl-5">
                <div class="d-flex justify-content-between align-items-baseline">
                  <h6 class="card-title mb-2">Zones</h6>
                </div>
                <h3 class="" id="zones_total"></h3>
              </div>
              <div class="col-6 col-md-12 col-xl-7 text-end">
                <i class="fa-solid fa-map text-success" style="font-size: 40px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            
            <div class="row">
              <div class="col-6 col-md-12 col-xl-5">
                <div class="d-flex justify-content-between align-items-baseline">
                  <h6 class="card-title mb-2">Staff</h6>
                </div>
                <h3 class="" id="staff_total"></h3>
              </div>
              <div class="col-6 col-md-12 col-xl-7 text-end">
                <i class="fa-solid fa-users text-warning"  style="font-size: 40px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            
            <div class="row">
              <div class="col-6 col-md-12 col-xl-5">
                <div class="d-flex justify-content-between align-items-baseline">
                  <h6 class="card-title mb-2">Transport Students</h6>
                </div>
                <h3 class="" id="transport_students_total"></h3>
              </div>
              <div class="col-6 col-md-12 col-xl-7 text-end">
                <i class="fa-solid fa-users text-warning"  style="font-size: 40px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div> <!-- row -->

<!-- row -->

<div class="row">
  <div class="col-lg-7 col-xl-8 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-baseline mb-2">
          <h6 class="card-title mb-0">Monthly sales</h6>
          <div class="dropdown mb-2">
            <button class="btn btn-link p-0" type="button" id="dropdownMenuButton4" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton4">
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="eye" class="icon-sm me-2"></i> <span class="">View</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="printer" class="icon-sm me-2"></i> <span class="">Print</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="download" class="icon-sm me-2"></i> <span class="">Download</span></a>
            </div>
          </div>
        </div>
        <p class="text-muted">Sales are activities related to selling or the number of goods or services sold in a given time period.</p>
        <div id="monthlySalesChart"></div>
      </div> 
    </div>
  </div>

  <div class="col-lg-5 col-xl-4 grid-margin  stretch-card">
    <div class="card">
      <div class="card-body" >
        <div class="d-flex justify-content-between align-items-baseline mb-2">
          <h6 class="card-title mb-0">System Alerts</h6>
          <div class="dropdown mb-2">
            <button class="btn btn-link p-0" type="button" id="dropdownMenuButton6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton6">
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="eye" class="icon-sm me-2"></i> <span class="">View</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="printer" class="icon-sm me-2"></i> <span class="">Print</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="download" class="icon-sm me-2"></i> <span class="">Download</span></a>
            </div>
          </div>
        </div>
        <div class="d-flex flex-column" id="system-alerts">

        </div>
      </div>
    </div>
  </div>
</div> <!-- row -->

<div class="row">
  <div class="col-lg-5 col-xl-4 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-baseline mb-2">
          <h6 class="card-title mb-0">Cloud storage</h6>
          <div class="dropdown mb-2">
            <button class="btn btn-link p-0" type="button" id="dropdownMenuButton5" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton5">
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="eye" class="icon-sm me-2"></i> <span class="">View</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="printer" class="icon-sm me-2"></i> <span class="">Print</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="download" class="icon-sm me-2"></i> <span class="">Download</span></a>
            </div>
          </div>
        </div>
        <div id="storageChart"></div>
        <div class="row mb-3">
          <div class="col-6 d-flex justify-content-end">
            <div>
              <label class="d-flex align-items-center justify-content-end tx-10 text-uppercase fw-bolder">Total storage <span class="p-1 ms-1 rounded-circle bg-secondary"></span></label>
              <h5 class="fw-bolder mb-0 text-end">8TB</h5>
            </div>
          </div>
          <div class="col-6">
            <div>
              <label class="d-flex align-items-center tx-10 text-uppercase fw-bolder"><span class="p-1 me-1 rounded-circle bg-warning"></span> Used storage</label>
              <h5 class="fw-bolder mb-0">~5TB</h5>
            </div>
          </div>
        </div>
        <div class="d-grid">
          <button class="btn btn-warning">Upgrade storage</button>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-7 col-xl-8 stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-baseline mb-2">
          <h6 class="card-title mb-0">Projects</h6>
          <div class="dropdown mb-2">
            <button class="btn btn-link p-0" type="button" id="dropdownMenuButton7" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="eye" class="icon-sm me-2"></i> <span class="">View</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="edit-2" class="icon-sm me-2"></i> <span class="">Edit</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="trash" class="icon-sm me-2"></i> <span class="">Delete</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="printer" class="icon-sm me-2"></i> <span class="">Print</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><i data-feather="download" class="icon-sm me-2"></i> <span class="">Download</span></a>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th class="pt-0">#</th>
                <th class="pt-0">Project Name</th>
                <th class="pt-0">Start Date</th>
                <th class="pt-0">Due Date</th>
                <th class="pt-0">Status</th>
                <th class="pt-0">Assign</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>NobleUI jQuery</td>
                <td>01/01/2023</td>
                <td>26/04/2023</td>
                <td><span class="badge bg-danger">Released</span></td>
                <td>Leonardo Payne</td>
              </tr>
              <tr>
                <td>2</td>
                <td>NobleUI Angular</td>
                <td>01/01/2023</td>
                <td>26/04/2023</td>
                <td><span class="badge bg-success">Review</span></td>
                <td>Carl Henson</td>
              </tr>
              <tr>
                <td>3</td>
                <td>NobleUI ReactJs</td>
                <td>01/05/2023</td>
                <td>10/09/2023</td>
                <td><span class="badge bg-info">Pending</span></td>
                <td>Jensen Combs</td>
              </tr>
              <tr>
                <td>4</td>
                <td>NobleUI VueJs</td>
                <td>01/01/2023</td>
                <td>31/11/2023</td>
                <td><span class="badge bg-warning">Work in Progress</span>
                </td>
                <td>Amiah Burton</td>
              </tr>
              <tr>
                <td>5</td>
                <td>NobleUI Laravel</td>
                <td>01/01/2023</td>
                <td>31/12/2023</td>
                <td><span class="badge bg-danger">Coming soon</span></td>
                <td>Yaretzi Mayo</td>
              </tr>
              <tr>
                <td>6</td>
                <td>NobleUI NodeJs</td>
                <td>01/01/2023</td>
                <td>31/12/2023</td>
                <td><span class="badge bg-warning">Coming soon</span></td>
                <td>Carl Henson</td>
              </tr>
              <tr>
                <td class="border-bottom">3</td>
                <td class="border-bottom">NobleUI EmberJs</td>
                <td class="border-bottom">01/05/2023</td>
                <td class="border-bottom">10/11/2023</td>
                <td class="border-bottom"><span class="badge bg-info">Pending</span></td>
                <td class="border-bottom">Jensen Combs</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div> 
    </div>
  </div>
</div> <!-- row -->
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/apexcharts/apexcharts.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/dashboard.js') }}"></script>
  <script defer>
    var colors = {
      warning        : "#6571ff",
      secondary      : "#7987a1",
      success        : "#05a34a",
      info           : "#66d1d1",
      warning        : "#fbbc06",
      danger         : "#ff3366",
      light          : "#e9ecef",
      dark           : "#060c17",
      muted          : "#7987a1",
      gridBorder     : "rgba(77, 138, 240, .15)",
      bodyColor      : "#000",
      cardBg         : "#fff"
    }
    $(window).on('load', function() {

      //get modal data
      $.ajax({
        method: "get",
        url: "/parent-std-loc",
        processData: false,
        cache: false,
        contentType: false,
        error: function(err) {
          console.log(err);
        },
        success: function(response) {
          //parent-student-location
          let mynum = 1;
          for (let t = 0; t < response.length; t++) {
            let template = `
                <tr>
                  <td>${mynum}</td>  
                  <td>${response[t].student_name}</td>  
                  <td>${response[t].parent_name}</td>  
                  <td>${response[t].contact}</td>  
                </tr>
            `;
            mynum++;
            $('#parent-student-location').append(template);
          }

          if (response.length > 0) {
            $('#myModal').modal('show');
          }
          
        }
      })

      function firstTopTab() {
        $.ajax({
          method: "get",
          url: "/home-first-top-tabs",
          processData: false,
          cache: false,
          contentType: false,
          error: function(err) {
            console.log(err);
          },
          success: function(response) {
            console.log(response);
            $('#drivers_total').text(response["drivers"]);
            $('#buses_total').text(response["vehicles"]);
            $('#students_total').text(response["students"]);
            $('#parents_total').text(response["parents"]);
            $('#routes_total').text(response["routes"]);
            $('#staff_total').text(response["staff"]);
            $('#transport_students_total').text(response['student_transport']);
            $('#zones_total').text(response['zones']);
          }
        })
      }

      firstTopTab();



      function homeAlerts() {
        $.ajax({
          method: "get",
          url: "/home-alerts",
          processData: false,
          cache: false,
          contentType: false,
          error: function(err) {
            console.log(err);
          },
          success: function(response) {
            console.log(response);
            $('#system-alerts').children().remove();
            if (response.length > 0) {
              for (let t = 0; t < response.length; t++) {
                let template = `
                  <a href="javascript:;" class="d-flex align-items-center border-bottom pb-3">
                    <div class="w-100">
                      <div class="alert alert-danger" role="alert" id="dangers">
                        ${response[t]}
                      </div> 
                    </div>
                  </a>
                `;
                
                $('#system-alerts').append(template);
              }
            } else {
              $('#system-alerts').append('<p>All good.</p>');
            }


          }
        })
      }

      homeAlerts();
    });
  </script>
@endpush