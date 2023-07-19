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
        <a class="btn btn-warning" style="float: right;border-radius:5px" href="{{ route('warranty.index') }}"><ion-icon style="position: relative; top:3px; right: 5px; color: #000;font-size:16px;" name="arrow-back-circle-outline"></ion-icon> Back</a>
    
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
                                <th>Bus Reg. No</th>
                                <th>Warranty Mileage (Km)</th>
                                <th>Claim Date</th>
                                <th>Comments</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($claims as $claim)
                            <tr>
                                <td>{{$number}}</td>
                                <?php $number++; ?>
                                <td>{{$vehicle->plate_num}}</td>
                                <td>{{$claim->mileage}}</td>
                                <td>{{$claim->date}}</td>
                                <td>{{$claim->comment}}</td>
                                <td>{{App\Models\User::find($claim->recorded_by)->name}}</td>
                            </tr>
                            
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/data-table.js') }}"></script>
@endpush