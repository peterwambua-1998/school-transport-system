@extends('layouts.app')
@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />

    <style>
        .img-account-profile {
    height: 10rem;
}
.rounded-circle {
    border-radius: 50% !important;
}
.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgb(33 40 50 / 15%);
}
.card .card-header {
    font-weight: 500;
}
.card-header:first-child {
    border-radius: 0.35rem 0.35rem 0 0;
}
.card-header {
    padding: 1rem 1.35rem;
    margin-bottom: 0;
    background-color: rgba(33, 40, 50, 0.03);
    border-bottom: 1px solid rgba(33, 40, 50, 0.125);
}
.form-control, .dataTable-input {
    display: block;
    width: 100%;
    padding: 0.875rem 1.125rem;
    font-size: 0.875rem;
    font-weight: 400;
    line-height: 1;
    color: #69707a;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #c5ccd6;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    border-radius: 0.35rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.nav-borders .nav-link.active {
    color: #ef8e00;
    border-bottom-color: #ef8e00;
}
.nav-borders .nav-link {
    color: #69707a;
    border-bottom-width: 0.125rem;
    border-bottom-style: solid;
    border-bottom-color: transparent;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    padding-left: 0;
    padding-right: 0;
    margin-left: 1rem;
    margin-right: 1rem;
}
    </style>
@endpush
@section('content')


<nav class="page-breadcrumb" style="display:flex">
    <ol class="breadcrumb" style="width: 85%">
      <li class="breadcrumb-item"><a href="#">Profile</a></li>
    </ol>
  
    <div style="width: 15%">
        <a class="btn btn-success" style="float: right;border-radius:5px" href="{{ route('profile_show', Crypt::encrypt(Auth::user()->id)) }}"><ion-icon style="position: relative; top:3px; right: 5px; color: #fff;font-size:16px;" name="add-circle-outline"></ion-icon> Edit profile</a>
    </div>
</nav>

<div class="container-xl px-4 mt-4">
    <!-- Account page navigation-->
    <nav class="nav nav-borders">
        <a class="nav-link active ms-0" target="__blank">Profile</a>
    </nav>
    <hr class="mt-0 mb-4">
    <div class="row">
        <div class="col-xl-4">
            <!-- Profile picture card-->
            <div class="card mb-4 mb-xl-0">
                <div class="card-header">Profile Picture</div>
                <div class="card-body text-center">
                    @if ($user->image)
                        <!-- Profile picture image-->
                        <img style="width: 120px; height:120px;" class="rounded-circle" src="{{ asset('store/'.$user->image) }}" alt="">
                    @else
                        <!-- Profile picture image-->
                        @if (Auth::user()->gender == 'male')
                        <img class="img-account-profile rounded-circle mb-2" src="{{ url('https://cdn-icons-png.flaticon.com/512/9875/9875255.png') }}" alt="">
                        @else
                        <img class="img-account-profile rounded-circle mb-2" src="{{ url('https://cdn-icons-png.flaticon.com/512/9875/9875392.png') }}" alt="">
                        @endif
                        
                    @endif
                    <h6 class="card-title mt-4">{{Auth::user()->user_type}}</h6>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <!-- Account details card-->
            <div class="card mb-4">
                <div class="card-header">Account Details</div>
                <div class="card-body">
                    <form>
                        
                        <!-- Form Row-->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (first name)-->
                            <div class="col-md-12">
                                <label class="small mb-1" for="inputFirstName">Full name</label>
                                <input readonly class="form-control" id="inputFirstName" type="text" placeholder="Enter your first name" value="{{$user->name}}">
                            </div>
                            
                        </div>
                        <!-- Form Row -->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (organization name)-->
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputOrgName">Phone Number</label>
                                <input readonly class="form-control" id="inputOrgName" type="text" placeholder="Enter your organization name" value="{{$user->phone_num}}">
                            </div>
                            <!-- Form Group (location)-->
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputEmailAddress">Email address</label>
                                <input readonly class="form-control" id="inputEmailAddress" type="email" placeholder="Enter your email address" value="{{$user->email}}">
                            </div>
                        </div>
                        
                        <!-- Form Row-->
                        <div class="row gx-3 mb-3">
                            <!-- Form Group (phone number)-->
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputPhone">ID number</label>
                                <input readonly class="form-control" id="inputPhone" type="tel" placeholder="Enter your phone number" value="{{$user->id_num}}">
                            </div>
                            <!-- Form Group (birthday)-->
                            <div class="col-md-6">
                                <label class="small mb-1" for="inputBirthday">Staff number</label>
                                <input readonly class="form-control" id="inputBirthday" type="text" name="birthday" placeholder="Enter your birthday" value="{{$user->staff_num}}">
                            </div>
                        </div>
                        <!-- Save changes button-->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

