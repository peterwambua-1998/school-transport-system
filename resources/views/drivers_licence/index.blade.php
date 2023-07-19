@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')


<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">Licenses</a></li>
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
                <h6 class="card-title">Licenses Table</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Driver Name</th>
                                <th>Driver Phone</th>
                                <th>Bus Reg. No</th>                               
                                <th>Date Issued</th>
                                <th>Date Renewed</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1 ?>
                            @foreach ($drivers as $driver)                             
                            <tr>
                                <td>{{ $number }}</td>
                                <?php $number++; ?>
                                <td>{{$driver->name}}</td>
                                <td>{{$driver->phone_num}}</td>
                                <td>{{($driver->vehicle) ? $driver->vehicle->plate_num : 'Assign'}}</td>
                                <td>{{($driver->licence) ? date_format(date_create($driver->licence->date_issued), 'd-M-Y') : 'Add details'}}</td>
                                <td>{{($driver->licence) ? date_format(date_create($driver->licence->date_renewed), 'd-M-Y') : 'Add details'}}</td>        
                                <td>{{($driver->licence) ? date_format(date_create($driver->licence->exp_date), 'd-M-Y') : 'Add details' }}</td>
                                <td>
                                    @if ($driver->licence)
                                        @if ($driver->licence->status)
                                            <p class="badge bg-success">Active</p>
                                        @else
                                            <p class="badge bg-danger">Expired</p>
                                        @endif
                                    @else
                                        add
                                    @endif
                                </td>
                                <td style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">

                                    @if ($driver->licence)
                                        @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'head teacher')
                                        <a data-bs-toggle="modal" data-bs-target="#driver-details{{$driver->id}}">
                                            <i class="fa-solid fa-eye text-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View more details"></i>
                                        </a>

                                        <a href="#" data-bs-toggle="modal" data-bs-target="#driver{{$driver->id}}">
                                            <i class="fa-regular fa-id-card text-primary" style="font-size: 16px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Renew licence" title=""></i>
                                        </a>

                                        <a href="{{route('license.edit', Crypt::encrypt($driver->id))}}">
                                            <i class="fa fa-pencil text-success"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit license" aria-hidden="true" style="font-size: 16px;"></i>
                                        </a>
                                        @endif
                                    @else
                                        @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                        <a href="{{route('create_dl', Crypt::encrypt($driver->id))}}">
                                            <i class="fa-solid fa-folder-plus text-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add license" style="font-size: 16px;"></i>
                                        </a>
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
@foreach ($drivers as $driver)
<div class="modal fade" id="driver{{$driver->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Renew {{$driver->name}} license</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            @if ($driver->licence)
            <form action="{{route('dl_renew')}}" method="post" class="renew-form">
                @csrf
                <input type="hidden" name="dl_id" value="{{$driver->licence->id}}">
                <div class="mb-3">
                    <label class="form-label" for="">Validity</label>
                    <input type="number" class="form-control validity" name="validity" placeholder="0" required>
                    <span class="text-danger"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="date_renewed">Date Renewed</label>
                    <input type="date" name="date_renewed" class="form-control date_renewed" required>
                    <span class="text-danger"></span>
                </div>
            </form>
            @else
                <p class="text-warning">Add Driving license to {{$driver->name}}.</p>
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


<!-- Modal -->
@foreach ($drivers as $driver)
<div class="modal fade" id="driver-details{{$driver->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{$driver->name}} Licence Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            <ul class="list-group mb-3">
                    
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Name:</span> <span>{{$driver->name}}</span>
                </li>
                @if ($driver->vehicle)
                
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Bus Reg. No:</span> <span>{{$driver->vehicle->plate_num}}</span>
                </li>
                @endif
                @if ($driver->licence)
                <li class="list-group-item">
                    <span class="ml-5 text-muted">DL Number:</span> <span>{{$driver->licence->dl_number}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">DL Class:</span> <span>{{$driver->licence->dl_class}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Date Issued:</span> <span>{{date_format(date_create($driver->licence->date_issued), 'd M, Y')}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Date Renewed:</span> <span>{{date_format(date_create($driver->licence->date_renewed), 'd M, Y')}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Validity:</span> <span>{{$driver->licence->validity}} years</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Expiry Date:</span> <span>{{date_format(date_create($driver->licence->exp_date), 'd M, Y')}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">DL Number:</span> 
                    @if ($driver->licence->status)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Expired</span>
                    @endif
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
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script defer>
        $( function() {
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
        });
    </script>
@endpush