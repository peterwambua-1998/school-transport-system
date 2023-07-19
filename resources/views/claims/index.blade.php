@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <style>
        .my-success {
            display: none;
        }
        .m-span:hover {
            cursor: pointer;
        }
    </style>
@endpush
@section('content')


<nav class="page-breadcrumb" style="display:grid; grid-template-columns: 1fr 1fr;">
    <ol class="breadcrumb" style="width: 100%">
      <li class="breadcrumb-item"><a href="#">Vehicle Claims</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
  
    <div style="width: 100%;display:flex;flex-direction:row-reverse; gap: 10px;">
        <a class="btn btn-primary" style="float: right;border-radius:5px" href="{{ route('claims.create', Crypt::encrypt($insurance->id)) }}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff;font-size:16px;" name="add-circle-outline"></ion-icon> Add Claim</a>
        <a class="btn btn-warning" style="float: right;border-radius:5px" href="{{ route('insurance.index') }}"><ion-icon style="position: relative; top:3px; right: 5px; color: #000;font-size:16px;" name="arrow-back-circle-outline"></ion-icon> Back</a>
    
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
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Claims for {{$vehicle->plate_num}} Table</h6>
        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>number</th>
                                <th>mileage (km)</th>
                                <th>date</th>
                                <th>approve_date</th>
                                <th>garage</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($claims as $claim)
                            <tr>
                                <td>{{$number}}</td>
                                <?php $number++; ?>
                                <td>{{$claim->claim_number}}</td>
                                <td>{{$claim->claim_mileage}}</td>
                                <td>{{$claim->claim_date}}</td>
                                <td>{{$claim->claim_approve_date}}</td>
                                <td>{{$claim->claim_garage}}</td>
                                <td>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        <i class="fa-solid fa-eye text-info"></i>
                                    </a> 
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

@foreach ($claims as $claim)
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Insurance claims for {{$vehicle->plate_num}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            <ul class="list-group mb-3">
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Bus Reg. No:</span> <span>{{$vehicle->plate_num}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Insurance Number:</span> <span>{{$insurance->ins_num}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Claim Number:</span> <span>{{$claim->claim_number}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Claim Mileage:</span> <span>{{$claim->claim_mileage}} Km</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Claim Date:</span> <span>{{$claim->claim_date}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Claim Approved Date:</span> <span>{{$claim->claim_approve_date}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Claim Report:</span> 
                    @if ($claim->report)
                    <span class="m-span" style="margin-left: 10px;"> <a href="{{route('dwn_report', Crypt::encrypt($claim->id))}}"><i class="fa-solid fa-download" style="fonr-size: 16px;"></i></a> </span>
                    @else
                    <span>upload report file</span>
                    @endif
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Claim Statement:</span> 
                    @if ($claim->statement)
                    <span class="m-span" style="margin-left: 10px;"><a href="{{route('dwn_statement', Crypt::encrypt($claim->id))}}"><i class="fa-solid fa-download" style="fonr-size: 16px;"></i></a></span> 
                    @else
                    <span>upload statement file</span>
                    @endif
                    
                    
                </li>
            </ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
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
@endpush