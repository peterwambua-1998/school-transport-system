@extends('layouts.app')
@push('plugin-styles')
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
      <li class="breadcrumb-item"><a href="{{route('staff_index')}}">Staff</a></li>
      <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>
  
    <div style="display: flex; flex-direction: row-reverse;">
      <a href="{{route('staff_index')}}" class="btn btn-warning"><ion-icon style="position: relative; top:3px; right: 5px; color: #000; font-size: 16px" name="close-circle-outline"></ion-icon> Cancel</a>
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
                <h4 class="card-title">Create Users</h4>
                <hr>
                <form action="{{ route('staff_store') }}" id="my-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="title">Full Name</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Name" required>
                            <span class="issue" id="name_error"></span>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="title">Staff Number</label>
                            <input type="text" name="staff_num" class="form-control" id="staff_num" placeholder="Staff number" required>
                            <span id="err_message_staff" class="text-danger mt-1" style="font-size:13px"></span>
                            <span class="issue" id="staff_num_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" class="form-label" for="platenum">Phone Number</label>
                            <input type="text" name="phone_num" class="form-control" id="phone_num" placeholder="+254700000000" required>
                            <span id="err_message_phone" class="text-danger mt-1" style="font-size:13px"></span>
                            <span class="issue" id="phone_num_error"></span>
                            
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="inputState">Staff Role</label>
                            <select id="role" class="form-select role" name="user_type" required onchange="getval(this);">
                                <option>select...</option>
                                @foreach ($roles as $role)
                                    <option value="{{$role->role}}">{{$role->role_name}}</option>
                                @endforeach
                            </select>
                            <span class="issue" id="user_type_error"></span>

                        </div>

                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">Email</label>
                            <input type="email" name="email" class="form-control  @error('email') is-invalid @enderror" id="d_email" placeholder="Enter Email" required>

                            <span id="err_message" class="text-danger mt-1" style="font-size:13px"></span>
                            <span class="issue" id="email_error"></span>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">ID Number</label>
                            <input type="number" name="id_num" class="form-control"  id="id_number" placeholder="ID Number" required>
                            <span id="err_message_id" class="text-danger mt-1" style="font-size:13px"></span>
                            <span class="issue" id="id_number_error"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">
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
                        <button id="submit-btn" type="button" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Register Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>      
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/dropify.js') }}"></script>
<script defer>
    $(function() {
        function validateEmail(email) {
            let d = new FormData;
            d.append('_token',"{{csrf_token()}}");
            d.append('email',email);
            console.log('peter');
            $.ajax({
                type: 'post',
                url: '/validate-email',
                processData: false,
                contentType: false,
                cache: false,
                data: d,
                error: function(err) {
                    console.log(err.responseJSON.message);
                    let msg = err.responseJSON.message.replace('(and 1 more error)','');
                    $('#err_message').text(msg);
                    $('#submit-btn').hide('slow');
                },
                success: function(response) {
                    $('#err_message').text('');
                    $('#submit-btn').show('slow');

                }
            })
        }

        $('#d_email').on('input', (e) => {
            validateEmail($('#d_email').val());
        });
        
        function validateStaffNumber(st_num) {
            let d = new FormData;
            d.append('_token',"{{csrf_token()}}");
            d.append('staff_num',st_num);

            $.ajax({
                type: 'post',
                url: '/validate-stnum',
                processData: false,
                contentType: false,
                cache: false,
                data: d,
                error: function(err) {
                    $('#err_message_staff').text('The staff number has already been taken.');
                    $('#submit-btn').hide('slow');

                },
                success: function(response) {
                    $('#err_message_staff').text('');
                    $('#submit-btn').show('slow');

                }
            })
        }


        $('#staff_num').on('input', (e) => {
            validateStaffNumber($('#staff_num').val());
        });


        function validateIDNumber(id_num) {
            let d = new FormData;
            d.append('_token',"{{csrf_token()}}");
            d.append('id_num',id_num);

            $.ajax({
                type: 'post',
                url: '/validate-idnum',
                processData: false,
                contentType: false,
                cache: false,
                data: d,
                error: function(err) {
                    $('#err_message_id').text('The ID number has already been taken.');
                    $('#submit-btn').hide('slow');

                },
                success: function(response) {
                    $('#err_message_id').text('');
                    $('#submit-btn').show('slow');

                }
            })
        }


        $('#id_number').on('input', (e) => {
            validateIDNumber($('#id_number').val());
        });



        function validatePhone(p_num) {
            let d = new FormData;
            d.append('_token',"{{csrf_token()}}");
            d.append('phone_num',p_num);

            $.ajax({
                type: 'post',
                url: '/validate-phone',
                processData: false,
                contentType: false,
                cache: false,
                data: d,
                error: function(err) {
                    $('#err_message_phone').text('The phone number has already been taken.');
                    $('#submit-btn').hide('slow');

                },
                success: function(response) {
                    $('#err_message_phone').text('');
                    $('#submit-btn').show('slow');

                }
            })
        }


        $('#phone_num').on('input', (e) => {
            validatePhone($('#phone_num').val());
        });


        $('#submit-btn').on('click',(e) => {
            if(!$('#name').val()){
                $('#name_error').text('field required');
                e.preventDefault();
                $('#name').focus();
                $('html, body').animate({
                    scrollTop: "0px"
                }, 800);
                return;
            } else {
                $('#name_error').text('');
            }

            if(!$('#staff_num').val()){
                $('#staff_num_error').text('field required');
                e.preventDefault();
                $('#staff_num').focus();
                $('html, body').animate({
                    scrollTop: "0px"
                }, 800);
                return;
            } else {
                $('#staff_num_error').text('');
            }

            if(!$('#phone_num').val()){
                $('#phone_num_error').text('field required');
                e.preventDefault();
                $('#phone_num').focus();
                $('html, body').animate({
                    scrollTop: "0px"
                }, 800);
                return;
            } else {
                $('#phone_num_error').text('');
            }

            if($('#role').find(':selected').text() == 'select...'){
                $('#user_type_error').text('field required');
                e.preventDefault();
                $('#role').focus();
                $('html, body').animate({
                    scrollTop: "0px"
                }, 800);
                return;
            } else {
                $('#user_type_error').text('');
            }

            if(!$('#d_email').val()){
                $('#email_error').text('field required');
                e.preventDefault();
                $('#d_email').focus();
                return;
            } else {
                $('#email_error').text('');
            }


            if(!$('#id_number').val()){
                $('#id_number_error').text('field required');
                e.preventDefault();
                $('#id_number').focus();
                return;
            } else {
                $('#id_number_error').text('');
            }

            if($('#gender').find(':selected').text() == 'select...'){
                $('#gender_error').text('field required');
                e.preventDefault();
                $('#gender').focus();
                return;
            } else {
                $('#gender_error').text('');
            }

            $('#my-form').submit();
        })
    })
</script>
@endpush