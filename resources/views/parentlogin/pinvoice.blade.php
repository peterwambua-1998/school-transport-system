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
    <p style="font-size: 16px;"><span>Paid invoice</span> - <span style="font-weight:500;">Management</span></p>
</div>



    

    <div class="row">
        
        <div class="col-sm-12">
            <div class="card tabs-card">
                
                <div class="tab-content card-block">
                    <div class="tab-pane active" id="home3" role="tabpanel">
    
                        <div class="table-responsive">
                            <p class="table-title-vehicle"><span style="color: #0071f3">Invoice</span> - List Viewer</p>
                            <table class="table table-striped" style="border: 1px solid gray;" id="vehTable">
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

                                        $parent = App\Models\User::where('id', '=', $invoice->parent_id)->first();
                                        $student = App\Models\Student::where('id', '=', $invoice->student_id)->first();

                                        $date=date_create($invoice->created_at);

                                        $fDate = date_format($date,"Y/m/d");
                                        @endphp

                                        @if ($amtReceipt == $invAmt)
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
                language: { searchPlaceholder: "Search records", search: "",},
            });
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