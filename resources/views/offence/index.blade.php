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
      <li class="breadcrumb-item"><a href="#">Offences</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
    
    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
    <div style="width: 15%">
        <a class="btn btn-primary" style="float: right;border-radius:5px" href="{{ route('offence.create') }}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff;font-size:16px;" name="add-circle-outline"></ion-icon> Add Offence</a>
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
                <h6 class="card-title">Offences Table</h6>
        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Offender Name</th>
                                <th>Offender Contact</th>
                                <th>Offender Type</th>
                                <th>Bus Reg. No</th>
                       
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($offences as $offence)
                            <tr>
                                <td>{{$number}}</td>
                                <?php $number++; ?>
                                <td>
                                    {{$offence->user->name}}
                                </td>
                                <td>{{$offence->user->phone_num}}</td>
                                <td>
                                    @if ($offence->user->user_type == 'driver')
                                        Driver
                                    @endif
                                    @if ($offence->user->user_type == 'attendant')
                                        Attendant
                                    @endif
                                </td>
                                <td>
                                    {{$offence->vehicle->plate_num}}
                                </td>
                               
                                <td style="display:grid; grid-template-columns:1fr 1fr;">
                                    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff' || Auth::user()->user_type == 'head teacher')
                                        @if ($offence->status)
                                            <a href="" data-bs-toggle="modal" data-bs-target="#offence{{$offence->id}}">
                                                <i class="fa-solid fa-eye text-info" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View more details"></i>
                                            </a>
                                        @endif

                                        @if ($offence->status)
                                        <a data-bs-toggle="modal" data-bs-target="#del{{$offence->id}}" href="#" class="span-delete" style="background: none; border: none">
                                            <i class="fa-solid fa-toggle-on text-success" aria-hidden="true" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate offence"></i>
                                        </a>
                                        @else
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#activate{{$offence->id}}">
                                            <i class="fa-solid fa-toggle-off text-danger" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate offence" title="Activate offence"></i>
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
@foreach ($offences as $offence)
<div class="modal fade" id="offence{{$offence->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Offence</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
        </div>
        <div class="modal-body">
            <ul class="list-group mb-3">
                    
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Name:</span> <span>{{$offence->user->name}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Contact:</span> <span>{{$offence->user->phone_num}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Type:</span> <span>{{$offence->type}}</span>
                </li>
                @if ($offence->vehicle)
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Bus Reg. No:</span> <span>{{$offence->vehicle->plate_num}}</span>
                </li>
                @endif
                
                @if ($offence->user->user_type == 'driver' && $offence->license)
                <li class="list-group-item">
                    <span class="ml-5 text-muted">DL Number:</span> <span>{{$offence->license->dl_number}}</span>
                </li>
                @endif
                
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Description:</span> <span>{{$offence->description}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted"> Action Taken:</span> <span>{{$offence->disciplinary_action}}</span>
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


@foreach ($offences as $offence)
<div class="modal fade" id="del{{$offence->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       <form action="{{ route('offence.destroy', $offence->id) }}" method="post" style="display: inline-block">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="exampleModalLabel">
                   <i class="far fa-lightbulb text-danger" style="margin-right: 20px;"></i>
                    
                    Deactivate offence</h5>
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

@foreach ($offences as $offence)
<div class="modal fade" id="activate{{$offence->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
       <form action="{{ route('activate_offence') }}" method="post" style="display: inline-block">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="exampleModalLabel">
                   <i class="far fa-lightbulb text-success" style="margin-right: 20px;"></i>
                    
                    Activate offence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Offence will be active. Are you sure?</p>
                
                @csrf
                <input type="hidden" name="offence_id" value="{{$offence->id}}">
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
    <script defer>
        
        
        function activate(activated, term_id) {
           
            
            var data = new FormData;
            data.append('_token', '{{csrf_token()}}');
            data.append('status', activated);
            data.append('term_id', term_id);

            $.ajax({
                type: "POST",
                url: "{{route('activate_term')}}",
                processData: false,
                contentType: false,
                cache: false,
                data: data,
                        
                error: function(data){
                    console.log(data);
                },
                success: function (message) {
                    console.log(message);
                    $('.my-success').show('slow');
                    setTimeout(() => {
                        location.reload();
                    }, 2500);
                    
                }
            });
            
        }

    </script>
@endpush