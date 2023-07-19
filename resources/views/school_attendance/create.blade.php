@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush
@section('content')



<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Attendance List</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>
</nav>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<form action="{{ route('school-attendance.store') }}" method="POST">
    
    @csrf
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title">Mark Attendance</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 50%">Student Name</th>
                                <th scope="col" style="width: 50%">Mark Present / Absent</th> 
                            </tr>
                        </thead>
                        <tbody class="pt-5">
                            @foreach ($attendanceAm as $attendance)
                                @php
                                    $student = App\Models\Student::where('id', '=', $attendance->student_id)->first();
                                @endphp
                                <tr>
                                    <td>
                                        <input type="text" value="{{ $student->first_name }} {{ $student->last_name }}" name="" class="form-control">
                                        <input type="hidden" name="student_id[]" value="{{ $student->id }}">
                                        <input type="hidden" name="vehicle_id[]" value="{{ $attendance->vehicle_id }}">
                                    </td>
                                    <td>
                                        <select id="inputState" class="form-control" name="present[]">
                                            <option selected value="0">absent</option>
                                            <option value="1">present</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        
                    </table>
                </div>
                <div class="text-center">
                    <button id="submit-btn" type="submit" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Save List</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection


@section('js')
    <script defer src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer>
        $(document).ready( function () {
            $('#vehTable').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });

        });
    </script>
@endsection