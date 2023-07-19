@extends('layouts.app')
@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/photoswipe.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/photoswipe-lightbox.umd.min.js') }}"></script>
    <link href="{{ asset('css/photoswipe.css') }}" rel="stylesheet" />
    <style>
        .span-delete {
            margin-right: 15px;
        }
    </style>
@endpush
@section('content')


<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">Staff</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
    <div style="width: 15%">
        <a class="btn btn-primary" style="float: right;border-radius:5px" href="{{ route('staff_create') }}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff;font-size:16px;" name="add-circle-outline"></ion-icon> Add Staff</a>
    </div>
    @endif
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
            <h6 class="card-title">Staff Table</h6>
            <p class="text-muted mb-3"></p>   
        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Phone No</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($staffs as $staff)
                            <tr>
                                <td>{{ $number }}</td>
                                <?php $number++; ?>
                                <td>
                                    @if ($staff->image)
                                    <div class="test-gallery">
                                        <a href="{{ asset('store/'.$staff->image) }}" data-pswp-width="600" data-pswp-height="600">
                                            <img class="wd-80 ht-80 rounded-circle" src="{{ asset('store/'.$staff->image) }}" alt="">
                                        </a>
                                    </div>
                                    @else
                                        @if ($staff->gender == "male")
                                            <img class="wd-80 ht-80 rounded-circle" src="{{url('https://cdn-icons-png.flaticon.com/512/9875/9875255.png')}}" alt="staff">
                                        @else
                                        <img class="wd-80 ht-80 rounded-circle" src="{{url('https://cdn-icons-png.flaticon.com/512/9875/9875392.png')}}" alt="staff">
                                        @endif
                                    @endif
                                </td>
                                <td>{{$staff->name}}</td>
                                <td>{{ucfirst($staff->user_type)}} 
                                    @if ($staff->grade)
                                        (Grade {{  $staff->grade }})
                                    @endif
                                </td>
                                <td>{{ $staff->phone_num ?? 'N/A' }}</td>
                                <td>{{ $staff->email }}</td>
                                <td>
                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'head teacher')
                                        @if ($staff->status)
                                            <a href="#" class="span-delete" data-bs-toggle="modal" data-bs-target="#staff{{$staff->id}}">
                                                <span class=""><i class="fa-solid fa-eye text-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View more details"></i></span>
                                            </a>
                                            @if ($staff->user_type == "driver")
                                            <a href="#" class="span-delete" data-bs-toggle="modal" data-bs-target="#stand-in-driver{{$staff->id}}">
                                                <i class="fa-solid fa-repeat" style="font-weight: bolder; color: #e11d48" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add stand-in driver" title="Add stand-in driver"></i>
                                            </a>
                                            @endif

                                            @if ($staff->user_type == "attendant")
                                            <a href="#" class="span-delete" data-bs-toggle="modal" data-bs-target="#stand-in-attendant{{$staff->id}}">
                                                <i class="fa-solid fa-repeat " style="font-weight: bolder; color: #e11d48" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add stand-in attendant" title="Add stand-in attendant"></i>
                                            </a>
                                            @endif
                                        @endif

                                        <a href="{{ route('staff_edit',Crypt::encrypt($staff->id)) }}" class="span-delete" title="">
                                            <span><i class="fa fa-pencil text-success" aria-hidden="true" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit staff details"></i></span>
                                        </a>
                                    @endif

                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                        @if ($staff->status)
                                            <button type="button" class="span-delete mr-2" style="background: none; border: none" data-bs-toggle="modal" data-bs-target="#del{{$staff->id}}">
                                                <span ><i class="fa-solid fa-toggle-on text-success" aria-hidden="true" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate staff"></i></span>
                                            </button>
                                        @else
                                            <a href="" data-bs-toggle="modal" data-bs-target="#activate{{$staff->id}}">
                                                <i class="fa-solid fa-toggle-off text-danger" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate staff" title="Activate staff" style="font-size: 16px;"></i>
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            
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

