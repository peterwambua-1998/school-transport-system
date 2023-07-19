@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
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
      <li class="breadcrumb-item"><a href="#">Vehicle Warranties</a></li>
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
                <h6 class="card-title">Warranties Table</h6>
        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Bus Reg. No</th>
                                <th>Mileage</th>
                                <th>Vehicle Status</th>
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
                                <td>{{$vehicle->mileage}} Km</td>
                                <td>
                                    @if ($vehicle->active == 1)
                                    <span class="badge bg-success">In-Service</span>
                                    @else
                                    <span class="badge bg-danger">Out-of-Service</span>
                                    @endif
                                </td>
                                <td style="display: flex;gap:30px;">
                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                        <a href="{{route('warranty.create', Crypt::encrypt($vehicle->id))}}">
                                            <i class="fa-solid fa-folder-plus text-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add warranty" style="font-size: 16px"></i>
                                        </a>
                                    @endif
                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'head teacher')
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#warranty{{$vehicle->id}}">
                                        <i class="fa-solid fa-eye text-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View more details" title="View more details"></i>
                                    </a>
                                    @endif
                                    {{--  
                                    @if ($vehicle->warranty->isNotEmpty())
                                        @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'head teacher')
                                            @if ($vehicle->warranty->status == 'active')
                                            <a href="{{route('warranty-claims', Crypt::encrypt($vehicle->warranty->id))}}" class="mywish">
                                                <i class="fa-solid fa-folder-open text-warning" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View warranty claims"></i>
                                            </a> 
                                            @endif
                                            <a href="{{ route('warranty.edit', Crypt::encrypt($vehicle->warranty->id)) }}" class="span-delete" style="margin-right: 15px;">
                                                <span><i class="fa fa-pencil" aria-hidden="true" style="color: rgb(2, 167, 2);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit vehicle warranty Details"></i></span>
                                            </a>
                                        @endif
                                    @endif
                                    @if ($vehicle->warranty->isNotEmpty())
                                        @if ($vehicle->warranty->status == 'inactive')
                                            @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                                <a href="{{route('warranty.create', Crypt::encrypt($vehicle->id))}}">
                                                    <i class="fa-solid fa-folder-plus text-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add warranty" style="font-size: 16px"></i>
                                                </a>
                                            @endif
                                        @endif
                                    @endif

                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                        <a href="{{route('warranty.create', Crypt::encrypt($vehicle->id))}}">
                                            <i class="fa-solid fa-folder-plus text-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add warranty" style="font-size: 16px"></i>
                                        </a>
                                    @endif


                                    @if ($vehicle->warranty)
                                        @if ($vehicle->warranty->status == 'inactive')
                                            @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                                <a href="" data-bs-toggle="modal" data-bs-target="#activate{{$vehicle->warranty->id}}">
                                                    <i class="fas fa-lightbulb" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate warranty" title="Activate warranty"></i>
                                                </a>
                                            @endif
                                        @endif

                                        @if ($vehicle->warranty->status == 'active')
                                            @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                                <button data-bs-toggle="modal" data-bs-target="#del{{$vehicle->warranty->id}}" type="submit" class="span-delete" style="background: none; border: none">
                                                    <span ><i class="fa fa-trash" aria-hidden="true" style="color: red" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate warranty"></i></span>
                                                </button>
                                            @endif
                                        @endif
                                    @endif
                                    --}}
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
@foreach ($vehicles as $vehicle)
<div  class="modal fade" id="warranty{{$vehicle->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Warranties for {{$vehicle->plate_num}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            @foreach ($vehicle->warranties as $warranty)
                <?php $claims = App\Models\WarrantyClaim::where('warranty_id','=', $warranty->id)->get(); ?>
                <ul class="list-group mb-3">
                    <li class="list-group-item active d-flex justify-content-between align-items-center">
                        <h5 class="">Dealer: {{$warranty->dealer}} </h5> 
                        <span class="badge bg-white rounded-pill">
                            @if($claims->isEmpty())
                            <div class="col-md-6" style="display: flex; gap: 20px;">
                                <a class="text-success ml-3" href="{{ route('warranty.edit', Crypt::encrypt($warranty->id)) }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit vehicle warranty Details">
                                    <i class="fa fa-pencil" style="margin-right: 5px; font-size: 14px" aria-hidden="true" ></i>
                                </a>
                                @if ($warranty->status == 'active')
                                <a class="text-success"  href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate">
                                    <i class="fa-solid fa-toggle-on text-success toggle-on" style="font-size: 18px" style="margin-right: 5px" aria-hidden="true" data-warranty="{{$warranty->id}}" data-vehicle="{{$vehicle->id}}"></i>
                                </a>
                                @else
                                <a class="text-success" href="#" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate">
                                    <i class="fa-solid fa-toggle-off text-danger toggle-off" style="font-size: 18px" style="margin-right: 5px" aria-hidden="true" data-warranty="{{$warranty->id}}" data-vehicle="{{$vehicle->id}}"></i>
                                </a>
                                @endif
                            </div>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item"><span class="text-muted" style="margin-right: 10px">Type:</span> {{$warranty->type}}</li>
                    <li class="list-group-item">
                        <span class="text-muted" style="margin-right: 10px">Status:</span> 
                        @if ($warranty->status == 'active')
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        @endif
                    </li>
                    
                    <li class="list-group-item">
                        <span class="text-muted" style="margin-right: 10px">Value:</span><span>{{$warranty->waranty_value}} {{$warranty->measurement}}</span>
                    </li>
                    @if($warranty->type == 'parts')
                    <li class="list-group-item">
                        <span class="text-muted" style="margin-right: 10px">Description:</span><span>{{$warranty->warranty_parts}}</span>
                    </li>
                    @endif
                    <li class="list-group-item">
                        <div class="row">
                            @if($claims->isNotEmpty())
                            <div class="col-md-6">
                                <a class="text-warning" href="{{route('warranty-claims', Crypt::encrypt($warranty->id))}}"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View warranty claims">
                                    <i class="fa-solid fa-folder-open " style="margin-right: 5px"></i> Claims
                                </a>
                            </div>
                            @else
                            <p class="text-muted">Claims not available</p>
                            @endif
                        </div>
                    </li>
                </ul>
                
            @endforeach
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>
@endforeach


