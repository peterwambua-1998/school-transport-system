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

    .span-delete:hover {
        cursor: pointer;
    }
    .card-header {
        border-top: 1px solid rgba(0,0,0,.125);
        border-radius: 0.25rem;
        
        border-left: 1px solid rgba(0,0,0,.125);
        border-right: 1px solid rgba(0,0,0,.125);
        
    }
</style>
@endsection
@section('content')


<div class="vihcleGrid card-header">

    <div>
        <h5 ><span style="font-weight:300;">Term Holidays</span> - <span style="font-weight:500;">Management</span></h5>
    </div>
    
   

   
    
    
</div>


    <div class="row">
        
        <div class="col-sm-12">
            <div class="card tabs-card">
                
                <div class="tab-content card-block">
                    <div class="tab-pane active" id="home3" role="tabpanel">
    
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" style="border: 1px solid gray;" id="vehTable">
                                <thead style="background-color: #0071f3; color: #fff">
                                    <tr>
                                       
                                       
                                        <th>Name</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($terms as $term)
                                        
                                    
                                    <tr>
                                        
                                        <td>{{$term->name}}</td>
                                        <td>{{$term->start}}</td>
                                        <td>{{$term->ends}}</td>
                                        
                
                                        
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
                    icon: 'eroor',
                    title: '{{ Session::get("unsuccess") }}',
                    showConfirmButton: false,
                    timer: 2500
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

    </script>
@endsection