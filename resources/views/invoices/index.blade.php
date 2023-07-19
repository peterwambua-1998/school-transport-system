
{{--  
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

    .nav-link  {
        color: #0071f3;
        
    }

    .nav-link:hover {
        color: #023570;
    }

    .active {
        
    }

    .date-form {

    }

    .top-navigation {
        padding: 10px;
        border-radius: .25rem;
        border: 1px solid rgba(0,0,0,.125);
        margin-bottom: 15px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        
    }

    .top-navigation p {
        flex-grow: 8;
        position: relative;
        top: 5px;
        letter-spacing: 1px;
        font-size: 16px;
    }

    form {
        padding: 0;
        margin: 0;
    }


</style>
@endsection
@section('content')
<div class="top-navigation">

    <div>
        <p ><span>Invoice</span> - <span style="font-weight:500;">Management</span></p>
    </div>
    
   

    
        <form action="{{ route('invoice_store') }}" method="post" id="invoiceGen" style="">
            @csrf
            <button class="btn btn-create" id="geninvoice" id="genInv" style="float: right;border-radius:5px; color: #fff">
                Generate Invoices
            </button>
        </form>
        
    
    
    
</div>


<div class="">
    <div class="card">
        <div class="page-w card-header p-1 m-0" >
            <form action="">
                <div class="form-row p-0 m-0">
                <div class="form-group col-md-5 m-0">
                    <label for="">from</label>
                    <input type="date" class="form-control" name="from" id="from" >
                    <p style="color: red" class="from-warning"></p>
                </div>
                <div  class="form-group col-md-5 m-0">
                    <label for="">to</label>
                    <input type="date" class="form-control" name="from" id="to">
                    <p style="color: red" class="to-warning"></p>
                </div>
                <div class="form-group col-md-2 m-0">
                    <label for="" style="visibility: hidden">cl</label><br>
                    <button type="button" class="btn get-data" style="background: #0071f3; color: #fff">Submit</button>
                </div>
                </div>
            </form>
        </div>
    </div>
<section id="tabs" class="project-tab">
    
        <div class="row">
            <div class="col-md-12">
               
                <div class="tab-content card p-2" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="table-responsive">
                            <p class="table-title-vehicle"><span style="color: #0071f3">Invoice</span> - List Viewer <span class="pl-5 total"></span> <span class="pl-5 totalBalance"></span></p>
                            @include('invoices.includes.table1currentmonth')
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>

        
  
</section>
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
            $('#invoiceGen').one('submit', function() {
                $(this).find('#geninvoice').attr('disabled', 'disabled');
            });


            $.ajax({
                type: "GET",
                url: "{{ route('invoice_index') }}",
                processData: false,
                contentType: false,
                cache: false,
               
                error: function (err) {
                    console.log(err)
                },
                success: function (response) {
                 
                 $('tbody').html(response['table']);
                 $('.total').text('Total Paid: {{ $settings->currency ?? "USD" }} ' + response['total']);
                 $('.totalBalance').text('Total Balance: {{ $settings->currency ?? "USD" }} ' + response['totalBalance']);
                 $('#vehTable').DataTable({
                    searching: false
                 });

                }
            });



            


            function getData() {
                var from = $('#from').val();
                var to = $('#to').val();

                

                
                if (! from) {
                    $('.from-warning').text('please eneter date');
                    return;
                }

                if (! to) {
                    $('.to-warning').text('please eneter date');
                    return;
                }

                if (! from && ! to) {
                    $('.from-warning').text('please eneter date');
                    return;
                }

                var data = new FormData;
                data.append('_token', '{{ csrf_token() }}');
                data.append('from', from);
                data.append('to', to);

                $.ajax({
                    type: "POST",
                    url: "{{ route('invoice_search') }}",
                    processData: false,
                    contentType: false,
                    cache: false,
                    data: data,
                    error: function (err) {
                        console.log(err)
                    },
                    success: function (response) {
                        console.log(response);
                        $('tbody').html(response['table']);
                        $('.total').text('{{ $settings->currency ?? "USD" }} ' + response['total']);
                        $("#vehTable").dataTable().fnDestroy();
                        $('#vehTable').DataTable({
                            searching: false
                        });

                    }
                });
                
            }

            $('.get-data').on('click', getData);


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


        $('#genInv').on('click', function() {

            
           

            var data = new FormData;
            data.append('_token','{{csrf_token()}}');

            Swal.fire({
                title: 'Are you sure?',
                text: "Ensure its a new week before genrating invoices to avoid duplicates",
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

    </script>
@endsection
--}}