@foreach ($vehicles as $vehicle)
@foreach ($vehicle->warranties as $warranty)
<div class="modal fade" id="del{{$warranty->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       <form action="{{ route('warranty.destroy', $warranty->id) }}" method="post" style="display: inline-block">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="exampleModalLabel">
                   <i class="far fa-lightbulb text-danger" style="margin-right: 20px;"></i>
                    
                    Deactivate warranty for {{$vehicle->plate_num}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Warranty from dealer {{$warranty->dealer}} will be inactive. Are you sure?</p>
               
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
@endforeach

@foreach ($vehicles as $vehicle)
@foreach ($vehicle->warranties as $warranty)
<div class="modal fade" id="activate{{$warranty->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       <form action="{{ route('warranty_activate') }}" method="post" style="display: inline-block">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="exampleModalLabel">
                   <i class="far fa-lightbulb text-success" style="margin-right: 20px;"></i>
                    
                    Activate warranty for {{$vehicle->plate_num}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Warranty from dealer {{$warranty->dealer}} will be active. Are you sure?</p>
                
                @csrf
                <input type="hidden" name="warranty_id" value="{{$warranty->id}}">
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
@endforeach

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/data-table.js') }}"></script>
<script defer>
    $(function() {
        $('.toggle-on').each((i, e) => {
            $(e).on('click',(ev) => {
                ev.preventDefault();
                let modal_id = '#warranty'+$(e).attr('data-vehicle');
                let del_modal = '#del'+$(e).attr('data-warranty');
                $(modal_id).modal('hide');
                $(del_modal).modal('show');
            });
        })

        $('.toggle-off').each((i, e) => {
            $(e).on('click',(ev) => {
                ev.preventDefault();
                let modal_id = '#warranty'+$(e).attr('data-vehicle');
                let activate_modal = '#activate'+$(e).attr('data-warranty');
                $(modal_id).modal('hide');
                $(activate_modal).modal('show');
            });
        })
    })
</script>
@endpush