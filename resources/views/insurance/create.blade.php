@extends('layouts.app')
@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
<style>
    .my-nav {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .map-div {
        height: 40vh;
    }

    #map {
        width: 100%;
        height: 100%;
    }

    #pac-input {
        background-color: #fff;
        font-family: "Roboto", Helvetica, sans-serif;
        font-size: 15px;
        font-weight: 400;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 400px;
    }

    #pac-input:focus {
        border-color: #4d90fe;
    }

    .label-marker {
        position: absolute;
        top: 0;
        left: -40px;
        background: #FEDB00;
        padding: 3px;
        border-radius: 0.125rem;
    }

    .controls {
        position: absolute;
        margin-top: 10px;
        left: 35vw;
        background-color: #fff;
        border-radius: 2px;
        border: 1px solid transparent;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        box-sizing: border-box;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        height: 40px;
        margin-left: 10px;
        outline: none;
        padding: 0 11px 0 13px;
        z-index: 10;
        width: 400px;
    }

    .controls:focus {
        border-color: #4d90fe;
    }

    .issue {
        color: #ff3366;
    }
</style>
@endpush
@section('content')


<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('insurance.index')}}">Vehicle insurance</a></li>
      <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('insurance.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
    </div>
</nav>


@if (Session::has('success'))
    <div class="alert alert-success" role="alert" id="success">
       {{Session::get('success')}}
    </div>
@endif

@if (Session::has('unsuccess'))
    <div class="alert alert-danger" role="alert" id="danger">
        {{Session::get('unsuccess')}}
    </div> 
@endif

@if ($errors->any())
<div class="alert alert-danger">
<ul>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>
</div>
@endif

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add {{$vehicle->plate_num}} insurance</h4>
                <hr>
                <form action="{{ route('insurance.store') }}"  method="POST" id="my-form">
                    @csrf
                    <input type="hidden" name="vehicle_id" value="{{$vehicle->id}}">

                    <div class="row">
                       
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="type">Insurance Type</label>
                            <select name="type" class="form-select" id="type" required>
                                <option>select...</option>
                                <option value="Comprehensive">Comprehensive</option>
                                <option value="Third party">Third party</option>
                            </select>
                            <span class="issue" id="type_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">Insurance number</label>
                            <input type="text" name="ins_num" class="form-control" id="ins_num" placeholder="54321" required>
                            <span class="issue" id="ins_num_error"></span>
                        </div>
                    </div>
                    <div class="row">
                       
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="company">Insurance Company</label>
                            <input type="text" placeholder="company" name="company" class="form-control" id="company" required>
                            <span class="issue" id="company_error"></span>
                        </div>

                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="issue_date">Issue Date</label>
                            <input type="date"  name="issue_date" class="form-control" id="issue_date" required>
                            <span class="issue" id="issue_date_error"></span>
                        </div>
                        
                    </div>
                    <div class="row">
                       
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="exp_date">Date Renewed</label>
                            <input type="date"  name="date_renewed" class="form-control" id="date_renewed" required>
                            <span class="issue" id="date_renewed_error"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="validity">Validity (Days)</label>
                            <input type="number" name="validity" id="validity" class="form-control">
                            <span class="issue" id="validity_error"></span>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="button" id="my-submit" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Add Insurance</button>
                    </div>
        
                </form>
            </div>
        </div>
    </div>
</div>
    

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush
@push('custom-scripts')
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script defer>
    $(function() {
        $('#my-submit').on('click',(e) => {
            if ($('#type').find(':selected').text() == 'select...') {
                $('#type_error').text('field required');
                e.preventDefault();
                $('#type').foucs()
                return;
            } else {
                $('#type_error').text('');
            }


            if (!$('#ins_num').val()) {
                $('#ins_num_error').text('field required');
                e.preventDefault();
                $('#ins_num').foucs()
                return;
            } else {
                $('#ins_num_error').text('');
            }


            if (!$('#company').val()) {
                $('#company_error').text('field required');
                e.preventDefault();
                $('#company').foucs()
                return;
            } else {
                $('#company_error').text('');
            }


            if (!$('#issue_date').val()) {
                $('#issue_date_error').text('field required');
                e.preventDefault();
                $('#issue_date').foucs()
                return;
            } else {
                $('#issue_date_error').text('');
            }

            if (!$('#date_renewed').val()) {
                $('#date_renewed_error').text('field required');
                e.preventDefault();
                $('#date_renewed').focus()
                return;
            } else {
                $('#date_renewed_error').text('');
            }

            if (!$('#validity').val()) {
                $('#validity_error').text('field required');
                e.preventDefault();
                $('#validity').focus()
                return;
            } else {
                $('#validity_error').text('');
            }
            
            $('#my-form').submit();
        })
    })
</script>
@endpush