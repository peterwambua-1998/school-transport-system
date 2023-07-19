@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')

<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">Vehicles Maintenance</a></li>
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
                <h6 class="card-title">Vehicles Maintenance Table</h6>
    
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Bus Reg. No</th>
                                <th>Driver</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($vehicles as $vehicle)
                                
                            
                            <tr>
                                <td>{{ $number }}</td>
                                <?php $number++; ?>
                                <td>{{ $vehicle->title }}</td>
                                <td>{{ $vehicle->plate_num }}</td>
                                <td>{{ $vehicle->driver->name ?? 'Assign driver' }}</td>
                                <td>
                                    @if ($vehicle->active)
                                    <span class="badge bg-success">In-Service</span>
                                    @else
                                    <span class="badge bg-danger">Out-of-Service</span>
                                        
                                    @endif
                                </td>
                            
                                <td>
                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'head teacher')
                                    <a href="{{route('maintenance_show',Crypt::encrypt($vehicle->id))}}" class="span-delete mr-4">
                                        <span class=""><i class="fa-solid fa-eye text-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View maintenance details"></i></span>
                                    </a>
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



@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script defer>
       
    </script>
@endpush