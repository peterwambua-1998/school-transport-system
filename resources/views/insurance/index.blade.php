@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <style>
        .my-success {
            display: none;
        }
    </style>
@endpush
@section('content')


<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">Vehicle Insurance</a></li>
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
                <h6 class="card-title">Insurances Table</h6>
        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Bus Reg. No</th>
                                <th>Driver Name</th>
                                <th>Driver Phone</th>
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
                                    @if ($vehicle->insurance)
                                        @php
                                        $status = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $vehicle->insurance->exp_date.'00:00:00');
                                        $expired = false;
                                        if ($status->gte(now())) {
                                            $expired = true;
                                        }
                                        @endphp

                                        @if ($expired)
                                        <span class="badge bg-success">In-Service</span>
                                        @else
                                        <span class="badge bg-danger">Out-of-Service</span>
                                        @endif
                                    @else
                                        <span class="badge bg-warning">Add details</span>
                                    @endif
                                   
                                <td style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr;">
                                        @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'head teacher')
                                        @if (!$vehicle->insurance)
                                            <a title="add insurance" href="{{route('create_ins', Crypt::encrypt($vehicle->id))}}">
                                                <i class="fa-solid fa-folder-plus text-primary" style="font-size: 16px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add insurance"></i>
                                            </a> 
                                        @else
                                            @if ($vehicle->insurance->status)
                                            <a href="#" title="view more details" data-bs-toggle="modal" data-bs-target="#view{{$vehicle->id}}">
                                                <i class="fa-solid fa-eye text-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View more details"></i>
                                            </a> 


                                            <a href="{{route('claims.show', Crypt::encrypt($vehicle->insurance->id))}}">
                                                <i class="fa-solid fa-folder-open" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View claims"></i>
                                            </a>
                                            
                                            @else
                                                <a href="">
                                                    Add 
                                                </a>
                                            @endif
                                            
                                        @endif
                                        @endif
                                        @if ($vehicle->insurance)
                                            @if ($vehicle->insurance->status)
                                                @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#renew{{$vehicle->id}}">
                                                    <i class="fa-solid fa-receipt" style="color: #3b82f6;" aria-hidden="true" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Renew"></i>
                                                </a>
                                                @endif
                                            @endif
                                        
                                            <a href="{{route('insurance.edit', Crypt::encrypt($vehicle->insurance->id))}}">
                                                <i class="fa fa-pencil icon text-success" aria-hidden="true" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit"></i>
                                            </a>
                                        @endif

                                        @if ($vehicle->insurance)
                                            @if ($vehicle->insurance->status)
                                                

                                                @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#disbale{{$vehicle->id}}">
                                                    <i class="fa-solid fa-toggle-on text-success" style="font-size: 20px;" aria-hidden="true" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Disable"></i>
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
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
@foreach ($vehicles as $vehicle)
<div class="modal fade" id="view{{$vehicle->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Insurance for {{$vehicle->plate_num}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            <ul class="list-group mb-3">
                @if ($vehicle->insurance)
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Bus Reg. No:</span> <span>{{$vehicle->plate_num}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Driver:</span> <span>{{$vehicle->driver->name}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Type:</span> <span>{{$vehicle->insurance->type}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Company:</span> <span>{{$vehicle->insurance->ins_company}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Date Renewed:</span> <span>{{ date('d-M-Y', strtotime($vehicle->insurance->renew_date) )}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Expiry Date:</span> <span>{{ date('d-M-Y', strtotime($vehicle->insurance->exp_date) )}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Valid For:</span> <span>{{$vehicle->insurance->validity}} days</span>
                </li>
                <li class="list-group-item">
                    @php
                        $status = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $vehicle->insurance->exp_date.'00:00:00');
                        $expired = false;
                        if ($status->gte(now())) {
                            $expired = true;
                        }
                    @endphp
                    @if ($expired)
                        <span class="badge bg-success">In-Service</span>
                    @else
                        <span class="badge bg-danger">Out-of-Service</span>
                    @endif
                </li>
                @else
                    <p class="text-warning">Add Vehicle Insurance</p>
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

@foreach ($vehicles as $vehicle)
<div class="modal fade" id="renew{{$vehicle->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Renew Insurance for {{$vehicle->plate_num}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            @if ($vehicle->insurance)
            <form action="{{route('renew_insurance')}}" method="post" class="renew-form">
                @csrf
                <input type="hidden" name="insurance_id" value="{{$vehicle->insurance->id}}">
                <div class="mb-3">
                    <label class="form-label" for="">Validity (Days)</label>
                    <input type="number" class="form-control validity" name="validity" placeholder="0" required>
                    <span class="text-danger"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="date_renewed">Date Renewed</label>
                    <input type="date" name="date_renewed" class="form-control date_renewed" required>
                    <span class="text-danger"></span>
                </div>
            </form>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success save-renew">Save changes</button>
        </div>
      </div>
    </div>
</div>
@endforeach

@foreach ($vehicles as $vehicle)
<div class="modal fade" id="disbale{{$vehicle->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">
            <i class="far fa-lightbulb text-danger" style="margin-right: 20px;"></i>
            Deactivate insurance for {{$vehicle->plate_num}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            @if ($vehicle->insurance)
            <form id="form-disable-insurance" action="{{Route('disable-insurace', $vehicle->insurance->id)}}" method="post">
                @csrf
                <p>Are you sure?</p>
                
            </form>
            @endif
           
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
          <button id="submit-disable-form" type="button" class="btn btn-danger" data-bs-dismiss="modal">Deactivate</button>
        </div>
      </div>
    </div>
</div>
@endforeach

@foreach ($vehicles as $vehicle)
<div class="modal fade" id="activate{{$vehicle->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">
            <i class="far fa-lightbulb text-success" style="margin-right: 20px;"></i>
            Activate insurance for {{$vehicle->plate_num}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            @if ($vehicle->insurance)
            <form id="form-disable-insurance" action="{{Route('insurance_activate', $vehicle->insurance->id)}}" method="post">
                @csrf
                <p>Are you sure?</p>
                
            </form>
            @endif
           
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
          <button id="submit-disable-form" type="button" class="btn btn-success" data-bs-dismiss="modal">Deactivate</button>
        </div>
      </div>
    </div>
</div>
@endforeach

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/data-table.js') }}"></script>
<script>
    $(function (){
        $('#submit-disable-form').on('click',()=>{
            $('#form-disable-insurance').submit();
        });

        $('.save-renew').each((i, e) => {
            $(e).on('click',(ev) => {
                let is_filled;
                if (!$(e).parent().prev().find('.validity').val()) {
                    $(e).parent().prev().find('.validity').next().text('field required');
                    $(e).parent().prev().find('.validity').focus();
                    ev.preventDefault();
                    is_filled = false;
                    return;
                } else {
                    $(e).parent().prev().find('.validity').next().text('');
                    is_filled = true;
                }

                if (!$(e).parent().prev().find('.date_renewed').val()) {
                    $(e).parent().prev().find('.date_renewed').next().text('field required');
                    $(e).parent().prev().find('.date_renewed').focus();
                    ev.preventDefault();
                    is_filled = false;
                    return;
                } else {
                    $(e).parent().prev().find('.date_renewed').next().text('');
                    is_filled = true;
                }

                console.log(is_filled);

                if (is_filled) {
                    $(e).parent().prev().find('.renew-form').submit();
                }
            }); 
        })
    })
   
</script>
@endpush