<!-- Modal -->
@foreach ($staffs as $staff)
<div class="modal fade" id="staff{{$staff->id}}" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="exampleModalCenterTitle">{{$staff->name}} Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
        <div class="modal-body">
            <ul class="list-group">
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Name:</span> <span>{{$staff->name}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Staff Number:</span> <span>{{$staff->staff_num}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">ID Number:</span> <span>{{$staff->id_num ?? 'n/a'}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Phone Number:</span> <span>{{$staff->phone_num}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Email:</span> <span>{{$staff->email}}</span>
                </li>

                @if ($staff->user_type == "attendant")
                    @php
                        $vehicle = App\Models\Vehicle::find($staff->vehicle_id);
                    @endphp
                    <li class="list-group-item">
                        <span class="ml-5 text-muted">Vehicle Title:</span> <span>{{$vehicle->title ?? 'not allocated'}}</span>
                    </li>
                    <li class="list-group-item">
                        <span class="ml-5 text-muted">Vehicle Plate:</span> <span>{{$vehicle->plate_num ?? 'not allocated'}}</span>
                    </li>
                @endif

                @if ($staff->user_type == "attendant")
                @php
                    $vehicle = App\Models\Vehicle::find($staff->vehicle_id);
                @endphp
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Vehicle Title:</span> <span>{{$vehicle->title ?? 'not allocated'}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Vehicle Plate:</span> <span>{{$vehicle->plate_num ?? 'not allocated'}}</span>
                </li>
                @endif


              </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach


@foreach ($staffs as $staff)

<div class="modal fade" id="del{{$staff->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="{{ route('staff_destroy', $staff->id) }}" method="post" style="display: inline-block">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="exampleModalLabel">
                   <i class="far fa-lightbulb text-danger" style="margin-right: 20px;"></i>
                    
                    Deactivate {{$staff->name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Warning {{$staff->name}} will be inactive. Are you sure?</p>
               
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
@endforeach

@foreach ($staffs as $staff)

<div class="modal fade" id="activate{{$staff->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       <form action="{{ route('activate_staff') }}" method="post" style="display: inline-block">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="exampleModalLabel">
                   <i class="far fa-lightbulb text-success" style="margin-right: 20px;"></i>
                    
                    Activate {{$staff->name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>{{$staff->name}} will be active. Are you sure?</p>
                
                @csrf
                <input type="hidden" name="staff_id" value="{{$staff->id}}">
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-success">Activate</button>
               <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
      </div>
    </div>
</div>
@endforeach


@foreach ($staffs as $staff)
<div class="modal fade" id="stand-in-driver{{$staff->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Stand-in for {{$staff->name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('stand_in_driver') }}" method="post" class="stand-in-form-driver" style="display: inline-block">
                @csrf
                @php 
                    $other_drivers = App\Models\User::where('id','!=', $staff->id)->where('user_type','=','driver')->get();
                @endphp
                <input type="hidden" name="original_driver" value="{{$staff->id}}">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="vehicle_id" class="form-label">Stand-in Driver</label>
                        <select name="stand_in_driver" class="form-select stand_in_driver">
                            <option>select...</option>
                            @foreach ($other_drivers as $other_driver)
                                <option value="{{$other_driver->id}}">{{$other_driver->name}}</option>
                            @endforeach
                        </select>
                        <span class="text-danger stand_in_driver_error"></span>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control date_from">
                        <span class="text-danger date_from_error"></span>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="date_to" class="form-label">Date From</label>
                        <input type="date" name="date_to" class="form-control date_to">
                        <span class="text-danger date_to_error"></span>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="stand_in_status" class="form-label">Status</label>
                        <select name="stand_in_status" class="form-select stand_in_status">
                            <option>select...</option>
                            <option value="1">Active</option>
                            <option value="0">In-active</option>
                        </select>
                        <span class="text-danger stand_in_status_error"></span>
                    </div>
                </div>
                </form>

            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
               <button type="button" class="btn btn-success save-stand-in-driver">Save Changes</button>
            </div>
      </div>
    </div>
</div>
@endforeach


@foreach ($staffs as $staff)
<div class="modal fade" id="stand-in-attendant{{$staff->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Stand-in for {{$staff->name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('stand_in_attendant') }}" method="post" class="stand-in-form-attendant" style="display: inline-block">
                @csrf
                @php 
                    $other_attendants = App\Models\User::where('id','!=', $staff->id)->where('user_type','=','attendant')->get();
                @endphp
                <input type="hidden" name="original_attendant" value="{{$staff->id}}">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="stand_in_attendant" class="form-label">Stand-in Attendant</label>
                        <select name="stand_in_attendant" class="form-select stand_in_attendant">
                            <option>select...</option>
                            @foreach ($other_attendants as $other_attendant)
                                <option value="{{$other_attendant->id}}">{{$other_attendant->name}}</option>
                            @endforeach
                        </select>
                        <span class="text-danger stand_in_attendant_error"></span>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control date_from">
                        <span class="text-danger date_from_error"></span>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="date_to" class="form-label">Date From</label>
                        <input type="date" name="date_to" class="form-control date_to">
                        <span class="text-danger date_to_error"></span>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="stand_in_status" class="form-label">Status</label>
                        <select name="stand_in_status" class="form-select stand_in_status">
                            <option>select...</option>
                            <option value="1">Active</option>
                            <option value="0">In-active</option>
                        </select>
                        <span class="text-danger stand_in_status_error"></span>
                    </div>
                </div>
                </form>

            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
               <button type="button" class="btn btn-success save-stand-in-attendant">Save Changes</button>
            </div>
      </div>
    </div>
</div>
@endforeach



@endsection


@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script defer>
       
        $(document).ready( function () {
            $('#vehTable').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });



            $('.save-stand-in-driver').each((i, e) => {
                $(e).on('click',(ev) => {
                    ev.preventDefault();
                    let stand_in_form = $(e).parent().prev().find('.stand-in-form-driver');
                    let vehicle_id = $(e).parent().prev().find('.stand_in_driver');
                    let date_from = $(e).parent().prev().find('.date_from');
                    let date_to = $(e).parent().prev().find('.date_to');
                    let status = $(e).parent().prev().find('.stand_in_status');
                    console.log(vehicle_id, date_from.val(), date_to);

                    if (vehicle_id.find(':selected').text() == "select...") {
                        vehicle_id.next().text('field required');
                        return false;
                    } else {
                        vehicle_id.next().text('');
                    }

                    if (! date_from.val()) {
                        date_from.next().text('field required');
                        return false;
                    } else {
                        date_from.next().text('');
                    }

                    if (! date_to.val()) {
                        date_to.next().text('field required');
                        return false;
                    } else {
                        date_to.next().text('');
                    }

                    if (status.find(':selected').text() == "select...") {
                        status.next().text('field required');
                        return false;
                    } else {
                        status.next().text('');
                    }

                    stand_in_form.submit();
                });
            });

            $('.save-stand-in-attendant').each((i, e) => {
                $(e).on('click',(ev) => {
                    ev.preventDefault();
                    let stand_in_form = $(e).parent().prev().find('.stand-in-form-attendant');
                    let vehicle_id = $(e).parent().prev().find('.stand_in_attendant');
                    let date_from = $(e).parent().prev().find('.date_from');
                    let date_to = $(e).parent().prev().find('.date_to');
                    let status = $(e).parent().prev().find('.stand_in_status');
                    console.log(vehicle_id, date_from.val(), date_to);

                    if (vehicle_id.find(':selected').text() == "select...") {
                        vehicle_id.next().text('field required');
                        return false;
                    } else {
                        vehicle_id.next().text('');
                    }

                    if (! date_from.val()) {
                        date_from.next().text('field required');
                        return false;
                    } else {
                        date_from.next().text('');
                    }

                    if (! date_to.val()) {
                        date_to.next().text('field required');
                        return false;
                    } else {
                        date_to.next().text('');
                    }

                    if (status.find(':selected').text() == "select...") {
                        status.next().text('field required');
                        return false;
                    } else {
                        status.next().text('');
                    }

                    stand_in_form.submit();
                });
            });
        });

        var lightbox = new PhotoSwipeLightbox({
                gallery: '.test-gallery',
                children: 'a',
                // dynamic import is not supported in UMD version
                pswpModule: PhotoSwipe 
            });
            lightbox.init();

    </script>
@endpush