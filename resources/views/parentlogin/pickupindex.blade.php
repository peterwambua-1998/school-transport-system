@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css">
<style>
    
    .dataTables_wrapper .dataTables_filter {
        float: none;
        text-align: center;
    }

    

    a {
        color: #fff;

    }

    a:hover {
        color: #fff;
    }

    .btn-create {
        background: #0071f3;
        border: 1px solid rgba(0, 0, 0, .125);
    }

    .btn-create:hover {
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

    .pick-up:hover {
        cursor: pointer;
    }

    .span-delete:hover { 
        cursor: pointer;
    }

    .top-navigation {
        padding: 10px;
        border-radius: .25rem;
        border: 1px solid rgba(0,0,0,.125);
        margin-bottom: 15px;
        display: flex;
        
    }

    .top-navigation p {
        flex-grow: 8;
        position: relative;
        top: 5px;
        letter-spacing: 1px;
        
    }

    .btn-create {
        margin: 5px;
        padding: 7px;
        border: 1px solid rgba(0,0,0,.125);
        border-radius: .25rem;

    }

    
</style>
@endsection
@section('content')
<div class="top-navigation" style="background: #e2e8f0">
    <p style="font-size: 16px;"><span>Students</span> - <span style="font-weight:500;">Management</span></p>
    <a href="{{ route('students.create') }}" class="btn btn-create" style="border-radius:5px">Add Student</a>
</div>



    <div class="row">
        
        <div class="col-sm-12">
            <div class="card tabs-card">
                
                <div class="tab-content card-block">
                    <div class="tab-pane active" id="home3" role="tabpanel">
    
                        <div class="table-responsive">
                           
                            <table class="table table-striped" style="border: 1px solid gray;" id="vehTable" data-ordering="false">
                                <thead style="background-color: #0071f3; color: #fff">
                                    <tr>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Transport Tel No</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="stdTableBody">
                                    @foreach ($students as $student)
                                        
                                        <td>{{ $student->first_name }}</td>
                                        <td>{{ $student->last_name }}</td>
                                        <th>{{ $settings->company_pnum ?? 'not provided' }}</th>
                                        
                                        
                                        <td>
                                            @if ($student->lat && $student->lng)
                                            <a href="{{ route('confirmpage', $student->id) }}" class="btn-create">Confirm</button>
                                            @endif
                                           

                                            @if (!$student->lat && !$student->lng)
                                                <a href="{{ route('selectpickuppage', $student->id) }}" class="btn-create">Select</a>  
                                            @endif
                                            
                                            @if ($student->lat && $student->lng)
                                            <a href="{{ route('changepickuppage', $student->id) }}" class="btn-create">Change</button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                               
                                
                            </table>
                        </div>
                        <div class="text-center">
                            
                        </div>
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
        $( function() {
            $( document ).tooltip();
        } );
        function pickUp(student_id, pickup_value) {

            var msg = '';

            if (pickup_value == 0) {
                msg = 'Student will not be picked up by bus';
            } else {
                msg = 'Student will be picked up by bus';
                
            }

            Swal.fire({
                title: 'Are you sure?',
                text: msg,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Change',
                cancelButtonText: 'Cancel',
            
            }).then((result) => {

                var dataPick = new FormData;
                dataPick.append('_token', '{{ csrf_token() }}');
                dataPick.append('pickup', pickup_value);

                var url = '/change-pickup/' + student_id;
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        processData: false,
                        contentType: false,
                        cache: false,
                        data: dataPick,
                        error: function (err) {
                            console.log(err)
                        },
                        success: function (response) {
                            console.log(response);
                            
                            if (response) {
                                location.reload();
                            } else {
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'system error please try again',
                                    showConfirmButton: false,
                                    timer: 2000,
                        
                                });
                            }
                            

                        }
                    })
                }
            })

            


            
        }
        $(document).ready( function () {
            $('#vehTable').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });

            document.getElementById('vehTable_wrapper').style.marginBottom  = '20px';

            $("input[name='search']").on( 'keyup', function () {
                table
                    .columns( 3 )
                    .search( this.value )
                    .draw();
            } );

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
                    timer: 2000,
        
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
            

            

        });

    </script>
@endsection
