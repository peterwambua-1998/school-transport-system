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

    a {
        color: #222222;
        font-size: 15px;
    }

    a:hover {
        color: black;
    }

    .no-pickup-btn {
      border: 1px solid rgba(0,0,0,.125);
      padding: 7px;
      background: #0071f3;
      color: #fff;
      border-radius: .25rem;
      
    }

    .no-pickup-btn:hover {
      background: #014797;
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
        border: 0;
        padding: 0;
    }
</style>
@endsection
@section('content')
<div class="top-navigation" style="background: #e2e8f0">
    <p style="font-size: 16px; ">
        Driver Details
    </p>
</div>




    <div class="row">
        
        <div class="col-sm-12">
            <div class="card tabs-card">
                
                <div class="tab-content card-block">
                    <div class="tab-pane active" id="home3" role="tabpanel">
    
                        <div class="table-responsive">
                            
                            <table class="table table-striped" style="border: 1px solid gray;" id="vehTable">
                                <thead style="background-color: #0071f3; color: #fff">
                                    <tr>
                                        
                                        <th>Child Name</th>
                                       
                                        <th>Driver Name</th>
                                        <th>Driver Number</th>
                                        <th>Vehicle</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $student)
                                        
                                    
                                    <tr>
                                        
                                        <td>
                                            <a href="{{ route('attendance_view', $student->id) }}">
                                            {{ $student->first_name }} {{ $student->last_name }}
                                            </a>
                                        </td>
                                        
                                        
                                        <td>
                                            <a href="{{ route('attendance_view', $student->id) }}">
                                            {{ $student->vehicle->driver->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('attendance_view', $student->id) }}">
                                            {{ $student->vehicle->driver->phone_num }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('attendance_view', $student->id) }}">
                                            {{ $student->vehicle->title }} {{ $student->vehicle->plate_num }}
                                            </a>
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
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script defer>
        function fireswal(student_id) {
                (async () => {

                    const { value: text } = await Swal.fire({
                    input: 'text',
                    inputLabel: 'Reason',
                    inputPlaceholder:  'reason for not attending...',
                    inputAttributes: {
                        'aria-label': 'Type your message here'
                    },
                    showCancelButton: true
                    })

                    if (text) {
                        var data = new FormData;
                        data.append('_token', '{{ csrf_token() }}');
                        data.append('student_id', student_id - 0);
                        data.append('reason', text);


                        $.ajax({
                            type: "POST",
                            url: "{{route('flag.store')}}",
                            processData: false,
                            contentType: false,
                            cache: false,
                            data: data,
                            
                            error: function(data){
                                console.log(data);
                            },
                            success: function (message) {
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }
                        })
                    }

                })()
            }
        $(document).ready( function () {
            $('#vehTable').DataTable({
                language: { searchPlaceholder: "Search records", search: "",},
            });


            
        } );

    </script>
@endsection