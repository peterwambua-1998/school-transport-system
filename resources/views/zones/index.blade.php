@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <style>
    table {
        border-top-color: rgb(203 213 225);
        border-top-width: 2px;
        border-top-style: solid;
    }

    .span-delete {
        margin-right: 15px;
    }
  </style>
@endpush
@section('content')
<nav class="page-breadcrumb" style="display:grid; grid-template-columns: 1fr 1fr;">
    <ol class="breadcrumb" style="width: 100%">
      <li class="breadcrumb-item"><a href="#">Zones</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>

    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff')
    <div style="width: 100%; display: flex; flex-direction:row-reverse; gap:10px;">
        <a href="{{ route('zones.create') }}" class="btn btn-primary btn-create" style="border-radius:5px; margin-right: 10px"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff; font-size: 16px;" name="add-circle-outline"></ion-icon> Create Zone</a>
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
                    <h6 class="card-title">Zones Table</h6>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTableExample">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Zone Name</th>
                                    <th>Description</th>
                                    <th> Two-Way Price ({{$settings->currency ?? 'Ksh'}})</th>
                                    <th> One-Way Price ({{$settings->currency ?? 'Ksh'}})</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $number = 1; ?>
                                @foreach ($zones as $zone)
                                <tr>
                                    <td>{{ $number }}</td>
                                    <?php $number++; ?>
                                    <td>{{ $zone->name }}</td>
                                    <td>{{ $zone->description }}</td>
                                    <td>{{ $zone->price }}</td>
                                    <td>{{ $zone->oneway_price }}</td>
                                
                                    <td>
                                        @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff')
                                                
                                            <a href="{{ route('zoneGeoFencePage', Crypt::encrypt($zone->id)) }}" class="span-delete">
                                                <span><i class="fa fa-map" aria-hidden="true" style="color:#0071f3" title="Shows zone geo fence" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Shows zone geo fence"></i></span>
                                            </a>
                                            
                                            <a href="{{ route('zones.edit', Crypt::encrypt($zone->id)) }}" class="span-delete">
                                                <span><i class="fa fa-pencil" aria-hidden="true" style="color: rgb(2, 167, 2);" title="Edit zone details" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit zone details"></i></span>
                                            </a>


                                        @endif
                                        @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                            @if ($zone->status)
                                                <button data-bs-toggle="modal" data-bs-target="#del{{$zone->id}}" type="submit" class="span-delete" style="background: none; border: none">
                                                    <span ><i class="fa-solid fa-toggle-on text-success" aria-hidden="true" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate zone"></i></span>
                                                </button>
                                            @endif
                                            @if ($zone->status == 0)
                                            <a href="" data-bs-toggle="modal" data-bs-target="#activate{{$zone->id}}">
                                                <i class="fa-solid fa-toggle-off text-danger" style="font-size: 20px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate zone" title="Activate zone"></i>
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
 @foreach ($zones as $zone)
 <div class="modal fade" id="del{{$zone->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
        <form action="{{ route('zones.destroy', $zone->id) }}" method="post" style="display: inline-block">
             <div class="modal-header">
                 <h5 class="modal-title text-danger" id="exampleModalLabel">
                    <i class="far fa-lightbulb text-danger" style="margin-right: 20px;"></i>
                     
                     Deactivate zone {{$zone->name}}</h5>
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
 @endforeach

 @foreach ($zones as $zone)
 <div class="modal fade" id="activate{{$zone->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
        <form action="{{ route('activate_zone') }}" method="post" style="display: inline-block">
             <div class="modal-header">
                 <h5 class="modal-title text-success" id="exampleModalLabel">
                    <i class="far fa-lightbulb text-success" style="margin-right: 20px;"></i>
                     
                     Activate zone {{$zone->name}}</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
             </div>
             <div class="modal-body">
                 <p>Zone will be active. Are you sure?</p>
                 
                 @csrf
                 <input type="hidden" name="zone_id" value="{{$zone->id}}">
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


@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/data-table.js') }}"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script defer>
    $( function() {
        $( document ).tooltip();
    } );
    $(document).ready( function () {
        $('#vehTable').DataTable({
            responsive: true,
            language: { searchPlaceholder: "Search records", search: "",},
            columnDefs: [{
                targets: 0,
                className: 'stripe'
            }]
            
        });

        $('#vehTable1').DataTable({
            responsive: true,
            language: { searchPlaceholder: "Search records", search: "",},
            columnDefs: [{
                targets: 0,
                className: 'stripe'
            }]
            
        });

        document.getElementById('vehTable_wrapper').style.marginBottom  = '20px';

        if("{{ Session::has('success') }}") {
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: '{{ Session::get("success") }}',
                showConfirmButton: false,
                timer: 1500
            });
        } else if ("{{ Session::has('unsuccess') }}") {
            Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: '{{ Session::get("unsuccess") }}',
                showConfirmButton: false,
                timer: 2500
            });
        }
    } );

</script>
@endpush
