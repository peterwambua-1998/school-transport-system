@extends('layouts.app')
@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
<style>
    .my-nav {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }
    .issue {
        color: #ff3366;
    }
</style>
@endpush
@section('content')


<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('warranty.index')}}">Vehicle warranty</a></li>
      <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('warranty.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
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
                <h4 class="card-title">Update Vehicle warranty</h4>
                <hr>
                <form action="{{ route('warranty.update', $warranty->id) }}"  method="POST" id="my-form">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="title">Bus Reg. No</label>
                            <input readonly type="text" class="form-control" name="vehicle" id="" value="{{$vehicle->plate_num}}">
                            <input type="hidden" name="vehicle_id" value="{{$vehicle->id}}">
                        </div>
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="measurement">Measurement Unit</label>
                            <select name="measurement" id="measurement" class="form-select">
                                <option>select...</option>
                                <option @if ($warranty->measurement == 'km')
                                    selected
                                @endif value="km">KM</option>
                                <option @if ($warranty->measurement == 'years')
                                    selected
                                @endif value="years">Years</option>
                            </select>
                            <span class="issue" id="measurement_error"></span>
                        </div>
                        
                    </div>
                    <div class="row">
                       
                      
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="waranty_value">Warranty Value</label>
                            <input type="number" placeholder="0" name="waranty_value" id="waranty_value" class="form-control" value="{{$warranty->waranty_value}}">
                            <span class="issue" id="waranty_value_error"></span>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="dealer">Dealer</label>
                            <input type="text" placeholder="Dealer" name="dealer" id="dealer" class="form-control" value="{{$warranty->dealer}}">
                            <span class="issue" id="dealer_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="status">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option>select...</option>
                                <option @if ($warranty->status == 'active')
                                    selected
                                @endif value="active">Active</option>
                                <option @if ($warranty->status == 'inactive')
                                    selected
                                @endif value="inactive">Inactive</option>
                            </select>
                            <span class="issue" id="status_error"></span>
                        </div>

                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="type">Type</label>
                            <select name="type" id="type" class="form-select">
                                <option>select...</option>
                                <option @if ($warranty->type == 'vehicle')
                                    selected
                                @endif value="vehicle">Vehicle</option>
                                <option @if ($warranty->type == 'parts')
                                    selected
                                @endif value="parts">Parts</option>
                            </select>
                            <span class="issue" id="type_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12" id="extra_input">

                        </div>
                    </div>

                    <div class="text-center">
                        <button id="submit-btn" type="button" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Update Warranty</button>
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
        let type_value = $('#type').find(":selected").val();

        if (type_value == 'parts') {
            $('#extra_input').children().remove();
            let tem = `
                <label class="form-label">Parts Description</label>
                <textarea class="form-control" name="parts_description" id="parts_description" value="{{$warranty->warranty_parts}}"></textarea>
                <span class="issue" id="parts_desc_error"></span>
            `;
            $('#extra_input').append(tem); 
        }

        $('#type').on('change',(e) => {
            let value = $('#type').find(":selected").val();
            if (value == 'parts') {
                $('#extra_input').children().remove();
                let tem = `
                    <label class="form-label">Parts Description</label>
                    <textarea class="form-control" name="parts_description" id="parts_description"></textarea>
                    <span class="issue" id="parts_desc_error"></span>
                `;
                $('#extra_input').append(tem);
            }
        })

        $('#submit-btn').on('click',(e) => {
            
            if($('#measurement').find(':selected').text() == 'select...'){
                $('#measurement_error').text('field required');
                e.preventDefault();
                $('#measurement').focus();
                return;
            } else {
                $('#measurement_error').text('');
            }

            if(!$('#waranty_value').val()){
                $('#waranty_value_error').text('field required');
                e.preventDefault();
                $('#waranty_value').focus();
                return;
            } else {
                $('#waranty_value_error').text('');
            }

            if(!$('#dealer').val()){
                $('#dealer_error').text('field required');
                e.preventDefault();
                $('#dealer').focus();
                return;
            } else {
                $('#dealer_error').text('');
            }

            if($('#status').find(':selected').text() == 'select...'){
                $('#status_error').text('field required');
                e.preventDefault();
                $('#status').focus();
                return;
            } else {
                $('#status_error').text('');
            }

            if($('#type').find(':selected').text() == 'select...'){
                $('#type_error').text('field required');
                e.preventDefault();
                $('#type').focus();
                return;
            } else {
                $('#type_error').text('');
            }

            if ($('#type').find(':selected').val() == 'parts') {
                if (!$('#parts_description').val()) {
                    $('#parts_description').focus();
                    $('#parts_desc_error').text('field required');
                    e.preventDefault();
                    return;
                } else {
                    $('#parts_desc_error').text('');
                }
            }

            $('#my-form').submit();
        });
    });
</script>
@endpush