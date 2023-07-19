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
        font-size: 16px;
    }
    
    #map {
        width: 100%;
        height: 80vh;
    }
</style>
@endsection
@section('content')

<div class="top-navigation" style="background: #e2e8f0">
    <p><span >Confirm {{ $student->first_name }}</span> - <span style="font-weight:500;">Pickup/Drop off</span></p>
    <a class="btn btn-create" style="float: right;border-radius:5px" onclick="confirmed()">Confirm</a>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="card">
            <div class="card-body">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>


@endsection


@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.js"></script>
<script defer src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js" ></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiyrRpT1Rg7EUpZCUAKTtdw3jl70UzBAU&callback=initMap&v=weekly" defer></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script defer>
        $( function() {
            $( document ).tooltip();
        } );
        $(document).ready( function () {
           
            
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

        function initMap() {
            var lat = "{{ $settings->lat }}" - 0;
            var lng = "{{ $settings->lng }}" - 0;

            var studentLat = '{{ $student->lat }}' - 0;
            var studentLng = '{{ $student->lng }}' - 0;

            const myLatLng = { lat: lat, lng: lng };

            var studentPD = { lat:studentLat, lng: studentLng };

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 14,
                center: studentPD,
            });

            const marker = new google.maps.Marker({
                position: studentPD,
                map,
                title: "{{ $student->first_name }} Pickup/Drop off point",
            });

            marker.addListener("click", () => {
                infoWindow.close();
                infoWindow.setContent(marker.getTitle());
                infoWindow.open(marker.getMap(), marker);
            });
        }

        window.initMap = initMap;

        function confirmed() {

            var data = new FormData();
            data.append('_token', '{{ csrf_token() }}');
            data.append('student_id', '{{ $student->id }}' - 0);

            $.ajax({
                type: "POST",
                url: "{{route('confirmedpickup')}}",
                processData: false,
                contentType: false,
                cache: false,
                data: data,
                        
                error: function(data){
                    console.log(data);
                },
                success: function (response) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: response,
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            })
        }

    </script>
@endsection