@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
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

    .select2-container--default .select2-selection--single {
        height: fit-content !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        background: #F6F7FB !important;
    }

    .msg-btn {
        background: #0071f3;
        color: #fff;
    }

    .msg-btn:hover {
        background: #012549;
    }

    .top-navigation {
        padding-left: 15px;
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


<div class="top-navigation">

    
        <p><span >Send Message To</span> - <span style="font-weight:500;">Parent</span></p>
   

    
</div>
<div class="page-wrapper">

    <div class="row">
        
        <div class="col-sm-12">
            <div class="card tabs-card">
                
                <div class="tab-content card-block" >
                    
                    <form action="{{ route('pnotification_send') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="parent_id">Select Parent</label>
                            <select class="form-control select2 " name="parent_id" id="parent_id" >
                                <option value="">---</option>
                                @foreach($parents as $id => $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="msgheader">Message Header</label>
                            <input type="text" name="msg_header" class="form-control" style="background: #F6F7FB">
                        </div>

                        <div class="form-group">
                            <label for="">Message Body</label>
                            <br>
                            <textarea name="msg_body" style="width: 100%; background: #F6F7FB" rows="12"></textarea>
                        </div>

                        <button class="btn btn-block msg-btn">Send Message</button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>

</div>
@endsection


@section('js')

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script defer>
        $('.select2').select2();


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
                    icon: 'success',
                    title: '{{ Session::get("unsuccess") }}',
                    showConfirmButton: false,
                    timer: 2500
                });
            } else if ("{{ Session::has('errors') }}") {
                Swal.fire(
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ Session::get("errors") }}',
                    showConfirmButton: false,
                    timer: 2500
                )
            }
    </script>
@endsection