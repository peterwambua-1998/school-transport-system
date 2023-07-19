@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <style>
    table {
        border-top-color: rgb(203 213 225);
        border-top-width: 2px;
        border-top-style: solid;
    }
  </style>
@endpush
@section('content')
<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 75%">
      <li class="breadcrumb-item"><a href="#">Attendance</a></li>
      <li class="breadcrumb-item active" aria-current="page">List</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">  
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Attendance Today</h6>
                <hr>
                <div>
                    <ul  class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
                        <li class="nav-item text-center" style="width: 50%; ">
                        <a class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" role="tab" aria-controls="home" aria-selected="true">Am Attendance</a>
                        </li>
                        <li class="nav-item text-center" style="width: 50%; ">
                        <a class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" role="tab" aria-controls="profile" aria-selected="false">Pm Attendance</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="lineTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        @include('attendance.includes.am')
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        @include('attendance.includes.pm')
                        </div>
                    </div>
                </div>
        

            </div>
        </div>
    </div>
</div>


@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/data-table.js') }}"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script defer>
    $( function() {
        $( document ).tooltip();
    } );
    $(document).ready( function () {


        document.getElementById('vehTable_wrapper').style.marginBottom  = '20px';

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
        }
    } );

</script>
@endpush

{{--
@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css">
<style>

    body {
        font-size: 14px;
    }
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
        background: #0071f3;
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

    .inv-link {
        color: black;
    }

    .inv-link:hover {
        color: #0071f3;
    }

    .overlay{
        display: none;
        position: fixed;
        width: 100vw;
        height: 100vh;
        top: 0;
        left: 0;
        z-index: 999;
        background: rgba(255,255,255,0.8) url("loader.gif") center no-repeat;
    }

    body.loading{
    overflow: hidden;   
    }
   
    body.loading .overlay{
        display: block;
    }

    th, td {
        font-size: 14px;
    }
    .nav-link  {
        color: #0071f3;
        
    }

    .nav-link:hover {
        color: #023570;
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

</style>
@endsection
@section('content')
<div class="top-navigation">

    
        <p ><span >Todays Attendance</span> - <span style="font-weight:500;">List</span></p>
   
    
    
    
</div>

<section id="tabs" class="project-tab">
    
    <div class="row">
        <div class="col-md-12">
            <nav>
                <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" style="border-bottom: 1px solid rgba(0,0,0,.125)" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">AM Attendance</a>
                    <a class="nav-item nav-link" style="border-bottom: 1px solid rgba(0,0,0,.125)" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">PM Attendance</a>
                    
                </div>
            </nav>
            <div class="tab-content p-4" id="nav-tabContent" style="background: #fff">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <div class="table-responsive">
                        
                            <p class="table-title-vehicle"><span style="color: #0071f3">AM Atendance</span> - List Viewer</p>
                        
                        
                        @include('attendance.includes.am')
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="table-responsive">
                        
                            <p class="table-title-vehicle"><span style="color: #0071f3">PM Atendance</span> - List Viewer</p>
                        
                        
                        @include('attendance.includes.pm')
                    </div>
                </div>
                
            </div>
        </div>
    </div>

</section>


<div class="page-wrapper">
    <ul class="nav nav-pills nav-fill" >
        <li class="nav-item">
            <a class="nav-link inv-link active" href="#">This Month</a>
        </li>
        <li class="nav-item">
            <a class="nav-link inv-link" href="#">Longer nav link</a>
        </li>
        <li class="nav-item">
            <a class="nav-link inv-link" href="#">Link</a>
        </li>
        <li class="nav-item">
            <a class="nav-link inv-link" href="#">Disabled</a>
        </li>
    </ul>

    <div class="row">
        
        <div class="col-sm-12">
            <div class="card tabs-card">
                
                <div class="tab-content card-block">
                    <div class="tab-pane active" id="home3" role="tabpanel">
    
                        <div class="table-responsive">
                            <p class="table-title-vehicle"><span style="color: #0071f3">Invoice</span> - List Viewer</p>
                            <table class="table table-striped" style="border: 1px solid gray;" id="vehTable" >
                                <thead style="background-color: #0071f3; color: #fff">
                                    <tr>
                                       
                                        <th>Inv No</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                        <th>Total</th>

                                        <th>Status</th>
                                        <th>Parent</th>
                                        <th>Student</th>
                                        <th>Date</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($invoices as $invoice)
                                        @php 
                                        $amtReceipt = 0;

                                        foreach ($invoice->receipt as $receipt) {
                                            $amtReceipt += $receipt->amount;
                                        }
                                        $invAmt = $invoice->amount;

                                        $balance = $invAmt - $amtReceipt;

                                        $parent = App\User::where('id', '=', $invoice->parent_id)->first();
                                        $student = App\Student::where('id', '=', $invoice->student_id)->first();

                                        $date=date_create($invoice->created_at);

                                        $fDate = date_format($date,"Y/m/d");
                                        @endphp

                                        @if ($amtReceipt < $invAmt)
                                        <tr>
                                            <td>{{ $invoice->inv_num }}</td>
                                            
                                            <td>{{ $amtReceipt }}</td>
                                            <td>{{ $balance }}</td>
                                            <td>{{ $invAmt }}</td>
                                            <td>{{ $invoice->status }}</td>
                                            <td>{{ $parent->name }}</td>
                                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                            <td>{{ $fDate }}</td>
                                        </tr>
                                        @endif
                                        
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

</div>

<div class="overlay">
    <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
@endsection


@section('js')
    <script defer src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer>
        $(document).ready( function () {
            $('#vehTable').DataTable({
                language: { searchPlaceholder: "Search records", search: "", ordering: false},
            });


            $('#pmattendance').DataTable({
                language: { searchPlaceholder: "Search records", search: "", ordering: false},
            });

            $('#vehTable3').DataTable({
                language: { searchPlaceholder: "Search records", search: "","ordering": false},
            });


            if("{{ Session::has('success') }}") {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ Session::get("success") }}',
                    showConfirmButton: false,
                    timer: 1500
                })
               
            } else if ("{{ Session::has('unsuccess') }}") {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ Session::get("unsuccess") }}',
                    showConfirmButton: false,
                    timer: 1500
                })
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

        /*
        $('#genInv').on('click', function() {

            
           

            var data = new FormData;
            data.append('_token','{{csrf_token()}}');

            Swal.fire({
                title: 'Are you sure?',
                text: "Ensure its a new month before genrating invoices to avoid duplicates",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Generate',
                cancelButtonText: 'Cancel',
                

            }).then((result) => {

                if (result.isConfirmed) {
                    $("body").addClass("loading");
                    $.ajax({
                        type: "POST",
                        url: "{{route('invoice_store')}}",
                        processData: false,
                        contentType: false,
                        cache: false,
                        data: data,
                        
                        error: function(data){
                            console.log(data);
                        },
                        success: function (message) {
                            $("body").removeClass("loading"); 
                            location.reload();
                        }
                    });
               
                } 
            });
        });

        */

    </script>
@endsection
--}}