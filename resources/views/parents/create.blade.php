@extends('layouts.app')

@push('plugin-styles')
    <script src="{{ asset('js/intlTelInput.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}"></script>
    <link href="{{ asset('css/intlTelInput.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
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
      <li class="breadcrumb-item"><a href="{{route('parents.index')}}">Parent</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>
    
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('parents.index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon>Cancel</a>
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
                <h4 class="card-title">Add Parent</h4>
                <hr>
                <form action="{{ route('parents.store') }}"  method="POST" id="my-form" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label  class="form-label"  for="title">Name</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Full Name" required>
                            <span class="issue" id="name_error"></span>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label  class="form-label"  for="title">Email</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
                            <span class="issue" id="email_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label  class="form-label"  for="title">ID Number</label>
                            <input type="number" name="id_number" class="form-control" id="id_number" placeholder="ID Number" required>
                            <span class="issue" id="id_number_error"></span>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label  class="form-label"  for="title">Phone Number</label><br>
                            <input type="number"  class="form-control" id="mobile_code" placeholder="Phone Number" required style="width:100%">
                            <input type="hidden" name="phone_number" class="form-control" id="mobile_code_submitdata" placeholder="Phone Number" required style="width:100%">
                            <br>
                            <span class="issue" id="mobile_code_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="exampleFormControlFile1">Photo</label>
                            <br>
                            <input type="file" class="form-control-file" id="myDropify" name="image">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="">Gender</label>
                            <select class="form-select" name="gender" id="gender">
                                <option>select...</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <span class="issue" id="gender_error"></span>
                        </div>
                    </div>

                    <div class="text-center">
                        <button id="submit-btn" type="button" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Add Parent</button>
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
<script src="{{ asset('assets/js/dropify.js') }}"></script>
<script defer>
    $(function() {


            
        $(window).keydown(function(event){
            if(event.keyCode == 13) {
            event.preventDefault();
            return false;
            }
        });

        var inp = document.getElementById('mobile_code');

        var iti = window.intlTelInput(inp,{
            initialCountry: "ke",
            separateDialCode: true,
           
        });

        inp.addEventListener('change', function () {
            var number = iti.getNumber();

            $('#mobile_code_submitdata').val(number);
            console.log(number);
        });


        $('#submit-btn').on('click',(e)=>{
            if(!$('#name').val()) {
                $('#name').focus();
                $('html, body').animate({
                    scrollTop: "0px"
                }, 800);
                e.preventDefault();
                $('#name_error').text('field required');
                return;
            } else{
                $('#name_error').text('');
            }

            if(!$('#email').val()) {
                $('#email').focus();
                $('html, body').animate({
                    scrollTop: "0px"
                }, 800);
                e.preventDefault();
                $('#email_error').text('field required');
                return;
            } else{
                $('#email_error').text('');
            }

            if(!$('#id_number').val()) {
                $('#id_number').focus();
                e.preventDefault();
                $('#id_number_error').text('field required');
                return;
            } else{
                $('#id_number_error').text('');
            }

            if(!$('#mobile_code').val()) {
                $('#mobile_code').focus();
                e.preventDefault();
                $('#mobile_code_error').text('field required');
                return;
            } else{
                $('#mobile_code_error').text('');
            }

            if($('#gender').find(':selected').text() == 'select...') {
                $('#gender').focus();
                e.preventDefault();
                $('#gender_error').text('field required');
                return;
            } else{
                $('#gender_error').text('');
            }


            $('#my-form').submit();

        })

       
    });
</script>
@endpush