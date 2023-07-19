@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <style>
        .icon-hover:hover {
            cursor: pointer;
        }
    </style>
@endpush
@section('content')


<nav class="page-breadcrumb">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">Reviews</a></li>
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
                <h6 class="card-title">Reviews Table</h6>
    
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTableExample" data-ordering="false">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Parent</th>
                                <th>Student</th>
                                <th>Rating</th>
                                <th>Feedback</th>
                                <th>Vehicle</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $number = 1; ?>
                            @foreach ($reviews as $review)
                            <tr>
                                @php
                                    $parent = App\Models\User::where('id','=',$review->user_id)->first() ?? 'deleted parent';
                                    $student = App\Models\Student::where('id','=',$review->student_id)->first() ?? 'deleted student';
                                    $trip = App\Models\Trip::find($review->trip_id);
                                    $vehicle = App\Models\Vehicle::where('id', '=', $trip->vehicle_id)->first() ?? 'deleted vehicle';
                                @endphp
                                <td>{{$number}}</td>
                                <?php $number++; ?>
                                <td>{{$parent->name}}</td>
                                <td>{{$student->first_name}} {{$student->last_name}}</td>
                                <td>{{$review->rating}}</td>
                                <td>{{Str::limit($review->feedback, 20)}}</td>
                                <td>{{$vehicle->title}}</td>
                                <td>{{$review->created_at->toFormattedDateString() }}</td>
                                <td>
                                    <i title="view more details" class="fa-solid fa-eye text-info icon-hover" data-bs-toggle="modal" data-bs-target="#review{{$review->id}}"></i>
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


{{-- modal --}}
@foreach ($reviews as $review)

@php
    $parent = App\Models\User::where('id','=',$review->user_id)->first() ?? 'deleted parent';
    $student = App\Models\Student::where('id','=',$review->student_id)->first() ?? 'deleted student';
    $trip = App\Models\Trip::find($review->trip_id);
    $vehicle = App\Models\Vehicle::where('id', '=', $trip->vehicle_id)->first() ?? 'deleted vehicle';
    $grade = DB::table('student_classes')->where('id','=', $student->grade)->first();
@endphp

<div class="modal fade" id="review{{$review->id}}" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="exampleModalCenterTitle">Review Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
        <div class="modal-body">
            <ul class="list-group mb-3">
                <p>Parent</p>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Name:</span> <span>{{$parent->name}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Name:</span> <span>{{$parent->phone_num}}</span>
                </li>
            </ul>

            <ul class="list-group mb-3">
                <p>Student</p>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Name:</span> <span>{{$student->first_name}} {{$student->last_name}}</span>
                </li>
                <li class="list-group-item">
                    @if ($grade)
                        <span class="ml-5 text-muted">Grade:</span> <span>{{$grade->name}}</span>
                    @endif
                </li>
            </ul>

            <ul class="list-group mb-3">
                <p>Review</p>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Rating:</span> <span>{{$review->rating}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Feedback:</span> <span>{{$review->feedback}}</span>
                </li>
            </ul>

            
            <ul class="list-group mb-3">
                <p>Vehicle</p>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Vehicle:</span> <span>{{$vehicle->plate_num}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Driver:</span> <span>{{$vehicle->driver->name}}</span>
                </li>
                <li class="list-group-item">
                    <span class="ml-5 text-muted">Phone:</span> <span>{{$vehicle->driver->phone_num}}</span>
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



