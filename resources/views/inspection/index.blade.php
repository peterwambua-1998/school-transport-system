@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
    <style>
        .my-success {
            display: none;
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


<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">Vehicle Inspection</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
  
    
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
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Inspection Table</h6>
        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Bus Reg. No</th>
                                <th>Driver Name</th>
                                <th>Driver Phone</th>
                                <th>Last Inspection</th>
                                <th>Next Inspection</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($vehicles as $vehicle)
                                
                           
                            <tr>
                                <td>{{$number}}</td>
                                <?php $number++; ?>
                                <td>{{$vehicle->plate_num}}</td>
                                <td>{{$vehicle->driver->name}}</td>
                                <td>{{$vehicle->driver->phone_num}}</td>
                                <td>
                                    @if ($vehicle->inspection)
                                        @if ($vehicle->inspection->last_inspection)
                                        {{date('d-M-Y', strtotime($vehicle->inspection->last_inspection))}}
                                        @else
                                        n/a 
                                        @endif
                                    @else
                                        Add Details
                                    @endif
                                </td>
                                        
                                <td>{{($vehicle->inspection) ? date('d-M-Y', strtotime($vehicle->inspection->next_inspection)) : 'Add Details'}}</td>
                                <td>
                                    @if ($vehicle->inspection)
                                        @php
                                            $inspection_date = Carbon\Carbon::createFromFormat('Y-m-d', $vehicle->inspection->next_inspection);
                                            $today_date = Carbon\Carbon::now();
                                        @endphp
                                        @if($inspection_date->gt($today_date))
                                        <span class="badge bg-success">Pending</span>
                                        @endif

                                        @if($inspection_date->lt($today_date))
                                            <span class="badge bg-danger">Overdue</span>
                                        @endif

                                        @if($inspection_date->eq($today_date))
                                        <span class="badge bg-warning">Due</span>
                                        @endif
                                    @else
                                    Add Details
                                    @endif

                                </td>
                                <td style="display: grid;grid-template-columns: 1fr 1fr 1fr 1fr 1fr;">
                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                    <a href="{{route('inspection.create', Crypt::encrypt($vehicle->id))}}">
                                        <i class="fa-solid fa-folder-plus text-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add inspection" title="" style="font-size: 16px"></i>
                                    </a>
                                    @endif

                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'head teacher')
                                        @if ($vehicle->inspection)
                                            @if ($vehicle->inspection->status)   
                                                <a href="#" class="mywish" data-bs-toggle="modal" data-bs-target="#view{{$vehicle->id}}" data-lat="{{$vehicle->inspection->lat}}" data-lng="{{$vehicle->inspection->lng}}" >
                                                    <i class="fa-solid fa-eye text-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View more details"></i>
                                                </a> 

                                                <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Upload report" href="#">
                                                    <i class="fa-solid fa-folder-open" data-bs-toggle="modal" data-bs-target="#report{{$vehicle->id}}"  title=""></i>
                                                </a> 
                                            @endif

                                            @if (!$vehicle->inspection->comment)
                                                <a href="{{ route('inspection.edit', Crypt::encrypt($vehicle->inspection->id)) }}" class="span-delete" style="margin-right: 15px;">
                                                    <span><i class="fa fa-pencil" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit inspection details" title="" aria-hidden="true" style="color: rgb(2, 167, 2);" ></i></span>
                                                </a>
                                            @endif
                                        
                                            @if ($vehicle->inspection->status)
                                                <button data-bs-toggle="modal" data-bs-target="#del{{$vehicle->inspection->id}}" type="submit" class="" style="background: none; border: none">
                                                    <span ><i class="fa-solid fa-toggle-on text-success" aria-hidden="true" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate inspection"></i></span>
                                                </button>
                                            @else
                                                <a href="" data-bs-toggle="modal" data-bs-target="#activate{{$vehicle->inspection->id}}">
                                                    <i class="fa-solid fa-toggle-off text-danger" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate inspection" title="Activate inspection"></i>
                                                </a>
                                            @endif
                                        @endif
                                    @endif
                                    
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-center"></div>     
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
@foreach ($vehicles as $key => $vehicle)
<div class="modal fade" id="view{{$vehicle->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content ">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Inspection details for {{$vehicle->plate_num}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-5">
                    <ul class="list-group mb-3">
                        @if ($vehicle->inspection)
                            <li class="list-group-item">
                                <span class="ml-5 text-muted">Bus Reg. No:</span> <span>{{$vehicle->plate_num}}</span>
                            </li>
                            <li class="list-group-item">
                                <span class="ml-5 text-muted">Driver:</span> <span>{{$vehicle->driver->name}}</span>
                            </li>
                            <li class="list-group-item">
                                <span class="ml-5 text-muted">Last Inspection:</span> <span>{{$vehicle->inspection->last_inspection}}</span>
                            </li>
                            <li class="list-group-item">
                                <span class="ml-5 text-muted">Next Inspection:</span> <span>{{$vehicle->inspection->next_inspection}}</span>
                            </li>
                            <li class="list-group-item">
                                <span class="ml-5 text-muted">Loaction:</span> <span>{{$vehicle->inspection->location_name}}</span>
                            </li>
                            @if ($vehicle->inspection->comment)
                            <li class="list-group-item">
                                <span class="ml-5 text-muted">Driver comment:</span> <span>{{$vehicle->inspection->comment}}</span>
                            </li>
                            @endif

                            @if ($vehicle->inspection->office_comment)
                            <li class="list-group-item">
                                <span class="ml-5 text-muted">Office comment:</span> <span>{{$vehicle->inspection->office_comment}}</span>
                            </li>
                            @endif
                            

                            <li class="list-group-item">
                                <span class="ml-5 text-muted">Report:</span> 
                                @if ($vehicle->inspection->report)
                                <span class="m-span" style="margin-left: 10px;"> <a href="{{route('inspection-report-download', Crypt::encrypt($vehicle->inspection->id))}}"><i class="fa-solid fa-download" style="fonr-size: 16px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Download report"></i></a> </span>
                                @else
                                <span>upload report</span>
                                @endif
                            </li>
                        @else
                            <p class="text-warning">Add Vehicle Insurance</p>
                        @endif
                        
                    </ul>
                </div>
                <div class="col-md-7">
                    <div id="map{{$key}}" style="width: 100%;height:100%"></div>
                </div>
            </div>
           
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>
@endforeach

@foreach ($vehicles as $key => $vehicle)
<div class="modal fade" id="report{{$vehicle->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
      <div class="modal-content ">
        <form action="{{route('inspection-report')}}" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Upload inspection report for {{$vehicle->plate_num}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    @if ($vehicle->inspection)
                        @csrf
                        <input type="hidden" name="inspection_id" value="{{Crypt::encrypt($vehicle->inspection->id)}}">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="comment" class="form-label">Comment</label><br>
                                <textarea id="comment" name="comment" rows="4" required class="form-control comment"></textarea>
                                <span class="text-danger comment_error"></span>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="comment" class="form-label">Report</label>
                                <input type="file" name="image" class="img report-file" id="myDropify{{$key}}" required>
                                <span class="text-danger report_file_error"></span>
                            </div>
                        </div>
                    @endif
                </div>
                
            </div>
           
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
          <button type="button"  id="report-submit" class="btn btn-success submit-report">Upload</button>
        </div>
        </form>
      </div>
    </div>
</div>
@endforeach

@foreach ($vehicles as $vehicle)
@if ($vehicle->inspection)
    
<div class="modal fade" id="del{{$vehicle->inspection->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       <form action="{{ route('inspection.destroy', Crypt::encrypt($vehicle->inspection->id)) }}" method="post" style="display: inline-block">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="exampleModalLabel">
                   <i class="far fa-lightbulb text-danger" style="margin-right: 20px;"></i>
                    Deactivate inspection for {{$vehicle->plate_num}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Warning record will be inactive. Are you sure?</p>
               
                @csrf
                @method('DELETE')
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-danger">Deactivate</button>
               <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
      </div>
    </div>
</div>
@endif

@endforeach

@foreach ($vehicles as $vehicle)
@if ($vehicle->inspection)
<div class="modal fade" id="activate{{$vehicle->inspection->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       <form action="{{ route('activate_inspection') }}" method="post" style="display: inline-block">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="exampleModalLabel">
                   <i class="far fa-lightbulb text-success" style="margin-right: 20px;"></i>
                    
                    Activate inspection for {{$vehicle->plate_num}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Inspection will be active. Are you sure?</p>
                
                @csrf
                <input type="hidden" name="inspection_id" value="{{$vehicle->inspection->id}}">
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-success">Activate</button>
               <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
      </div>
    </div>
</div>
@endif
@endforeach


@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/data-table.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&libraries=places&v=weekly"></script>
    <script defer>
        var nodeList = $('.mywish');

        var upload_inputs = $('.img');

        upload_inputs.each((index) => {
            $(`#myDropify${index}`).dropify();
        });

            let map;

            function initMap() {
            
                    var myLatlng;
                    let markers = [];

                   
                }

            initMap();

        
            var nodeList = $('.mywish');
        nodeList.each(function(index) {
            $(this).on('click', () => {
                var lat = $(this).attr('data-lat');
                var lng = $(this).attr('data-lng');

                let myLatlng = { lat: '{{ $settings->lat }}' - 0, lng: '{{ $settings->lng }}' - 0 };
                let map = new google.maps.Map(document.getElementById("map" + index), {
                    zoom: 10,
                    center: myLatlng,
                });
                let makers = [{lat: lat - 0, lng: lng - 0}];
                const image = "https://cdn-icons-png.flaticon.com/512/1183/1183390.png";
                for (let t = 0; t < makers.length; t++) {
                    new google.maps.Marker({
                        position: makers[t],
                        label: {text: "Inspection location", color: "#1e293b", fontSize: "15px", className: "label-marker"},
                        icon: {
                            url: image,
                            scaledSize: new google.maps.Size(50, 50), // scaled size
                            
                        },
                        map: map
                    });
                    map.panTo(makers[t]);
                }
            });
        });


        $(function() {
            $('.submit-report').each((i, e) => {
                $(e).on('click',(eb) => {
                    let is_filled;
                    if (!$(e).parent().prev().find('.comment').val()) {
                        $(e).parent().prev().find('.comment').focus();
                        $(e).parent().prev().find('.comment_error').text('field required');
                        is_filled = false;
                        return;
                    } else {
                        $(e).parent().prev().find('.comment_error').text('');
                        is_filled = true;
                    }

                    if (!$(e).parent().prev().find('.report-file').val()) {
                        $(e).parent().prev().find('.report-file').focus();
                        $(e).parent().prev().find('.report_file_error').text('field required');
                        is_filled = false;
                        return;
                    } else {
                        $(e).parent().prev().find('.report_file_error').text('');
                        is_filled = true;
                    }

                    if (is_filled) {
                        $(e).parent().parent().submit();
                    }
                })
            })

        })

    </script>
@endpush