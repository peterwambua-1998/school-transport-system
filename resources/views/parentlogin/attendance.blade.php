@extends('layouts.app')
@section('css')

<style>
   .table-bordered thead th {
    border-bottom: 0; 
   }

   .table-bordered td {
    border: 1px solid #d0d2d4 !important;
   }
</style>
@endsection
@section('content')
<h5 class="text-center">Attendance Report </h5>

<div class="page-wrapper">
    

    <div class="page-header card">
        <h5 class="text-center pt-3">Bus Attendance (includes both am and pm for transport)</h5>
        <div class="card-block">
            <table class="table  table-bordered" style="border: 1px solid gray;" id="vehTable">
                <thead style="background-color: #0071f3; color: #fff">
                    <tr>
                       
                        <th>#</th>
                        <th>Student</th>
                        <th>Grade</th>
                        <th>Present</th>
                        <th>Absent</th>
                    </tr>
                </thead>
                <tbody id="attendance_table">

                    
                </tbody>
               
                
            </table>
        </div>
    </div>

    <div class="page-header card">
        <h5 class="text-center pt-3">Class Attendance</h5>
        <div class="card-block">
            <table class="table  table-bordered" style="border: 1px solid gray;" id="vehTable">
                <thead style="background-color: #0071f3; color: #fff">
                    <tr>
                       
                        <th>#</th>
                        <th>Student</th>
                        <th>Grade</th>
                        <th>Present</th>
                        <th>Absent</th>
                    </tr>
                </thead>
                <tbody id="school-attendance_table">

                    
                </tbody>
               
                
            </table>
        </div>
    </div>


    

    <div class="page-body">
        
    </div>

</div>
    

@endsection


@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script defer>
        $(document).ready( function () {

            
            $.ajax({
                type: "GET",
                url: "{{ route('attendance_data', $student->id) }}",
                processData: false,
                contentType: false,
                cache: false,
               
                error: function (err) {
                    console.log(err)
                },
                success: function (response) {
                 
                 $('#attendance_table').html(response);
                 $('[data-toggle="popover"]').popover(); 

                }
            });


            $.ajax({
                type: "GET",
                url: "{{ route('attendanceschool_data', $student->id) }}",
                processData: false,
                contentType: false,
                cache: false,
               
                error: function (err) {
                    console.log(err)
                },
                success: function (response) {
                 
                 $('school-attendance_table').html(response);
                 $('[data-toggle="popover"]').popover(); 

                }
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
                    timer: 1500
                });
            }else if ("{{ Session::has('errors') }}") {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ Session::get("erros") }}',
                    showConfirmButton: false,
                    timer: 2500
                });
        }


            $('#filter').on('click', getAtt);

            function getAtt() {

                var month = $('#month').val();
                var data = new FormData;
                data.append('_token', '{{ csrf_token() }}');
                data.append('month', month);
                

                $.ajax({
                    type: "POST",
                    url: "{{ route('attendance-report-query') }}",
                    processData: false,
                    contentType: false,
                    cache: false,
                    data: data,
                    error: function (err) {
                        console.log(err)
                    },
                    success: function (response) {
                        console.log(response);
                        $('tbody').html(response[0]);
                        $('[data-toggle="popover"]').popover(); 
                    }
                });
            }
        } );



    </script>
@endsection