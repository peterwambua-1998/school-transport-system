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
      <li class="breadcrumb-item"><a href="{{route('routes.index')}}">Staff</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
                <h4 class="card-title">Edit Staff</h4>
                <hr>
                <form id="my-form" action="{{ route('staff_update', $staff->id) }}"  method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="title">Full Name</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Enter Staff Name" value="{{ old('name', $staff->name) }}" required>
                            <span class="issue" id="name_error"></span>
                        </div>
                        
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="title">Staff Number</label>
                            <input type="text" name="staff_num" class="form-control" id="staff_num" placeholder="Enter Staff Number" value="{{ old('name', $staff->staff_num) ?? 'not provided' }}" required>
                            <span id="err_message_staff" class="text-danger mt-1" style="font-size:13px"></span>
                            <span class="issue" id="staff_num_error"></span>
                        </div>

                    </div>


                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">Phone Number</label>
                            <input type="text" name="phone_num" class="form-control" id="phone_num" placeholder="Staff phone number" value="{{ old('name', $staff->phone_num) ?? 'not provided' }}" required>
                            <span id="err_message_phone" class="text-danger mt-1" style="font-size:13px"></span>
                            <span class="issue" id="phone_num_error"></span>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="inputState">Select Role</label>
                            
                            <select id="role" class="form-select role" name="user_type" required onchange="getval(this);">
                                <option>select...</option>
                                @foreach ($roles as $role)
                                    <option @if ($staff->user_type ==  $role->role) selected   @endif value="{{$role->role}}">{{$role->role_name}}</option>
                                @endforeach
                                
                            </select>
                            <span class="issue" id="user_type_error"></span>

                        </div>

                       

    
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">Email</label>
                            <input type="email" name="email" class="form-control" id="d_email" placeholder="example@mail.com" value="{{ old('email', $staff->email) }}" required>
                            <span id="err_message" class="text-danger mt-1" style="font-size:13px"></span>
                            <span class="issue" id="email_error"></span>
                        </div>
                        
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="platenum">ID Number</label>
                            <input type="text" name="id_number" class="form-control" id="id_number" placeholder="ID Number" required value="{{ $staff->id_num ?? 'not provided' }}">
                            <span id="err_message_id" class="text-danger mt-1" style="font-size:13px"></span>
                            <span class="issue" id="id_number_error"></span>
                        </div>
                       
                    </div>

                    <div class="row">
                        

                        <div class="col-md-6">
                            <label class="form-label" for="">Gender</label>
                            <select class="form-select" name="gender" id="gender">
                                <option>select...</option>
                                <option @if ($staff->gender == "male")
                                    selected
                                @endif value="male">Male</option>
                                <option @if ($staff->gender == "female")
                                    selected
                                @endif value="female">Female</option>
                            </select>
                            <span class="issue" id="gender_error"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center" style="height: 100%">
                                @if ($staff->image)
                                    <img class="wd-250 ht-250 mt-5" style="border-radius: 0.25rem;" src="{{ asset('store/'.$staff->image) }}" alt="photo" >
                                @else
                                @if ($staff->gender == "male")
                                    <img class="wd-150 ht-150 mt-5 rounded-circle" src="{{url('https://cdn-icons-png.flaticon.com/512/9875/9875255.png')}}" alt="staff">
                                @else
                                    <img class="wd-150 ht-150 mt-5 rounded-circle" src="{{url('https://cdn-icons-png.flaticon.com/512/9875/9875392.png')}}" alt="staff">
                                @endif
                                @endif
                            </div>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="exampleFormControlFile1">Photo</label>
                            <br>
                            <input type="file" class="form-control-file" id="myDropify" name="image">
                        </div>
                        
                    </div>
        
                    <div class="text-center">
                        <button id="submit-btn" type="button" class="btn btn-success mt-3"><ion-icon style="position: relative; top:2px; right: 5px; color: #fff;" name="save"></ion-icon> Save Changes</button>
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
<script src="{{ asset('assets/js/dropify.js') }}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer>
    $(function() {
        $('#submit-btn').on('click',(e) => {
            if(!$('#name').val()){
                $('#name_error').text('field required');
                e.preventDefault();
                $('#name').focus();
                return;
            } else {
                $('#name_error').text('');
            }

            if(!$('#staff_num').val()){
                $('#staff_num_error').text('field required');
                e.preventDefault();
                $('#staff_num').focus();
                return;
            } else {
                $('#staff_num_error').text('');
            }

            if(!$('#phone_num').val()){
                $('#phone_num_error').text('field required');
                e.preventDefault();
                $('#phone_num').focus();
                return;
            } else {
                $('#phone_num_error').text('');
            }

            if($('#role').find(':selected').text() == 'select...'){
                $('#user_type_error').text('field required');
                e.preventDefault();
                $('#role').focus();
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
