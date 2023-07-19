@extends('layouts.app')
@push('plugin-styles')
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
      <li class="breadcrumb-item"><a href="{{route('license.index')}}">License</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('license.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
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
                <h4 class="card-title">Add {{$driver->name }} License</h4>
                <hr>
                <form action="{{ route('license.store') }}"  method="POST" id="my-form">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="name">Driver Name</label>
                            <input readonly type="text" name="name" class="form-control" id="name" placeholder="Driver Name" value="{{$driver->name}}" required>
                            <input type="hidden" name="driver_id" value="{{$driver->id}}">
                        </div>

                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="dl_number">DL Number</label>
                            <input type="number" name="dl_number"  class="form-control" id="dl_number" placeholder="DL Number" required>
                            <span class="issue" id="dl_number_error"></span>
                        </div>
                        
                    </div>


                    <div class="row">
                        <div class="mb-3 col-md-6 col-sm-12">
                            <label class="form-label" for="dl_class">DL Class</label>
                            <input type="text" name="dl_class" class="form-control" id="dl_class" required>
                            <span class="issue" id="dl_class_error"></span>
                        
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="date_issued">Date Issued</label>
                            <input type="date" name="date_issued" class="form-control" id="date_issued" required>
                            <span class="issue" id="date_issued_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="date_renewed">Date Renewed</label>
                            <input type="date" name="date_renewed" class="form-control" id="date_renewed" required>
                            <span class="issue" id="date_renewed_error"></span>
                        
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="validity">Validity @if($notificationSetting)({{$notificationSetting->license_unit}}) @endif</label>
                            <input type="number" name="validity" class="form-control" id="validity" required>
                            <span class="issue" id="validity_error"></span>
                            {{-- -1.2645160084966023, 36.95933317127831 --}}
                        </div>
                    </div>

                    
                    <div class="text-center">
                        <button type="button" id="submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Add License</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>
    

@endsection


@push('custom-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        

        $('#submit-btn').on('click', function(e) {
            if (!$('#dl_number').val()) {
                $('#dl_number').focus();
                $('#dl_number_error').text('field required');
                e.preventDefault();
                return;
            } else {
                $('#dl_number_error').text('');
            }

            if (!$('#dl_class').val()) {
                $('#dl_class').focus();
                $('#dl_class_error').text('field required');
                e.preventDefault();
                return;
            } else {
                $('#dl_class_error').text('');
            }

            if (!$('#date_issued').val()) {
                $('#date_issued').focus();
                $('#date_issued_error').text('field required');
                e.preventDefault();
                return;
            } else {
                $('#date_issued_error').text('');
            }

            if (!$('#date_renewed').val()) {
                $('#date_renewed').focus();
                $('#date_renewed_error').text('field required');
                e.preventDefault();
                return;
            } else {
                $('#date_renewed_error').text('');
            }

            if (!$('#validity').val()) {
                $('#validity').focus();
                $('#validity_error').text('field required');
                e.preventDefault();
                return;
            } else {
                $('#validity_error').text('');
            }

            $('#my-form').submit();
        })
    })
</script>
@endpush