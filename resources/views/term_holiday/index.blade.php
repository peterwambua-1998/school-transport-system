@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')


<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">Holidays</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
  
    @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'office staff')
    <div style="width: 15%">
        <a class="btn btn-primary" style="float: right;border-radius:5px" href="{{ route('term_holiday.create') }}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff;font-size:16px;" name="add-circle-outline"></ion-icon> Add Holiday</a>
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
                <h6 class="card-title">Holidays Table</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Start</th>
                                <th>End</th>                               
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1 ?>
                            @foreach ($terms as $term)                             
                            <tr>
                                <td>{{ $number }}</td>
                                <?php $number++; ?>
                                <td>{{$term->name}}</td>
                                <td>{{date_format(date_create($term->start), 'd-M-Y')}}</td>
                                <td>{{date_format(date_create($term->ends), 'd-M-Y')}}</td>        
                                <td>
                                    <div style="display:grid; grid-template-columns:1fr 1fr; width:50%">
                                        @if (Auth::user()->user_type == 'supervisor' || Auth::user()->user_type == 'admin')
                                            <a href="{{ route('term_holiday.edit', Crypt::encrypt($term->id)) }}" class="span-delete" title="">
                                                <i class="fa fa-pencil" aria-hidden="true" style="color: rgb(2, 167, 2);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit holiday details"></i>
                                            </a>

                                            @if ($term->status)
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#del{{$term->id}}" class="span-delete" >
                                                <i class="fa-solid fa-toggle-on text-success" aria-hidden="true" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Deactivate holiday"></i>
                                            </a>
                                            @else
                                            <a href="" data-bs-toggle="modal" data-bs-target="#activate{{$term->id}}">
                                                <i class="fa-solid fa-toggle-off text-danger" style="font-size: 20px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Activate holiday" title="Activate holiday"></i>
                                            </a>
                                            @endif
                                        @endif
                                    </div>
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
@foreach ($terms as $term)
<div class="modal fade" id="del{{$term->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="{{ route('term_holiday.destroy', $term->id) }}" method="post">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="exampleModalLabel">
                    <i class="far fa-lightbulb text-danger" style="margin-right: 20px;"></i>
                    
                    Deactivate {{$term->name}} holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure?</p>
                
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

@foreach ($terms as $term)
<div class="modal fade" id="activate{{$term->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="{{ route('activate_holiday') }}" method="post">
            <div class="modal-header">
                <h5 class="modal-title text-success" id="exampleModalLabel">
                    <i class="far fa-lightbulb text-success" style="margin-right: 20px;"></i>
                    Activate {{$term->name}} holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure?</p>
                
                @csrf
                <input type="hidden" name="holiday_id" value="{{$term->id}}">
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
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script defer>
        $( function() {
            $( document ).tooltip();
        } );
        $(document).ready( function () {
            $('#vehTable').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });
        } );

    </script>
@endpush