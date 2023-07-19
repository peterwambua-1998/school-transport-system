
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
    .delete:hover {
        cursor: pointer;
    }
    .card-header {
        border-top: 1px solid rgba(0,0,0,.125);
        border-radius: 0.25rem;
        
        border-left: 1px solid rgba(0,0,0,.125);
        border-right: 1px solid rgba(0,0,0,.125);
        
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

</style>
@endsection
@section('content')

<div class="top-navigation" style="background: #e2e8f0">
    <p style="font-size:16px;"><span>School Trips</span> - <span style="font-weight:500;">Management</span></p>
</div>




    <div class="row">
        
        <div class="col-sm-12">
            <div class="card tabs-card">
                
                <div class="tab-content card-block">
                    <div class="tab-pane active" id="home3" role="tabpanel">
    
                        <div class="table-responsive">
                            <p class="table-title-vehicle"><span style="color: #0071f3">School Trips</span> - List Viewer</p>
                            <table class="table table-striped" style="border: 1px solid gray;" id="vehTable">
                                <thead style="background-color: #0071f3; color: #fff">
                                    <tr>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Teacher</th>
                                        
                                        <th>Destination</th>
                                        
                                        <th>Departure</th>
                                        <th>Return</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($schooltrips as $schooltrip)                                    
                                    <tr>
                                        
                                        <td>{{ $schooltrip->trip_name }}</td>
                                        <td>{{ $schooltrip->trip_date }}</td>
                                        <td>{{ $schooltrip->teacher->name }}</td>
                                      
                                        <td>{{ $schooltrip->destination }}</td>
                                        
                                        <td>{{ $schooltrip->departure_time }}</td>
                                        <td>{{ $schooltrip->return_time }}</td>
                                        

                                        <td>
                                            
                                            
                                            <a href="{{ route('showroutepath', $schooltrip->id) }}" class="span-delete" title="Shows school trip route path">
                                                <span><i class="fa fa-map" aria-hidden="true" style="color:#0071f3"></i></span>
                                            </a>
                                            {{--
                                            <a href="{{ route('edit_fence', $vehicle->id) }}" class="span-delete" title="Edit Vehicle GeoFence">
                                                <span><i class="fa fa-map" aria-hidden="true" style="color:#0071f3"></i></span>
                                            </a>
                                            --}}
                                            @php
                                                $date = date('Y-m-d');
                                            @endphp
                                                @if ($schooltrip->trip_date === $date)
                                                <button onclick="sendNotification({{ $schooltrip->id }})">Start Trip</button>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.js"></script>
    <script defer src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script defer>
        $( function() {
            $( document ).tooltip();
        } );
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
        } );

        function sendNotification(schooltrip_id) {
            var dataTwo = new FormData;
            dataTwo.append('_token', '{{ csrf_token() }}');
            dataTwo.append('schooltrip_id', schooltrip_id);


            $.ajax({
                type: "POST",
                url: "{{ route('send_start_notification') }}",
                processData: false,
                contentType: false,
                cache: false,
                data: dataTwo,
                error: function (err) {
                    console.log(err)
                },
                success: function (response) {
                    console.log(response);
                    
                    
                   
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    setTimeout(() => {
                        location.href = '/driver-myschooltrips/schooltrips/schooltripshow/' + schooltrip_id;
                    }, 2000);
                   
                    
                    
                }
            });
        }



        if (!navigator.geolocation) {
        status.textContent = "Geolocation is not supported by your browser";
    } else {
        navigator.geolocation.watchPosition(success, error);
    }

    function error() {
        status.textContent = "Unable to retrieve your location";
    }

    function success(position) {
        var data = new FormData;
        data.append('_token', '{{ csrf_token() }}');
        data.append('latitude', position.coords.latitude);
        data.append('longitude', position.coords.longitude);
        $.ajax({
            type: "POST",
            url: "{{route('saveCoords')}}",
            processData: false,
            contentType: false,
            cache: false,
            data: data,
                        
            error: function(err){
                console.log(err);
            },
            success: function (message) {
                console.log(message)
                
            }
        });
    }
    </script>
@endsection