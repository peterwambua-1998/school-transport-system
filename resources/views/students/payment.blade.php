@extends('layouts.app')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
    <style>
        .my-nav {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
    </style>
@endpush

@section('content')

<nav class="page-breadcrumb my-nav" >
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('students.index')}}">Students</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>
    
  
    <div style="display: flex; flex-direction: row-reverse;">
        <a href="{{route('students.show', Crypt::encrypt($student->id))}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon>Cancel</a>
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
                <h4 class="card-title">Add Payment</h4>
                <hr>
                <h6 class="mb-1"><span class="text-success">Amount:</span> {{$settings->currency}} {{number_format($total, 2)}}</h6><br>
                <h6 ><span class="text-warning">Balance:</span> {{$settings->currency}} {{number_format($balance, 2)}}</h6>

                <hr>
                <form action="{{ route('store_payment') }}"  method="POST" id="studentForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="student_id" value="{{$student->id}}">
                    <input type="hidden" name="student_fee_id" value="{{$student_fee->id}}">
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="receipt_number">Receipt Number</label>
                            <input type="text" name="receipt_number" class="form-control" id="receipt_number" placeholder="receipt number" required>
                            <span class="text-danger" id="receipt_number_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="amount_paid">Amount Paid</label>
                            <input type="number" name="amount_paid" class="form-control" id="amount_paid" placeholder="0" required>
                            <span class="text-danger" id="amount_paid_error"></span>
                        </div>
                    </div>
                    
                    <div class="row">
                        
                        <div class="col-md-6">
                            <label class="form-label" for="">Payment Method</label>
                            <select class="form-select" name="payment_method" id="payment_method">
                                <option>select...</option>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                                <option value="mpesa">Mpesa</option>
                            </select>
                            <span class="text-danger" id="payment_method_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">Date Paid</label>
                            <input type="date" name="date_paid" class="form-control" id="date_paid" required>
                            <span class="text-danger" id="date_paid_error"></span>
                        </div>

                    </div>
                  
                    <div class="text-center">
                        <button type="button" id="submit-btn" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Add Payment</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>
    

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/dropify.js') }}"></script>
<script>
    $(function() {
        $('#submit-btn').on('click', () => {
            if(!$('#receipt_number').val()) {
                $('#receipt_number').focus();
                $('#receipt_number_error').text('field required');
                return;
            } else {
                $('#receipt_number_error').text('');
            }

            

            if(!$('#amount_paid').val()) {
                $('#amount_paid').focus();
                $('#amount_paid_error').text('field required');
                return;
            } else {
                $('#amount_paid_error').text('');
            }

            if($('#payment_method').find(':selected').text() == 'select...') {
                $('#payment_method').focus();
                $('#payment_method_error').text('field required');
                return;
            } else {
                $('#payment_method_error').text('');
            }

            if(!$('#date_paid').val()) {
                $('#date_paid').focus();
                $('#date_paid_error').text('field required');
                return;
            } else {
                $('#date_paid_error').text('');
            }

            $('#studentForm').submit();

        })
    })
</script>
@endpush