@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css">
<style>
    .dataTables_wrapper .dataTables_filter {
        float: none;
        text-align: center;
    }

    .vihcleGrid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        justify-content: end;
        padding-left: 20px;
        padding-right: 3%;
    }

    a {
        color: #fff;

    }

    a:hover {
        color: #fff;
    }

    .btn-create {
        background: #0071f3
    }

    .vihcleGrid .btn-create:hover {
        background: #012549;
    }
    
    .table-title-vehicle {
        font-size:18px;
        font-weight: 500;
        margin-bottom: 25px;
        
    }

    .span-delete {
        margin-right: 2vw;
        font-size: 20px;
    }

    thead tr {
        border: none;
    }

    .card-header {
        border-top: 1px solid rgba(0,0,0,.125);
        border-radius: 0.25rem;
        background: #fff;
        border-left: 1px solid rgba(0,0,0,.125);
        border-right: 1px solid rgba(0,0,0,.125);
    }

    .select {
        background: transparent;
    }
</style>
@endsection
@section('content')

<div class="page-wrapper">
    <form action="{{ route('teachertrips_saveattendance') }}" method="POST">
    
        @csrf
    <div class="card-header mb-4">
        
            <h5 class="text-center">Mark Today's Attendance</h5>
        
    </div>
    <div class="card">
        
        <div class="card-body">
            
        
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead style="background: #0071f3">
                            <tr>
                                <th scope="col">Student Name</th>
                                <th scope="col">Mark Present / Absent</th>
                               
                            </tr>
                        </thead>
                        <tbody class="pt-5">
                            
                            @foreach ($depature as $depatures)
                            
                           
                            <tr>
                                <td>
                                    <input type="text" value="{{ $depatures->student->first_name }} {{ $depatures->student->last_name }}" name="" class="form-control">
                                    <input type="hidden" name="student_id[]" value="{{ $depatures->student->id }}">
                                    <input type="hidden" name="trip_id[]" value="{{ $depatures->schooltrip_id }}">
                                </td>
                                <td>
                                    <select id="inputState" class="form-control" name="present[]">
                                        <option selected value="absent">absent</option>
                                        <option value="present">present</option>
                                    </select>
                                </td>
                                
                            </tr>
                            @endforeach
                        </tbody>
                        
                    </table>
                </div>
                <div >
                    <button class="btn btn-primary btn-block mt-5">Submit</button>
                </div>
            
    
        
            
            
               
                
                
            
    
    
            
        
        </div>
    </div>
</form>
    

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
            }else if ("{{ Session::has('errors') }}") {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ Session::get("errors") }}',
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        } );

    </script>
@endsection